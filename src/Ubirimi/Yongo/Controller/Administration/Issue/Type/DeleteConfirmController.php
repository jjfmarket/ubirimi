<?php

namespace Ubirimi\Yongo\Controller\Administration\Issue\Type;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Type;

class DeleteConfirmController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $Id = $request->get('id');

        $types = Type::getAll($session->get('client/id'));

        $issueQueryParameters = array(
            'type' => $Id,
            'client_id' => $session->get('client/id')
        );

        $issuesResult = $this->getRepository('yongo.issue.issue')->getByParameters($issueQueryParameters);
        $issuesCount = null;

        if (null != $issuesResult) {
            $issuesCount = $issuesResult->num_rows;
        }

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/issue/type/DeleteConfirm.php', get_defined_vars());
    }
}
