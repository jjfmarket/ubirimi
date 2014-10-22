<?php

namespace Ubirimi\Yongo\Controller\Report;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class SaveConfirmController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $filterId = $request->get('filter_id');

        $filter = null;
        if ($filterId != -1) {
            $filter = $this->getRepository('yongo.issue.filter')->getById($filterId);
        }

        return $this->render(__DIR__ . '/../../Resources/views/filter/SaveConfirm.php', get_defined_vars());
    }
}
