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

namespace Ubirimi\Agile\Repository\Board;

use Ubirimi\Agile\Repository\Sprint\Sprint;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Issue\IssueFilter;
use Ubirimi\Yongo\Repository\Issue\IssueSettings;

class Board
{
    public $name;
    public $description;
    public $clientId;
    public $filterId;
    public $projects;

    function __construct($clientId = null, $filterId = null, $name = null, $description = null, $projects = null) {
        $this->clientId = $clientId;
        $this->filterId = $filterId;
        $this->name = $name;
        $this->description = $description;
        $this->projects = $projects;

        return $this;
    }

    public function save($userCreatedId, $currentDate) {
        $boardId = $this->saveAgileBoard($userCreatedId, $currentDate);

        $projectCount = count($this->projects);
        for ($i = 0; $i < $projectCount; $i++) {
            $this->saveAgileBoardProject($boardId, $this->projects[$i]);
        }

        return $boardId;
    }

    protected function saveAgileBoard($userCreatedId, $currentDate) {
        $query = "INSERT INTO agile_board(
                 client_id, filter_id, name, description, swimlane_strategy, user_created_id, date_created
                 ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?
                 )";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $defaultSwimlaneStrategy = 'story';
        $stmt->bind_param(
            "iisssis",
            $this->clientId,
            $this->filterId,
            $this->name,
            $this->description,
            $defaultSwimlaneStrategy,
            $userCreatedId,
            $currentDate
        );
        $stmt->execute();

        $boardId = UbirimiContainer::get()['db.connection']->insert_id;

