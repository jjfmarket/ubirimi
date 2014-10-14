<?php

namespace Ubirimi\Yongo\Controller\Issue;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Email\Email;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Workflow\WorkflowFunction;

class SaveIssueTransitionNoScreenController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {

        Util::checkUserIsLoggedInAndRedirect();

        $clientId = UbirimiContainer::get()['session']->get('client/id');
        $loggedInUserId = UbirimiContainer::get()['session']->get('user/id');

        $workflowStepIdFrom = $_POST['step_id_from'];
        $workflowStepIdTo = $_POST['step_id_to'];
        $workflowId = $_POST['workflow_id'];
        $issueId = $_POST['issue_id'];

        $clientSettings = $this->getRepository('ubirimi.general.client')->getSettings($clientId);

        $workflowData = $this->getRepository('yongo.workflow.workflow')->getDataByStepIdFromAndStepIdTo($workflowId, $workflowStepIdFrom, $workflowStepIdTo);
        $issue = $this->getRepository('yongo.issue.issue')->getByParameters(array('issue_id' => $issueId), $loggedInUserId);

        $canBeExecuted = $this->getRepository('yongo.workflow.workflow')->checkConditionsByTransitionId($workflowData['id'], $loggedInUserId, $issue);

        if ($canBeExecuted) {

            $smtpSettings = $session->get('client/settings/smtp');
            if ($smtpSettings) {
                Email::$smtpSettings = $smtpSettings;
            }

            $date = Util::getServerCurrentDateTime();
            WorkflowFunction::triggerPostFunctions($clientId, $issue, $workflowData, array(), $loggedInUserId, $date);

            // update the date_updated field
            Issue::updateById($issueId, array('date_updated' => $date), $date);

            return new Response('success');

        } else {
            return new Response('can_not_be_executed');
        }
    }
}