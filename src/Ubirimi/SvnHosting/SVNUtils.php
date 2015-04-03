<?php

namespace Ubirimi\SvnHosting;

use Exception;
use Ubirimi\ConsoleUtils;

/**
 * Usefull static method to manipulate an svn repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: SVNUtils.php 1536 2008-11-01 16:08:37Z duponc_j $
 */

class SVNUtils {
    public static $hooks = array('post-commit', 'post-unlock', 'pre-revprop-change', 'post-lock', 'pre-commit', 'pre-unlock', 'post-revprop-change', 'pre-lock', 'start-commit');

    /**
     * It's for use with testunit. This method simulate svnadmin create $path
     *
     * @param string Path to create directory structs
     */
    public static function createSvnDirectoryStruct($path) {
        @mkdir($path);
        @mkdir($path . "/hooks");
        @mkdir($path . "/locks");
        @mkdir($path . "/conf");
        @mkdir($path . "/dav");
        @mkdir($path . "/db");
    }

    /**
     * Get the command svn
     *
     * @param string Parameters
     */
    public static function svnCommand($cmd) {
        return "svn --config-dir /UBR/fake $cmd";
    }

    /**
     * Get the command svnadmin
     *
     * @param string Parameters
     */
    public static function svnadminCommand($cmd) {
        return "svnadmin --config-dir /UBR/fake $cmd";
    }

    /**
     * Import file into subversion repository
     *
     * @param string path to server repository
     * @param string path to directory to import
     */
    private static function _svnImport($server, $local) {
        $server = SVNUtils::getRepositoryPath($server);
        $local = escapeshellarg($local);
        $cmd = SVNUtils::svnCommand("import --non-interactive --username Ubirimi -m \"" . "Commit by Ubirimi" . "\" $local $server");
        $message = ConsoleUtils::runCmdCaptureMessage($cmd, $return);
        if ($return) {
            throw new Exception("Can't import into subversion repository.\nCommand:\n" . $cmd . "\n\nError:\n" . $message);
        }
    }

    /**
     * Create SVN repository with standard organisation
     * /trunk
     * /tags
     * /branches
     *
     * @param string Path to create subversion
     */
    public static function createSvn($path) {
        $escape_path = escapeshellarg($path);
        $message = ConsoleUtils::runCmdCaptureMessage(SVNUtils::svnadminCommand("create $escape_path"), $return);
        if ($return) {
            throw new Exception("Can't create subversion repository: " . $message);
        }
    }

    static public function removeDirectory($remove_path) {
        if (!file_exists($remove_path)) {
            return;
        }
        if (($path = realpath($remove_path)) !== FALSE) {
            if (@chmod($path, 0777) === FALSE) {
                throw new Exception(sprintf("Can't delete directory %s. Permission denied.", $path));
            }
            try {
                if (is_dir($path)) {
                    $dh = opendir($path);
                } else {
                    return;
                }
            } catch (Exception $e) {
                return;
            }
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                        SVNUtils::removeDirectory($path . DIRECTORY_SEPARATOR . $file);
                    } else {
                        if (chmod($path . DIRECTORY_SEPARATOR . $file, 0777) === FALSE) {
                            throw new Exception(sprintf("Can't delete file %s.", $path . DIRECTORY_SEPARATOR . $file));
                        }
                        unlink($path . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
            closedir($dh);
            if (@rmdir($path) === FALSE) {
                throw new Exception(sprintf("Can't delete directory %s.", $path));
            }
        }
    }

    /**
     * Create and return a tmp directory
     *
     * @return string Path to tmp directory
     */
    static public function getTmpDirectory() {
        $path = tempnam("", "UBR_");
        unlink($path);
        mkdir($path);
        return $path;
    }

    /**
     * Create standard svn directories
     * /trunk
     * /tags
     * /branches
     *
     * @param string Path to create subversion
     */
    public static function createStandardDirectories($path) {
        $tmpdir = SVNUtils::getTmpDirectory();
        try {
            mkdir($tmpdir . DIRECTORY_SEPARATOR . "trunk");
            mkdir($tmpdir . DIRECTORY_SEPARATOR . "branches");
            mkdir($tmpdir . DIRECTORY_SEPARATOR . "tags");
            SVNUtils::_svnImport($path, $tmpdir);
        } catch (Exception $e) {
            SVNUtils::removeDirectory($path);
            SVNUtils::removeDirectory($tmpdir);
            throw $e;
        }
        SVNUtils::removeDirectory($tmpdir);
    }

    /**
     * This code work only for directory
     * Directory separator need to be /
     */
    private static function getCannocialPath($path) {
        $origpath = $path;
        $path = preg_replace('#//+#', '/', $path);
        $list_path = preg_split('#/#', $path, -1, PREG_SPLIT_NO_EMPTY);
        $i = 0;
        while (isset($list_path[$i])) {
            if ($list_path[$i] == '..') {
                unset($list_path[$i]);
                if ($i > 0) {
                    unset($list_path[$i - 1]);
                }
                $list_path = array_values($list_path);
                $i = 0;
            } elseif ($list_path[$i] == '.') {
                unset($list_path[$i]);
                $list_path = array_values($list_path);
                $i = 0;
            } else {
                $i++;
            }
        }
        $newpath = '';
        $first = true;
        foreach ($list_path as $path) {
            if (!$first) {
                $newpath .= '/';
            } else {
                $first = false;
            }
            $newpath .= $path;
        }
        if ($origpath[0] == '/') {
            return '/' . $newpath;
        }
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            return $newpath;
        } else {
            return getcwd() . '/' . $newpath;
        }
    }

    /**
     * Return clean version of a Subversion repository path betwenn ' and with file:// before
     *
     * @param string Path to repository
     * @return string absolute path to repository
     */
    public static function getRepositoryPath($path) {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $newpath = realpath($path);
            if ($newpath === FALSE) {
                $path = str_replace('//', '/', str_replace('\\', '/', $path));
                $path = SVNUtils::getCannocialPath($path);
            } else {
                $path = $newpath;
            }
            return "\"file:///" . str_replace('\\', '/', $path) . "\"";
        }
        $newpath = realpath($path);
        if ($newpath === FALSE) {
            $newpath = SVNUtils::getCannocialPath($path);
        }
        return escapeshellarg('file://' . $newpath);
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/repos/v/function.copyr.php
     * @param       string $source    Source path
     * @param       string $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    static public function copyr($source, $dest) {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }
        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }
        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }
        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            // Deep copy directories
            SVNUtils::copyr("$source/$entry", "$dest/$entry");
        }
        // Clean up
        $dir->close();
        return true;
    }
}
