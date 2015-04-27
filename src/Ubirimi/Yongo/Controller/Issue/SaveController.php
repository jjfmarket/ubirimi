<?php

/*
 *  Copyright (C) 2012-2014 SC Ubirimi SRL <info-copyright@ubirimi.com>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */

namespace Ubirimi\Yongo\Controller\Issue;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Email\Email;
use Ubirimi\Repository\General\UbirimiClient;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\Field;
use Ubirimi\Yongo\Repository\Issue\IssueEvent;
use Ubirimi\Yongo\Repository\Project\YongoProject;
use Ubirimi\Yongo\Repository\Workflow\Workflow;
use Ubirimi\Yongo\Repository\Workflow\WorkflowFunction;

class SaveController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $clientId = $session->get('client/id');
        $clientSettings = $this->getRepository(UbirimiClient::class)->getSettings($clientId);

        $timeTrackingDefaultUnit = $session->get('yongo/settings/time_tracking_default_unit');

        $projectId = $request->request->get('project_id');
        $issueId = $request->request->get('issue_id');
        $attachIdsToBeKept = $request->request->get('attach_ids');

        $fieldTypes = $request->request->get('field_types');
        $fieldValues = $request->request->get('field_values');

        $fieldTypesCustom = $request->request->get('field_types_custom');
        $fieldValuesCustom = $request->request->get('field_values_custom');

        if (!is_array($attachIdsToBeKept)) {
            $attachIdsToBeKept = array();
        }

        $issueSystemFieldsData = array();
        $issueCustomFieldsData = array();

        for ($i = 0; $i < count($fieldTypes); $i++) {
            if ($fieldValues[$i] != 'null' && $fieldValues[$i] != '') {
                $issueSystemFieldsData[$fieldTypes[$i]] = $fieldValues[$i];
            }
            else {
                $issueSystemFieldsData[$fieldTypes[$i]] = null;
            }
        }

        for ($i = 0; $i < count($fieldTypesCustom); $i++) {
            if ($fieldValuesCustom[$i] != 'null' && $fieldValuesCustom[$i] != '') {
                $issueCustomFieldsData[$fieldTypesCustom[$i]] = $fieldValuesCustom[$i];
            } else {
                $issueCustomFieldsData[$fieldTypesCustom[$i]] = null;
            }
        }

        if (!$projectId) {
            $projectId = $issueSystemFieldsData['project'];
        }

        $project = $this->getRepository(YongoProject::class)->getById($projectId);

        if (array_key_exists(Field::FIELD_ASSIGNEE_CODE, $issueSystemFieldsData)) {
            // assignee field is placed on screen
            if ($issueSystemFieldsData[Field::FIELD_ASSIGNEE_CODE] == -1) {
                $issueSystemFieldsData[Field::FIELD_ASSIGNEE_CODE] = null;
            }
        } else {
            // put the assignee as the project default assignee
            $issueSystemFieldsData[Field::FIELD_ASSIGNEE_CODE] = $project['lead_id'];
        }

        $issueSystemFieldsData['helpdesk_flag'] = 0;
        if ($session->get("selected_product_id") == SystemProduct::SYS_PRODUCT_HELP_DESK) {
            $issueSystemFieldsData['helpdesk_flag'] = 1;
        }
        $issueSystemFieldsData['user_reported_ip'] = Util::getClientIP();

        $issue = UbirimiContainer::get()['issue']->save(
            $project,
            $issueSystemFieldsData,
            $issueId,
            $timeTrackingDefaultUnit,
            $projectId,
            $issueCustomFieldsData,
            $attachIdsToBeKept,
            $clientSettings,
            $session->get('user/id'),
            $clientId
        );

        $workflowUsed = UbirimiContainer::get()['repository']->get(YongoProject::class)->getWorkflowUsedForType($projectId, $issue['issue_type_id']);
        $creationData = UbirimiContainer::get()['repository']->get(Workflow::class)->getDataForCreation($workflowUsed['id']);
        $eventData = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_CREATED_CODE);
        $hasNotificationEvent = UbirimiContainer::get()['repository']->get(WorkflowFunction::class)->hasEvent($creationData['id'], 'event=' . $eventData['id']);

        if ($hasNotificationEvent) {
            UbirimiContainer::get()['issue.email']->emailIssueCreate($clientId, $issue, $project, $session->get('user/id'));
        }

        // clean the search information
        $session->remove('array_ids');
        $session->remove('last_search_parameters');

        $this->getLogger()->addInfo('ADD Yongo issue ' . $project['code'] . '-' . $issue['nr'], $this->getLoggerContext());

        return new Response('New Issue Created <a href="/yongo/issue/' . $issue['id'] . '">' . $project['code'] . '-' . $issue['nr'] . '</a>');
    }
}