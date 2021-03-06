<?php

/*
 *  Copyright (C) 2012-2015 SC Ubirimi SRL <info-copyright@ubirimi.com>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */

namespace Ubirimi\Documentador\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Documentador\Repository\Entity\Entity;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class UploadController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $clientId = $session->get('client/id');
        $loggedInUserId = $session->get('user/id');

        $entityId = $request->get('id');
        $currentDate = Util::getServerCurrentDateTime();
        $pathBaseAttachments = Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_DOCUMENTADOR, 'filelists');
        $index = 0;

        if (isset($_FILES['entity_upload_file'])) {
            foreach ($_FILES['entity_upload_file']['name'] as $filename) {
                if (!empty($filename)) {
                    // check if this file already exists
                    $fileExists = $this->getRepository(Entity::class)->getFileByName($entityId, $filename);

                    if ($fileExists) {
                        // get the last revision and increment it by one
                        $fileId = $fileExists['id'];
                        $revisions = $this->getRepository(Entity::class)->getRevisionsByFileId($fileId);
                        $revisionNumber = $revisions->num_rows + 1;

                        // create the revision folder
                        if (!file_exists($pathBaseAttachments . $entityId)) {
                            mkdir($pathBaseAttachments . $entityId);
                        }
                        if (!file_exists($pathBaseAttachments . $entityId . '/' . $fileId)) {
                            mkdir($pathBaseAttachments . $entityId . '/' . $fileId);
                        }
                        if (!file_exists($pathBaseAttachments . $entityId . '/' . $fileId . '/' . $revisionNumber)) {
                            mkdir($pathBaseAttachments . $entityId . '/' . $fileId . '/' . $revisionNumber);
                        }

                    } else {
                        // add the file to the list of files
                        $fileId = $this->getRepository(Entity::class)->addFile($entityId, $filename, $currentDate);

                        $this->getLogger()->addInfo('ADD Documentador entity file ' . $filename, $this->getLoggerContext());

                        $revisionNumber = 1;

                        // create the folder for the file
                        mkdir($pathBaseAttachments . $entityId . '/' . $fileId);

                        // create the folder for the first revision
                        mkdir($pathBaseAttachments . $entityId . '/' . $fileId . '/' . $revisionNumber);
                    }

                    // add revision to the file

                    $this->getRepository(Entity::class)->addFileRevision($fileId, $loggedInUserId, $currentDate);

                    if ($revisionNumber > 1) {
                        $this->getLogger()->addInfo('ADD Documentador entity file revision to ' . $filename, $this->getLoggerContext());
                    }
                    $baseFileName = pathinfo($filename, PATHINFO_FILENAME);
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);

                    move_uploaded_file($_FILES["entity_upload_file"]["tmp_name"][$index], $pathBaseAttachments . $entityId . '/' . $fileId . '/' . $revisionNumber . '/' . $baseFileName . '.' . $extension);
                    $index++;
                }
            }
        }

        return new RedirectResponse('/documentador/page/view/' . $entityId);
    }
}