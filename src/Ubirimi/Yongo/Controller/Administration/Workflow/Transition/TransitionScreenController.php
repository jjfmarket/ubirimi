<?php

namespace Ubirimi\Yongo\Controller\Administration\Workflow\Transition;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class TransitionScreenController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $clientId = $session->get('client/id');

        $stepIdFrom = $request->get('id_from');
        $stepIdTo = $request->get('id_to');
        $workflowId = $request->get('workflow_id');

        $workflowMetadata = $this->getRepository('yongo.workflow.workflow')->getMetaDataById($workflowId);

        $workflowData = $this->getRepository('yongo.workflow.workflow')->getDataByStepIdFromAndStepIdTo($workflowId, $stepIdFrom, $stepIdTo);
        $transitionName = $workflowData['transition_name'];
        $screens = $this->getRepository('yongo.screen.screen')->getAll($clientId);
        $initialStep = $this->getRepository('yongo.workflow.workflow')->getInitialStep($workflowId);

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/workflow/transition/TransitionScreen.php', get_defined_vars());
    }
}