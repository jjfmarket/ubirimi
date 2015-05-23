<?php

/*
 *  Copyright (C) 2012-2015 SC Ubirimi SRL <info-copyright@ubirimi.com>
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

namespace Ubirimi\Yongo\Repository\Workflow;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Yongo\Repository\Field\Field;
use Ubirimi\Yongo\Repository\Issue\IssueSettings;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class Workflow
{
    public function updateMetaDataById($Id, $name, $description, $workflowIssueTypeSchemeId, $date) {
        $q = 'update workflow set name = ?, description = ?, issue_type_scheme_id = ?, date_updated = ? ' .
            'where id = ? ' .
            'limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("ssisi", $name, $description, $workflowIssueTypeSchemeId, $date, $Id);
        $stmt->execute();
    }

    public function getByClientId($clientId) {
        $query = "select yongo_workflow.id, yongo_workflow.name, yongo_workflow.description, yongo_workflow_scheme.name as scheme_name, yongo_issue_type_scheme.name as issue_type_scheme_name " .
                 "from yongo_workflow " .
                 "left join yongo_workflow_scheme_data on yongo_workflow_scheme_data.workflow_id = yongo_workflow.id " .
                 "left join yongo_workflow_scheme on yongo_workflow_scheme.id = yongo_workflow_scheme_data.workflow_scheme_id " .
                 "left join yongo_issue_type_scheme on yongo_issue_type_scheme.id = yongo_workflow.issue_type_scheme_id " .
                 "where yongo_workflow.client_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function updateDataById($workflowDataId, $transitionName, $transitionDescription, $screenId, $workflowStepIdTo) {
        $q = 'update yongo_workflow_data set screen_id = ?, transition_name = ?, ' .
                'transition_description = ?, workflow_step_id_to = ? ' .
                'where id = ? ' .
                'limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("issii", $screenId, $transitionName, $transitionDescription, $workflowStepIdTo, $workflowDataId);
        $stmt->execute();
    }

    public function updateTransitionData($workflowId, $transition_name, $screenId, $idFrom, $idTo) {
        $q = 'update yongo_workflow_data set screen_id = ?, transition_name = ? ' .
             'where workflow_id = ? and workflow_step_id_from = ? and workflow_step_id_to = ? ' .
             'limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("isiii", $screenId, $transition_name, $workflowId, $idFrom, $idTo);
        $stmt->execute();
    }

    public function getStepsForStatus($workflowId, $StatusId) {
        $query = "select yongo_workflow_data.id, " .
            "yongo_workflow_data.transition_name, yongo_workflow_data.screen_id " .
            "from yongo_workflow " .
            "left join yongo_workflow_data on yongo_workflow_data.workflow_id = yongo_workflow.id " .
            "where yongo_workflow.id = ? and yongo_workflow_data.issue_status_from_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $workflowId, $StatusId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getDataByStepIdFromAndStepIdTo($workflowId, $IdFrom, $IdTo) {
        $query = "select yongo_workflow_data.* " .
                "from yongo_workflow_data " .
                "where yongo_workflow_data.workflow_id = ? and workflow_step_id_from = ? and workflow_step_id_to = ? " .
                "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("iii", $workflowId, $IdFrom, $IdTo);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function createNewMetaData($clientId, $workflowIssueTypeSchemeId, $name, $description, $currentDate) {
        $q = 'insert into yongo_workflow(client_id, issue_type_scheme_id, name, description, date_created) ' .
             'values(?, ?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("iisss", $clientId, $workflowIssueTypeSchemeId, $name, $description, $currentDate);

        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function deleteRecord($workflowId, $idFrom, $idTo) {
        $q = 'delete from yongo_workflow_data where workflow_id = ? and workflow_step_id_from = ? and workflow_step_id_to = ? limit 1 ';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("iii", $workflowId, $idFrom, $idTo);
        $stmt->execute();
    }

    public function createNewSingleDataRecord($projectWorkflowId, $idFrom, $idTo, $name) {
        $q = 'insert into yongo_workflow_data (workflow_id, workflow_step_id_from, workflow_step_id_to, transition_name) values(?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("iiis", $projectWorkflowId, $idFrom, $idTo, $name);
        $stmt->execute();
    }

    public function deleteById($Id) {

        $arrWorkflowDataIds = array();
        $query = "select id " .
            "from yongo_workflow_data " .
            "where workflow_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            while ($data = $result->fetch_array(MYSQLI_ASSOC))
                $arrWorkflowDataIds[] = $data['id'];
        }

        $query = "delete from yongo_workflow_data where workflow_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $query = "delete from yongo_workflow_position where workflow_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $query = "delete from yongo_workflow where id = ? limit 1";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $steps = UbirimiContainer::get()['repository']->get(Workflow::class)->getSteps($Id);
        if ($steps) {
            while ($step = $steps->fetch_array(MYSQLI_ASSOC)) {
                $query = "delete from yongo_workflow_step_property_data where workflow_step_id = ?";

                $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
                $stmt->bind_param("i", $step['id']);
                $stmt->execute();
            }
        }

        $query = "delete from yongo_workflow_step where workflow_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        if (count($arrWorkflowDataIds)) {
            $query = "delete from yongo_workflow_post_function_data where workflow_data_id IN (" . implode(", ", $arrWorkflowDataIds) . ")";

            $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
            $stmt->execute();

            $query = "delete from yongo_workflow_condition_data where workflow_data_id IN (" . implode(", ", $arrWorkflowDataIds) . ")";
            $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
            $stmt->execute();
        }
    }

    public function getMetaDataById($Id) {
        $query = "select * " .
            "from yongo_workflow " .
            "where id = " . $Id;

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getDataById($Id) {
        $query = "select yongo_workflow_data.id, is1.name as isn1, is1.id as isi1, is2.name as isn2, is2.id as isi2, transition_name, transition_description, yongo_workflow_data.workflow_id, " .
                 "yongo_screen.name as screen_name, yongo_screen.id as screen_id, ws2.name as destination_step_name, ws2.id as destination_step_id " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step ws1 on ws1.id = yongo_workflow_data.workflow_step_id_from " .
            "left join yongo_workflow_step ws2 on ws2.id = yongo_workflow_data.workflow_step_id_to " .
            "left join yongo_issue_status is1 on ws1.linked_issue_status_id = is1.id " .
            "left join yongo_issue_status is2 on ws2.linked_issue_status_id = is2.id " .
            "left join yongo_screen on yongo_screen.id = yongo_workflow_data.screen_id " .
            "where yongo_workflow_data.id = " . $Id . ' ' .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getDataByWorkflowId($workflowId) {
        $query = "select yongo_workflow_data.id, yongo_workflow_data.screen_id, ws1.id as ws1id, ws2.id as ws2id, is1.name as isn1, is1.id as isi1, is2.name as isn2, is2.id as isi2, transition_name, transition_description, yongo_workflow_data.workflow_id " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step ws1 on ws1.id = yongo_workflow_data.workflow_step_id_from " .
            "left join yongo_workflow_step ws2 on ws2.id = yongo_workflow_data.workflow_step_id_to " .
            "left join yongo_issue_status is1 on ws1.linked_issue_status_id = is1.id " .
            "left join yongo_issue_status is2 on ws2.linked_issue_status_id = is2.id " .
            "where yongo_workflow_data.workflow_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getInitialStep($workflowId) {
        $query = "select yongo_workflow_step.* " .
            "from yongo_workflow_step " .
            "where yongo_workflow_step.workflow_id = ? and yongo_workflow_step.initial_step_flag = 1 " .
            "limit 1 ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getDataForCreation($workflowId) {
        $initialStep = UbirimiContainer::get()['repository']->get(Workflow::class)->getInitialStep($workflowId);

        $query = "select yongo_workflow_data.id, yongo_workflow_step.linked_issue_status_id " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step on yongo_workflow_step.id = yongo_workflow_data.workflow_step_id_to " .
            "where yongo_workflow_data.workflow_id = ? and yongo_workflow_data.workflow_step_id_from = ? limit 1 ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $workflowId, $initialStep['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getByIssueType($issueTypeId, $clientId) {
        $query = "select yongo_workflow.name, yongo_workflow.id " .
            "from yongo_workflow " .
            "left join yongo_issue_type_scheme_data on yongo_issue_type_scheme_data.issue_type_scheme_id = yongo_workflow.issue_type_scheme_id " .
            "left join yongo_issue_type on yongo_issue_type.id = yongo_issue_type_scheme_data.issue_type_id " .
            "where yongo_issue_type.id = ? " .
            "and yongo_workflow.client_id = ? " .
            "group by yongo_workflow.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $issueTypeId, $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getTransitions($workflowId) {
        $query = "select yongo_workflow_data.transition_name " .
            "from yongo_workflow_data " .
            "where yongo_workflow_data.workflow_id = ? group by transition_name " .
            "order by yongo_workflow_data.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getTransitionsForStepId($workflowId, $stepId) {
        $query = "select yongo_workflow_data.transition_name, yongo_workflow_data.screen_id, yongo_issue_status.id as status, yongo_issue_status.name, yongo_workflow_data.id, yongo_workflow_data.workflow_id, " .
                 "yongo_workflow_data.workflow_step_id_to " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step on yongo_workflow_step.id = yongo_workflow_data.workflow_step_id_to " .
            "left join yongo_issue_status on yongo_issue_status.id = yongo_workflow_step.linked_issue_status_id " .
            "where yongo_workflow_data.workflow_id = ? " .
            "and yongo_workflow_data.workflow_step_id_from = ? " .
            "order by yongo_workflow_data.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $workflowId, $stepId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getOriginatingStepsForTransition($workflowId, $transitionName) {
        $query = "select yongo_workflow_data.transition_name, yongo_workflow_step.id, yongo_workflow_step.name as step_name, " .
                    "yongo_workflow_data.id, yongo_workflow_data.workflow_id " .
                "from yongo_workflow_data " .
                "left join yongo_workflow_step on yongo_workflow_step.id = yongo_workflow_data.workflow_step_id_from " .
                "where yongo_workflow_data.workflow_id = ? " .
                    "and yongo_workflow_data.transition_name = ? " .
                    "order by yongo_workflow_data.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $workflowId, $transitionName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getDestinationStepsForTransition($workflowId, $transitionName) {
        $query = "select distinct yongo_workflow_data.transition_name, yongo_workflow_step.id, yongo_workflow_step.name as step_name, " .
            "yongo_workflow_data.workflow_id " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step on yongo_workflow_step.id = yongo_workflow_data.workflow_step_id_to " .
            "where yongo_workflow_data.workflow_id = ? " .
                 "and yongo_workflow_data.transition_name = ? " .
                 "order by yongo_workflow_data.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $workflowId, $transitionName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function deleteDataById($Id) {
        $q = 'delete from yongo_workflow_data where id = ? limit 1 ';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $q = 'delete from yongo_workflow_post_function_data where workflow_data_id = ? ';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
    }

    public function createInitialData($clientId, $workflowId) {
        $statusOpen = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getByName($clientId, 'status', 'Open');

        $q = 'insert into yongo_workflow_step(workflow_id, linked_issue_status_id, name, initial_step_flag) ' .
             'values(?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $linkedStatusIdNULL = null;
        $initialStepFlagOne = 1;
        $initialStepFlagZero = 0;
        $stepNameCreateIssue = 'Create Issue';
        $stepNameOpen = 'Open';

        $stmt->bind_param("iisi", $workflowId, $linkedStatusIdNULL, $stepNameCreateIssue, $initialStepFlagOne);
        $stmt->execute();
        $stepCreateId = UbirimiContainer::get()['db.connection']->insert_id;

        $stmt->bind_param("iisi", $workflowId, $statusOpen['id'], $stepNameOpen, $initialStepFlagZero);
        $stmt->execute();
        $stepOpenId = UbirimiContainer::get()['db.connection']->insert_id;

        $q = 'insert into yongo_workflow_data(workflow_id, workflow_step_id_from, workflow_step_id_to, transition_name) ' .
            'values(?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $transitionNameCreateIssue = 'Create Issue';
        $stmt->bind_param("iiis", $workflowId, $stepCreateId, $stepOpenId, $transitionNameCreateIssue);
        $stmt->execute();

        // set the position of the initial nodes
        // create node
        $query = "insert into yongo_workflow_position(workflow_id, workflow_step_id, top_position, left_position) " .
            "values (" . $workflowId . ", " . $stepCreateId . ", 73, 45)";
        UbirimiContainer::get()['db.connection']->query($query);

        // open node
        $query = "insert into yongo_workflow_position(workflow_id, workflow_step_id, top_position, left_position) " .
            "values (" . $workflowId . ", " . $stepOpenId . ", 119, 306)";
        UbirimiContainer::get()['db.connection']->query($query);
    }

    public function getSteps($workflowId, $allFlag = null) {
        $query = "select yongo_workflow_step.id, yongo_workflow_step.name as step_name, yongo_workflow_step.initial_step_flag, yongo_issue_status.id as status_id, yongo_issue_status.name as status_name, " .
            "yongo_workflow_step.workflow_id " .
            "from yongo_workflow_step " .
            "left join yongo_issue_status on yongo_issue_status.id = yongo_workflow_step.linked_issue_status_id " .
            "where yongo_workflow_step.workflow_id = ? ";
        if ($allFlag == null) {
            $query .= " and (yongo_workflow_step.initial_step_flag = 0 or yongo_workflow_step.initial_step_flag is null)";
        }
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result;
        } else {
            return null;
        }
    }

    public function addStep($workflowId, $name, $StatusId, $initialStepFlag, $date) {
        $q = 'insert into yongo_workflow_step(workflow_id, linked_issue_status_id, name, initial_step_flag, date_created) ' .
            'values(?, ?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("iisis", $workflowId, $StatusId, $name, $initialStepFlag, $date);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function getStepById($workflowStepId, $fieldName = null) {
        $query = "select yongo_workflow_step.id, yongo_workflow_step.workflow_id, yongo_workflow_step.name, yongo_issue_status.name as status_name, yongo_workflow_step.linked_issue_status_id " .
            "from yongo_workflow_step " .
            "left join yongo_issue_status on yongo_issue_status.id = yongo_workflow_step.linked_issue_status_id " .
            "where yongo_workflow_step.id = ? " .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowStepId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows) {

            $data = $result->fetch_array(MYSQLI_ASSOC);
            if ($fieldName)
                return $data[$fieldName];
            else
                return $data;
        } else
            return null;
    }

    public function addTransition($workflowId, $screenId, $stepIdFrom, $stepIdTo, $name, $description) {
        $q = 'insert into yongo_workflow_data(workflow_id, screen_id, workflow_step_id_from, workflow_step_id_to, transition_name, transition_description) ' .
             'values(?, ?, ?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("iiiiss", $workflowId, $screenId, $stepIdFrom, $stepIdTo, $name, $description);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function getIncomingTransitionsForStep($workflowId, $stepId) {
        $query = "select yongo_workflow_data.transition_name, yongo_workflow_step.id, yongo_workflow_step.name as step_name, " .
            "yongo_workflow_data.id, yongo_workflow_data.workflow_id " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step on yongo_workflow_step.id = yongo_workflow_data.workflow_step_id_to " .
            "where yongo_workflow_data.workflow_id = ? " .
                "and yongo_workflow_data.workflow_step_id_to = ? " .
                "order by yongo_workflow_data.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $workflowId, $stepId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function  getOutgoingTransitionsForStep($workflowId, $stepId, $resultType = null) {
        $query = "select yongo_workflow_data.transition_name, yongo_workflow_step.id, yongo_workflow_step.name as step_name, yongo_workflow_step.linked_issue_status_id, " .
            "yongo_workflow_data.id, yongo_workflow_data.workflow_id " .
            "from yongo_workflow_data " .
            "left join yongo_workflow_step on yongo_workflow_step.id = yongo_workflow_data.workflow_step_id_to " .
            "where yongo_workflow_data.workflow_id = ? " .
                "and yongo_workflow_data.workflow_step_id_from = ? " .
                "order by yongo_workflow_data.id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $workflowId, $stepId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($transition = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resultArray[] = $transition;
                }
                return $resultArray;
            } else return $result;

        } else
            return null;
    }

    public function getStepByWorkflowIdAndStatusId($workflowId, $issueStatusId) {
        $query = "select yongo_workflow_step.* " .
            "from yongo_workflow_step " .
            "where yongo_workflow_step.linked_issue_status_id = ? " .
            "and yongo_workflow_step.workflow_id = ? " .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $issueStatusId, $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function createDefaultStep($workflowId, $linkedIssueStatusId, $stepName, $initialStepFlag, $date) {
        $query = "INSERT INTO yongo_workflow_step(workflow_id, linked_issue_status_id, name, initial_step_flag, date_created) VALUES (?, ?, ?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("iisss", $workflowId, $linkedIssueStatusId, $stepName, $initialStepFlag, $date);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function updateStepById($stepId, $name, $StatusId, $date) {
        $q = 'update yongo_workflow_step set name = ?, linked_issue_status_id = ?, date_updated = ? ' .
            'where id = ? ' .
            'limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("sisi", $name, $StatusId, $date, $stepId);
        $stmt->execute();
    }

    public function addPostFunctionToTransition($transitionId, $functionId, $definitionData) {
        $query = "INSERT INTO yongo_workflow_post_function_data(workflow_data_id, sys_workflow_post_function_id, definition_data) VALUES (?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("iis", $transitionId, $functionId, $definitionData);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function deleteTransitions($workflowId, $transitionsPosted) {
        $q = 'delete from yongo_workflow_data where workflow_id = ? and id = ?';

        for ($i = 0; $i < count($transitionsPosted); $i++) {
            $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
            $stmt->bind_param("ii", $workflowId, $transitionsPosted[$i]);
            $stmt->execute();
        }
    }

    public function getByScreen($clientId, $screenId) {
        $query = "select yongo_workflow.id, yongo_workflow.name, yongo_workflow_data.transition_name, yongo_workflow_data.id as workflow_data_id " .
            "from yongo_workflow " .
            "left join yongo_workflow_data on yongo_workflow_data.workflow_id = yongo_workflow.id " .
            "where yongo_workflow.client_id = ? " .
            "and yongo_workflow_data.screen_id = ? " .
            "group by transition_name";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $clientId, $screenId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function addCondition($transitionId, $definitionData = null) {
        $query = "INSERT INTO yongo_workflow_condition_data(workflow_data_id, definition_data) VALUES (?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $transitionId, $definitionData);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function getConditionByTransitionId($workflowDataId) {
        $query = "select yongo_workflow_condition_data.* " .
            "from yongo_workflow_condition_data " .
            "where yongo_workflow_condition_data.workflow_data_id = ? " .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowDataId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function checkLogicalConditionsByTransitionId($workflowDataId) {
        $conditionData = UbirimiContainer::get()['repository']->get(Workflow::class)->getConditionByTransitionId($workflowDataId);
        $conditionString = $conditionData['definition_data'];

        $conditionString = str_replace("[[AND]]", ' && ', $conditionString);
        $conditionString = str_replace("[[OR]]", ' || ', $conditionString);
        preg_match_all('/cond_id=[0-9]+/', $conditionString, $conditions);
        $text = ' 1 ';
        for ($i = 0; $i < count($conditions); $i++) {
            $conditionString = str_replace($conditions[$i], $text, $conditionString);
        }

        preg_match_all('/perm_id=[0-9]+/', $conditionString, $permissions);
        $permissions = $permissions[0];
        for ($i = 0; $i < count($permissions); $i++) {
            $conditionString = str_replace($permissions[$i], $text, $conditionString);
        }

        $canBeExecuted = true;
        if ($conditionString) {
            $finalCondition = "\$result = (" . $conditionString . ") ? 1 : 0; return \$result;";

            $canBeExecuted = @eval($finalCondition);
            $error = error_get_last();
            if (strstr($error["message"], "Parse error"))
                $canBeExecuted = false;
        }

        return $canBeExecuted;
    }

    public function checkConditionsByTransitionId($workflowDataId, $userId, $issueData) {
        $conditionData = UbirimiContainer::get()['repository']->get(Workflow::class)->getConditionByTransitionId($workflowDataId);
        $conditionString = $conditionData['definition_data'];

        $conditionString = str_replace("[[AND]]", ' && ', $conditionString);
        $conditionString = str_replace("[[OR]]", ' || ', $conditionString);
        preg_match_all('/cond_id=[0-9]+/', $conditionString, $conditions);

        for ($i = 0; $i < count($conditions); $i++) {
            $conditionId = (int)str_replace('cond_id=', '', $conditions[$i]);

            $text = '';
            switch ($conditionId) {
                case WorkflowCondition::CONDITION_ONLY_ASSIGNEE:

                    if ($userId == $issueData[Field::FIELD_ASSIGNEE_CODE])
                        $text = ' 1 ';
                    else
                        $text = ' 0 ';
                    break;
                case WorkflowCondition::CONDITION_ONLY_REPORTER:

                    if ($userId == $issueData[Field::FIELD_REPORTER_CODE])
                        $text = ' 1 ';
                    else
                        $text = ' 0 ';
                    break;
            }
            $conditionString = str_replace($conditions[$i], $text, $conditionString);
        }

        preg_match_all('/perm_id=[0-9]+/', $conditionString, $permissions);
        $permissions = $permissions[0];
        for ($i = 0; $i < count($permissions); $i++) {
            $permissionId = (int)str_replace('perm_id=', '', $permissions[$i]);

            $hasPermission = UbirimiContainer::get()['repository']->get(YongoProject::class)->userHasPermission(array($issueData['issue_project_id']), $permissionId, $userId);
            if ($hasPermission)
                $text = ' 1 ';
            else
                $text = ' 0 ';

            $conditionString = str_replace($permissions[$i], $text, $conditionString);
        }

        $canBeExecuted = true;
        if ($conditionString) {
            $finalCondition = "\$result = (" . $conditionString . ") ? 1 : 0; return \$result;";

            $canBeExecuted = @eval($finalCondition);
            $error = error_get_last();
            if (strstr($error["message"], "Parse error"))
                $canBeExecuted = false;
        }

        return $canBeExecuted;
    }

    public function getByIssueStatusId($StatusId) {
        $query = "select yongo_workflow.id, yongo_workflow.name " .
            "from yongo_workflow_step " .
            "left join yongo_workflow on yongo_workflow.id = yongo_workflow_step.workflow_id " .
            "where yongo_workflow_step.linked_issue_status_id = ? ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $StatusId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getByClientIdAndName($clientId, $name) {
        $query = "select * from yongo_workflow where client_id = ? and LOWER(name) = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $clientId, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function copy($clientId, $workflowId, $name, $description, $date) {
        $oldWorkflow = UbirimiContainer::get()['repository']->get(Workflow::class)->getMetaDataById($workflowId);
        $newWorkflowId = UbirimiContainer::get()['repository']->get(Workflow::class)->createNewMetaData($clientId, $oldWorkflow['issue_type_scheme_id'], $name, $description, $date);

        // duplicate the steps
        $oldWorkflowSteps = UbirimiContainer::get()['repository']->get(Workflow::class)->getSteps($workflowId, 1);
        $stepsLinking = array();
        while ($oldStep = $oldWorkflowSteps->fetch_array(MYSQLI_ASSOC)) {
            $newStepId = UbirimiContainer::get()['repository']->get(Workflow::class)->addStep($newWorkflowId, $oldStep['step_name'], $oldStep['status_id'], $oldStep['initial_step_flag'], $date);
            $stepsLinking[$oldStep['id']] = $newStepId;
        }

        // duplicate the data
        $dataLinking = array();
        $oldWorkflowData = UbirimiContainer::get()['repository']->get(Workflow::class)->getDataByWorkflowId($workflowId);
        while ($oldWorkflowRow = $oldWorkflowData->fetch_array(MYSQLI_ASSOC)) {
            $newDataId = UbirimiContainer::get()['repository']->get(Workflow::class)->addTransition($newWorkflowId, $oldWorkflowRow['screen_id'], $stepsLinking[$oldWorkflowRow['ws1id']], $stepsLinking[$oldWorkflowRow['ws2id']], $oldWorkflowRow['transition_name'], $oldWorkflowRow['transition_description']);
            $dataLinking[$oldWorkflowRow['id']] = $newDataId;
        }

        // duplicate the position
        $oldPositions = UbirimiContainer::get()['repository']->get(WorkflowPosition::class)->getByWorkflowId($workflowId);
        while ($oldPosition = $oldPositions->fetch_array(MYSQLI_ASSOC)) {
            UbirimiContainer::get()['repository']->get(WorkflowPosition::class)->addSinglePositionRecord($newWorkflowId, $stepsLinking[$oldPosition['workflow_step_id']], $oldPosition['top_position'], $oldPosition['left_position']);
        }

        // duplicate the post function data
        foreach ($dataLinking as $oldDataId => $newDataId) {
            $oldFunctionData = UbirimiContainer::get()['repository']->get(WorkflowFunction::class)->getByWorkflowDataId($oldDataId);
            while ($oldFunctionData && $oldFunctionRow = $oldFunctionData->fetch_array(MYSQLI_ASSOC)) {
                UbirimiContainer::get()['repository']->get(Workflow::class)->addPostFunctionToTransition($newDataId, $oldFunctionRow['function_id'], $oldFunctionRow['definition_data']);
            }
        }

        // duplicate workflow condition data
        foreach ($dataLinking as $oldDataId => $newDataId) {

            $oldConditionData = UbirimiContainer::get()['repository']->get(WorkflowCondition::class)->getByTransitionId($oldDataId);
            if ($oldConditionData) {
                UbirimiContainer::get()['repository']->get(Workflow::class)->addCondition($newDataId, $oldConditionData['definition_data']);
            }
        }
    }

    public function getStepByWorkflowIdAndName($workflowId, $name) {
        $query = "select * from yongo_workflow_step where workflow_id = ? and LOWER(name) = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $workflowId, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getLinkedStatuses($workflowId, $resultType = null, $field = null) {
        $query = "select linked_issue_status_id, yongo_issue_status.name
                    from yongo_workflow_step
                    left join yongo_issue_status on yongo_issue_status.id = yongo_workflow_step.linked_issue_status_id
                    where workflow_id = ? and
                        linked_issue_status_id is not null";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($status = $result->fetch_array(MYSQLI_ASSOC)) {
                    if ($field)
                        $resultArray[] = $status[$field];
                    else
                        $resultArray[] = $status;
                }
                return $resultArray;
            } else return $result;
        } else
            return null;
    }

    public function deleteStepById($stepId) {
        $q = 'delete from yongo_workflow_data where workflow_step_id_from = ?';
        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("i", $stepId);
        $stmt->execute();

        $q = 'delete from yongo_workflow_step where id = ? limit 1';
        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("i", $stepId);
        $stmt->execute();
    }

    public function deleteOutgoingTransitionsForStepId($workflowId, $stepId) {
        $q = 'delete from yongo_workflow_data where workflow_step_id_from = ? and workflow_id = ?';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("ii", $stepId, $workflowId);
        $stmt->execute();
    }

    public function getStepKeyByStepIdAndKeyId($stepId, $keyId, $stepPropertyId = null) {
        $query = "select * from yongo_workflow_step_property_data where workflow_step_id = ? and sys_workflow_step_property_id = ? ";

        if ($stepPropertyId)
            $query .= 'and id != ' . $stepPropertyId;

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $stepId, $keyId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result;
        } else
            return null;
    }

    public function addStepProperty($stepId, $keyId, $value, $date) {
        $q = 'insert into yongo_workflow_step_property_data(workflow_step_id, sys_workflow_step_property_id, value, date_created) ' .
            'values(?, ?, ?, ?)';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("isss", $stepId, $keyId, $value, $date);

        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function getStepProperties($stepId, $resultType = null, $field = null) {
        $query = "select yongo_workflow_step_property_data.id, yongo_workflow_step_property_data.value, yongo_workflow_step_property.name " .
                 "from yongo_workflow_step_property_data " .
                 "left join yongo_workflow_step_property on yongo_workflow_step_property.id = yongo_workflow_step_property_data.sys_workflow_step_property_id " .
                 "where workflow_step_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $stepId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
                    if ($field)
                        $resultArray[] = $data[$field];
                    else
                        $resultArray[] = $data;
                }
                return $resultArray;
            } else return $result;

        } else
            return null;
    }

    public function getSystemWorkflowProperties() {
        $query = "select * from yongo_workflow_step_property";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result;
        } else
            return null;
    }

    public function deleteStepPropertyById($propertyId) {
        $q = 'delete from yongo_workflow_step_property_data where id = ? limit 1 ';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("i", $propertyId);
        $stmt->execute();
    }

    public function getStepPropertyById($stepPropertyId) {
        $query = "select * from yongo_workflow_step_property_data where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $stepPropertyId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result->fetch_array(MYSQLI_ASSOC);
        } else
            return null;
    }

    public function updateStepPropertyById($stepPropertyId, $keyId, $value, $date) {
        $q = 'update yongo_workflow_step_property_data set sys_workflow_step_property_id = ?, value = ?, date_updated = ? ' .
             'where id = ? ' .
             'limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($q);
        $stmt->bind_param("issi", $keyId, $value, $date, $stepPropertyId);
        $stmt->execute();
    }

    public function getAllSteps() {
        $query = "select * from yongo_workflow_step";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result;
        } else
            return null;
    }
}
