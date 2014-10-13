<?php
    use Ubirimi\SystemProduct;
    use Ubirimi\Util;
    use Ubirimi\Yongo\Repository\Workflow\Workflow;

    Util::checkUserIsLoggedInAndRedirect();

    $stepId = $_GET['id'];

    $step = $this->getRepository('yongo.workflow.workflow')->getStepById($stepId);
    $workflowId = $step['workflow_id'];
    $workflow = $this->getRepository('yongo.workflow.workflow')->getMetaDataById($workflowId);

    if ($workflow['client_id'] != $clientId) {
        header('Location: /general-settings/bad-link-access-denied');
        die();
    }

    $menuSelectedCategory = 'issue';

    $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Workflow Step Browser';

    require_once __DIR__ . '/../../../../Resources/views/administration/workflow/step/ViewBrowser.php';