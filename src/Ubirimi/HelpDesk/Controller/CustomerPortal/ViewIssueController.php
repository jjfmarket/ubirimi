<?php

namespace Ubirimi\HelpDesk\Controller\CustomerPortal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\Field;
use Ubirimi\Yongo\Repository\Issue\Attachment;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Issue\SystemOperation;
use Ubirimi\Yongo\Repository\Issue\Watcher;

class ViewIssueController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        $issueId = $request->get('id');

        Util::checkUserIsLoggedInAndRedirect();

        $issue = UbirimiContainer::getRepository('yongo.issue.issue')->getById($issueId, $session->get('user/id'));
        $issueId = $issue['id'];
        $projectId = $issue['issue_project_id'];
        $clientSettings = $session->get('client/settings');

        $sectionPageTitle = $clientSettings['title_name']
            . ' / ' . SystemProduct::SYS_PRODUCT_HELP_DESK_NAME
            . ' / ' . $issue['project_code'] . '-'
            . $issue['nr'] . ' ' . $issue['summary'];

        $session->set('selected_project_id', $projectId);
        $issueProject = $this->getRepository('yongo.project.project')->getById($projectId);

        /* before going further, check to is if the issue id a valid id -- start */
        $issueValid = true;
        if (!$issue || $session->get('client/id') != $issueProject['client_id']) {
            $issueValid = false;
        }

        /* before going further, check to is if the issue id a valid id -- end */

        $components = $this->getRepository('yongo.issue.component')->getByIssueIdAndProjectId($issueId, $projectId);
        $versionsAffected = $this->getRepository('yongo.issue.version')->getByIssueIdAndProjectId(
            $issueId,
            $projectId,
            Issue::ISSUE_AFFECTED_VERSION_FLAG
        );

        $versionsTargeted = $this->getRepository('yongo.issue.version')->getByIssueIdAndProjectId(
            $issueId,
            $projectId,
            Issue::ISSUE_FIX_VERSION_FLAG
        );

        $arrayListResultIds = null;
        if ($session->has('array_ids')) {
            $arrayListResultIds = $session->get('array_ids');
            $index = array_search($issueId, $arrayListResultIds);
        }

        $workflowUsed = $this->getRepository('yongo.project.project')->getWorkflowUsedForType($projectId, $issue[Field::FIELD_ISSUE_TYPE_CODE]);

        $step = $this->getRepository('yongo.workflow.workflow')->getStepByWorkflowIdAndStatusId($workflowUsed['id'], $issue[Field::FIELD_STATUS_CODE]);
        $stepProperties = $this->getRepository('yongo.workflow.workflow')->getStepProperties($step['id'], 'array');

        if ($issueValid) {

            $workflowActions = $this->getRepository('yongo.workflow.workflow')->getTransitionsForStepId($workflowUsed['id'], $step['id']);
            $screenData = $this->getRepository('yongo.project.project')->getScreenData(
                $issueProject,
                $issue[Field::FIELD_ISSUE_TYPE_CODE],
                SystemOperation::OPERATION_CREATE,
                'array'
            );

            $customFieldsData = $this->getRepository('yongo.issue.customField')->getCustomFieldsData($issue['id']);

            $attachments = Attachment::getByIssueId($issue['id'], true);
            $countAttachments = count($attachments);

            $atLeastOneSLA = false;
            $slasPrintData = $this->getRepository('yongo.issue.issue')->updateSLAValue($issue, $session->get('client/id'), $clientSettings);

            foreach ($slasPrintData as $slaData) {
                if ($slaData['goal']) {
                    $atLeastOneSLA = true;
                    break;
                }
            }
            $watchers = Watcher::getByIssueId($issueId);
            $timeTrackingFlag = $session->get('yongo/settings/time_tracking_flag');

            $customFieldsData = $this->getRepository('yongo.issue.customField')->getCustomFieldsData($issue['id']);
            $customFieldsDataUserPickerMultipleUser = $this->getRepository('yongo.issue.customField')->getUserPickerData($issue['id']);
        }

        $menuSelectedCategory = 'issue';
        $hasEditPermission = true;
        $issueEditableProperty = true;
        $hasAssignPermission = false;
        $hasAddCommentsPermission = true;
        $hasDeletePermission = true;
        $childrenIssues = null;
        $linkedIssues = null;
        $linkIssueTypes = null;
        $hasCreateAttachmentPermission = true;
        $hasDeleteAllAttachmentsPermission = false;
        $hasDeleteOwnAttachmentsPermission = true;
        $hasLinkIssuePermission = false;
        $parentIssue = null;

        $menuSelectedCategory = 'home';
        $showWorkflowMenu = false;

        return $this->render(__DIR__ . '/../../Resources/views/customer_portal/ViewIssue.php', get_defined_vars());
    }
}
