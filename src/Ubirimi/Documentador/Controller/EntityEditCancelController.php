<?php

namespace Ubirimi\Documentador\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Documentador\Repository\Space\Space;
use Ubirimi\Documentador\Repository\Entity\Entity;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class EntityEditCancelController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $loggedInUserId = $session->get('user/id');
        $session->set('selected_product_id', SystemProduct::SYS_PRODUCT_DOCUMENTADOR);

        $spaceId = $_GET['space_id'];
        $entityId = $_GET['entity_id'];

        $this->getRepository('documentador.entity.entity')->deleteAllSnapshotsByEntityIdAndUserId($entityId, $loggedInUserId);

        header('Location: /documentador/pages/' . $spaceId);
    }
}

