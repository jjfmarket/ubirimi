<?php

use Ubirimi\Util;

require_once __DIR__ . '/_header.php';
?>

<div style="font: Trebuchet MS, sans-serif; white-space: nowrap; padding-top: 5px;text-align: left;padding-left: 2px;">
    Hello <?php echo $first_name ?> <?php echo $last_name ?>
    <br />
    <br />
    You have a new password for your <strong><?php echo $repoName ?></strong> SVN Repository.
    <br />
    Bellow you will find the information to access the repository
    <br />
    <br />
    Repository name: <?php echo $repoName ?>
    <br />
    Repository URL: <?php echo $baseURL . '/svn/' . Util::slugify($clientId) . '/' . Util::slugify($repoName) . '/trunk' ?>
    <br />
    username: <?php echo $username ?>
    <br />
    <?php if (null !== $password): ?>
    password: <?php echo $password ?>
    <?php endif ?>
    <br />
    <br />
    The password and access rights (read/write) for the repository can be changed once you are logged in.
</div>

<?php require_once __DIR__ . '/_footer.php' ?>