        return $boardId;
    }

    protected function saveAgileBoardProject($boardId, $projectIndex) {
        $query = "INSERT INTO agile_board_project(agile_board_id, project_id) VALUES (?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("ii", $boardId, $projectIndex);
        $stmt->execute();
    }

    public function getByClientId($clientId, $resultType = null) {
        $query = "select agile_board.client_id, agile_board.id, agile_board.filter_id, " .
            "agile_board.name, agile_board.description, agile_board.swimlane_strategy, " .
            "agile_board.user_created_id, agile_board.date_created, general_user.first_name, " .
            "general_user.last_name, yongo_filter.name as filter_name, yongo_filter.id as filter_id, " .
            "yongo_filter.definition as filter_definition " .
            "from agile_board " .
            "left join general_user on general_user.id = agile_board.user_created_id " .
            "left join yongo_filter on yongo_filter.id = agile_board.filter_id " .
            "where agile_board.client_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($board = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resultArray[] = $board;
                }

                return $resultArray;
            } else
                return $result;
        } else
            return null;
    }

    public function getById($boardId) {
        $query = "select agile_board.id, agile_board.client_id, agile_board.name, agile_board.description, " .
            "agile_board.user_created_id, agile_board.swimlane_strategy, yongo_filter.name as filter_name, " .
            "yongo_filter.description as filter_description, general_user.first_name, general_user.last_name " .
            "from agile_board " .
            "left join yongo_filter on yongo_filter.id = agile_board.filter_id " .
            "left join general_user on general_user.id = agile_board.user_created_id " .
            "where agile_board.id = ? " .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getProjects($boardId, $resultType = null) {
        $query = "select yongo_project.id, yongo_project.name " .
            "from agile_board_project " .
            "left join yongo_project on yongo_project.id = agile_board_project.project_id " .
            "where agile_board_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($prj = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resultArray[] = $prj;
                }

                return $resultArray;
            } else return $result;

        } else
            return null;
    }

    public function addStatusToColumn($columnId, $StatusId) {
        $query = "INSERT INTO agile_board_column_status(agile_board_column_id, issue_status_id) VALUES (?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("ii", $columnId, $StatusId);
        $stmt->execute();
    }

    public function addDefaultColumnData($clientId, $boardId) {
        // add To Do column
        $columnId = $this->saveColumn($boardId, 1, 'To Do');

        $openStatusData = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getByName($clientId, 'status', 'Open');
        $reopenedStatusData = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getByName($clientId, 'status', 'Reopened');
        UbirimiContainer::get()['repository']->get(Board::class)->addStatusToColumn($columnId, $openStatusData['id']);
        UbirimiContainer::get()['repository']->get(Board::class)->addStatusToColumn($columnId, $reopenedStatusData['id']);

        // add In Progress column
        $columnId = $this->saveColumn($boardId, 2, 'In Progress');

        $inProgressStatusData = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getByName($clientId, 'status', 'In Progress');
        UbirimiContainer::get()['repository']->get(Board::class)->addStatusToColumn($columnId, $inProgressStatusData['id']);

        // add Done column
        $columnId = $this->saveColumn($boardId, 3, 'Done');

        $resolvedStatusData = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getByName($clientId, 'status', 'Resolved');
        $closedStatusData = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getByName($clientId, 'status', 'Closed');
        UbirimiContainer::get()['repository']->get(Board::class)->addStatusToColumn($columnId, $resolvedStatusData['id']);
        UbirimiContainer::get()['repository']->get(Board::class)->addStatusToColumn($columnId, $closedStatusData['id']);
    }

    protected function saveColumn($boardId, $position, $columnName) {
        $query = "INSERT INTO agile_board_column(agile_board_id, position, name) VALUES (?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("iis", $boardId, $position, $columnName);
        $stmt->execute();
        $columnId = UbirimiContainer::get()['db.connection']->insert_id;

        return $columnId;
    }

    public function getColumns($boardId, $resultType = null) {
        $query = "select agile_board_column.* " .
            "from agile_board_column " .
            "where agile_board_column.agile_board_id = ? " .
            "order by position";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($column = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resultArray[] = $column;
                }

                return $resultArray;
            } else {
                return $result;
            }
        } else {
            return null;
        }
    }

    public function getColumnStatuses($columnId, $resultType = null, $column = null) {
        $query = "select yongo_issue_status.id, yongo_issue_status.name " .
            "from agile_board_column_status " .
            "left join yongo_issue_status on yongo_issue_status.id = agile_board_column_status.issue_status_id " .
            "where agile_board_column_status.agile_board_column_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $columnId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($status = $result->fetch_array(MYSQLI_ASSOC)) {
                    if ($column)
                        $resultArray[] = $status[$column];
                    else
                        $resultArray[] = $status;
                }

                return $resultArray;
            } else return $result;

        } else
            return null;
    }

    public function deleteStatusFromColumn($boardId, $StatusId) {
        $columns = UbirimiContainer::get()['repository']->get(Board::class)->getColumns($boardId, 'array');
        $columnsIds = array();
        $columnsCount = count($columns);
        for ($i = 0; $i < $columnsCount; $i++) {
            $columnsIds[] = $columns[$i]['id'];
        }

        $columnIdsList = implode(', ', $columnsIds);
        $query = "delete from agile_board_column_status where issue_status_id = ? and agile_board_column_id IN (?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $StatusId, $columnIdsList);
        $stmt->execute();
    }

    public function getUnmappedStatuses($clientId, $boardId, $resultType = null) {
        $clientStatuses = UbirimiContainer::get()['repository']->get(IssueSettings::class)->getAllIssueSettings('status', $clientId, 'array');

        $query = "select yongo_issue_status.id, yongo_issue_status.name " .
            "from agile_board " .
            "left join agile_board_column on agile_board_column.agile_board_id = agile_board.id " .
            "left join agile_board_column_status on agile_board_column_status.agile_board_column_id = agile_board_column.id " .
            "left join yongo_issue_status on yongo_issue_status.id = agile_board_column_status.issue_status_id " .
            "where agile_board.id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();
        $result = $stmt->get_result();

        $resultArray = array();

        if ($result->num_rows) {
            $clientStatusesCount = count($clientStatuses);
            for ($i = 0; $i < $clientStatusesCount; $i++) {
                $found = false;
                while ($status = $result->fetch_array(MYSQLI_ASSOC)) {
                    if ($clientStatuses[$i]['id'] == $status['id']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found)
                    $resultArray[] = $clientStatuses[$i];
                $result->data_seek(0);
            }
        }

        return $resultArray;
    }

    public function addColumn($boardId, $name, $description) {
        $query = "INSERT INTO agile_board_column(agile_board_id, name) VALUES (?, ?)";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("is", $boardId, $name);
        $stmt->execute();
    }

    public function deleteColumn($columnId) {
        $query = "delete from agile_board_column_status where agile_board_column_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $columnId);
        $stmt->execute();

        $query = "delete from agile_board_column where id = ? limit 1";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $columnId);
        $stmt->execute();
    }

    public function getLast5BoardsByClientId($clientId) {
        $query = "select * " .
            "from agile_board " .
            "where client_id = ? " .
            "order by id desc";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getBacklogIssues($clientId, $boardData, $onlyMyIssuesFlag, $loggedInUserId, $searchText, $completeStatuses) {
        $filterId = $boardData['filter_id'];

        $filterData = UbirimiContainer::get()['repository']->get(IssueFilter::class)->getById($filterId);
        $definition = $filterData['definition'];
        $definitionArray = explode('&', $definition);
        $searchParameters = array();
        $definitionArrayCount = count($definitionArray);
        for ($i = 0; $i < $definitionArrayCount; $i++) {
            $keyValueArray = explode('=', $definitionArray[$i]);
            if ($keyValueArray[0] != 'search_query') {
                $searchParameters[$keyValueArray[0]] = explode('|', $keyValueArray[1]);
            } else {
                $searchParameters['search_query'] = $keyValueArray[1];
            }
        }

        $searchParameters['client_id'] = $clientId;
        $searchParameters['backlog'] = true;
        if ($onlyMyIssuesFlag)
            $searchParameters['assignee'] = $loggedInUserId;

        if ($searchText) {
            $searchParameters['search_query'] = $searchText;
            $searchParameters['summary_flag'] = 1;
        }

        $searchParameters['not_status'] = $completeStatuses;

        return UbirimiContainer::get()['repository']->get(Issue::class)->getByParameters($searchParameters, $loggedInUserId, null, $loggedInUserId);
    }

    public function deleteIssuesFromSprints($issueIdArray) {
        $issueIdList = implode(", ", $issueIdArray);

        $query = "delete from agile_board_sprint_issue where issue_id IN (?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("s", $issueIdList);
        $stmt->execute();
    }

    public function getIssuesBySprintAndStatusIdAndParentId($sprintId, $parentId = null, $statuses, $onlyMyIssuesFlag, $loggedInUserId) {
        $statusList = implode(", ", $statuses);

        $query = "select yongo_issue.id, nr, yongo_issue.parent_id, yongo_issue_priority.name as priority_name, " .
            "yongo_issue_status.name as status_name, yongo_issue_status.id as status, summary, yongo_issue.description, " .
            "environment, yongo_issue_type.name as type, yongo_project.code as project_code, yongo_project.name as project_name, " .
            "yongo_issue.project_id as issue_project_id, yongo_issue_type.id as type, " .
            "yongo_issue_type.description as issue_type_description, yongo_issue_type.icon_name as issue_type_icon_name, " .
            "yongo_issue_priority.description as issue_priority_description, yongo_issue_priority.icon_name as issue_priority_icon_name, " .
            "yongo_issue_priority.color as priority_color from agile_board_sprint_issue " .
            "left join yongo_issue on yongo_issue.id = agile_board_sprint_issue.issue_id " .
            "LEFT join yongo_issue_priority on yongo_issue.priority_id = yongo_issue_priority.id " .
            "LEFT join yongo_issue_type on yongo_issue.type_id = yongo_issue_type.id " .
            "LEFT JOIN yongo_issue_status on yongo_issue.status_id = yongo_issue_status.id " .
            "LEFT join yongo_project on yongo_issue.project_id = yongo_project.id " .
            "where agile_board_sprint_issue.agile_board_sprint_id = ? " .
            "and yongo_issue.status_id IN (?) ";
        if ($onlyMyIssuesFlag)
            $query .= "and yongo_issue.user_assigned_id = " . $loggedInUserId . " ";
        if ($parentId)
            $query .= "and yongo_issue.parent_id = " . $parentId . " ";
        else
            $query .= "and yongo_issue.parent_id is null ";

        $query .= "order by id desc";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $sprintId, $statusList);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getLastColumn($boardId) {
        $query = "select * " .
            "from agile_board_column " .
            "where agile_board_id = ? " .
            "order by position desc " .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function transferNotDoneIssues($boardId, $sprintId, $completeStatuses) {
        $nextSprint = UbirimiContainer::get()['repository']->get(Sprint::class)->getNextNotStartedByBoardId($boardId, $sprintId);

        $completeStatusesList = implode(', ', $completeStatuses);
        // set as done the completed issues
        $query = "select * " .
            "from agile_board_sprint_issue " .
            "left join yongo_issue on yongo_issue.id = agile_board_sprint_issue.issue_id " .
            "where agile_board_sprint_id = ? " .
            "and yongo_issue.status_id IN (?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $sprintId, $completeStatusesList);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows) {
            $issueIdArray = array();
            while ($issue = $result->fetch_array(MYSQLI_ASSOC)) {
                $issueIdArray[] = $issue['issue_id'];
            }

            $issuesIdList = implode(', ', $issueIdArray);

            $queryUpdate = 'update agile_board_sprint_issue set done_flag = 1 where agile_board_sprint_id = ? and  issue_id IN (?)';

            $stmtUpdate = UbirimiContainer::get()['db.connection']->prepare($queryUpdate);
            $stmtUpdate->bind_param("is", $sprintId, $issuesIdList);
            $stmtUpdate->execute();
        }

        // transfer the not done issues to the next sprint
        $query = "select * " .
            "from agile_board_sprint_issue " .
            "left join yongo_issue on yongo_issue.id = agile_board_sprint_issue.issue_id " .
            "where agile_board_sprint_id = ? " .
            "and done_flag = 0";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $sprintId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($nextSprint) {

                $issueIdArray = array();
                while ($issue = $result->fetch_array(MYSQLI_ASSOC)) {
                    $issueIdArray[] = $issue['issue_id'];
                }
                $queryTransfer = 'insert into agile_board_sprint_issue(agile_board_sprint_id, issue_id) values ';
                $queryTransferPart = array();
                $issueIdArrayCount = count($issueIdArray);
                for ($i = 0; $i < $issueIdArrayCount; $i++) {
                    $queryTransferPart[] = '(' . $nextSprint['id'] . ', ' . $issueIdArray[$i] . ')';
                }
                $queryTransfer .= implode(', ', $queryTransferPart);
                $stmtTransfer = UbirimiContainer::get()['db.connection']->prepare($queryTransfer);
                $stmtTransfer->execute();
            }
        }
    }

    public function deleteByProjectId($projectId) {
        $query = "delete from agile_board_project where project_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
    }

    public function deleteById($boardId) {
        $boardColumnsArray = UbirimiContainer::get()['repository']->get(Board::class)->getColumns($boardId, 'array');
        if ($boardColumnsArray) {
            $boardColumnsIds = Util::array_column($boardColumnsArray, 'id');
            $boardColumnsIdList = implode(', ', $boardColumnsIds);
            $query = "delete from agile_board_column_status where agile_board_column_id IN (?)";
            $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
            $stmt->bind_param("s", $boardColumnsIdList);
            $stmt->execute();
        }

        $query = "delete from agile_board_column where agile_board_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();

        $query = "delete from agile_board_project where agile_board_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();

        $sprintIdsArray = UbirimiContainer::get()['repository']->get(Sprint::class)->getByBoardId($boardId, 'array', 'id');

        $sprintIdsArrayCount = count($sprintIdsArray);
        for ($i = 0; $i < $sprintIdsArrayCount; $i++) {
            $query = "delete from agile_board_sprint_issue where agile_board_sprint_id = ?";
            $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
            $stmt->bind_param("i", $sprintIdsArray[$i]);
            $stmt->execute();
        }

        $query = "delete from agile_board_sprint where agile_board_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();

        $query = "delete from agile_board where id = ? limit 1";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $boardId);
        $stmt->execute();
    }

    public function updateColumnOrder($newOrder) {
        $newOrderCount = count($newOrder);
        for ($i = 0; $i < $newOrderCount; $i++) {
            $query = "update agile_board_column set position = ? where id = ? limit 1";

            $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
            $position = $i + 1;
            $stmt->bind_param("ii", $position, $newOrder[$i]);
            $stmt->execute();
        }
    }

    public function updateSwimlaneStrategy($boardId, $strategy) {
        $query = "update agile_board set swimlane_strategy = ? where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("si", $strategy, $boardId);
        $stmt->execute();
    }

    public function updateMetadata($clientId, $boardId, $name, $description, $date) {
        $query = "update agile_board set name = ?, description = ?, date_updated = ? where client_id = ? and id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("sssii", $name, $description, $date, $clientId, $boardId);
        $stmt->execute();
    }

    public function getByFilterId($filterId) {
        $query = "select * " .
            "from agile_board " .
            "where filter_id = ? " .
            "order by id desc";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $filterId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {

            return $result;
        } else {
            return null;
        }
    }

    public function getAll($filters = null) {
        $query = "select * " .
            "from agile_board ";

        if (empty($filters['sort_by'])) {
            $query .= " order by agile_board.id";
        } else {
            $query .= " order by " . $filters['sort_by'] . " " . $filters['sort_order'];
        }

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result;
        } else {
            return null;
        }
    }
}
