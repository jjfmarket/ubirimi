<?php

namespace Ubirimi\Documentador\Controller\Page;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Documentador\Repository\Space\Space;
use Ubirimi\Documentador\Repository\Entity\Entity;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class AddEntityController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $loggedInUserId = $session->get('user/id');

        $name = $_POST['name'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $parentId = $_POST['parent_id'];
        $spaceId = $_POST['space_id'];
        if ($type == 'file_list')
            $pageType = EntityType::ENTITY_FILE_LIST;
        else
            $pageType = EntityType::ENTITY_BLANK_PAGE;

        if ($parentId == -1) {
            // set the parent to the home page of the space if it exists
            $space = $this->getRepository('documentador.space.space')->getById($spaceId);
            $homeEntityId = $space['home_entity_id'];
            if ($homeEntityId) {
                $parentId = $homeEntityId;
            } else {
                $parentId = null;
            }
        }

        $page = new Entity($pageType, $spaceId, $loggedInUserId, $parentId, $name, $description);
        $currentDate = Util::getServerCurrentDateTime();
        $pageId = $page->save($currentDate);

        // if the page is a file list create the folders
        $baseFilePath = Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_DOCUMENTADOR, 'filelists');
        if ($pageType == EntityType::ENTITY_FILE_LIST) {
            mkdir($baseFilePath . $pageId);
        }

        return new Response($pageId);
    }
}