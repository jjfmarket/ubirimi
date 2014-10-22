<?php

namespace Ubirimi\Yongo\Controller\Administration\Project;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class ViewSummaryController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $projectId = $request->get('id');
        $project = $this->getRepository('yongo.project.project')->getById($projectId);

        if ($project['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $menuSelectedCategory = 'project';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / ' . $project['name'];

        $issueTypeScheme = $this->getRepository('yongo.issue.typeScheme')->getMetaDataById($project['issue_type_scheme_id']);
        $issueTypeSchemeData = $this->getRepository('yongo.issue.typeScheme')->getDataById($project['issue_type_scheme_id']);

        $workflowScheme = $this->getRepository('yongo.project.project')->getWorkflowScheme($projectId);

        $workflows = $this->getRepository('yongo.workflow.scheme')->getWorkflows($workflowScheme['id']);

        return $this->render(__DIR__ . '/../../../Resources/views/administration/project/ViewSummary.php', get_defined_vars());
    }
}