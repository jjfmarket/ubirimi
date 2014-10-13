<?php

namespace Ubirimi\General\Controller\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\UbirimiController;
use Ubirimi\Repository\User\User;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Issue;

class DeleteConfirmController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $userId = $request->get('user_id');
        $delete_own_user = false;
        if ($userId == $session->get('user/id'))
            $delete_own_user = true;

        if (!$delete_own_user) {
            $issues_reported_count = 0;
            $issues_assigned_count = 0;

            $user = $this->getRepository('ubirimi.user.user')->getById($userId);
            $issuesReported = UbirimiContainer::getRepository('yongo.issue.issue')->getByParameters(array('reporter' => $userId));
            $issuesAssigned = UbirimiContainer::getRepository('yongo.issue.issue')->getByParameters(array('assignee' => $userId));

            if (null != $issuesReported)
                $issues_reported_count = $issuesReported->num_rows;

            if (null != $issuesAssigned)
                $issues_assigned_count = $issuesAssigned->num_rows;
        }

        return $this->render(__DIR__ . '/../../Resources/views/user/DeleteConfirm.php', get_defined_vars());
    }
}
