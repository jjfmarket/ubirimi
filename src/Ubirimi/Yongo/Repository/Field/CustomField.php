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

namespace Ubirimi\Yongo\Repository\Field;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Yongo\Repository\Issue\IssueType;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class CustomField {

    public function getById($Id) {
        $query = "SELECT * from yongo_field where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getByClientId($clientId) {
        $query = "SELECT yongo_field.id, yongo_field.name, yongo_field.description, yongo_field_type.id as type_id, yongo_field_type.name as type_name, " .
                 "yongo_field.all_issue_type_flag, yongo_field.all_project_flag, yongo_field_type.description as type_description " .
            "from yongo_field " .
            "left join yongo_field_type on yongo_field_type.id = yongo_field.sys_field_type_id " .
            "where system_flag = 0 and client_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getTypes() {
        $query = "SELECT * FROM yongo_field_type";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function create($clientId, $fieldType, $name, $description, $issueType, $project, $date) {
        $query = "INSERT INTO yongo_field(client_id, sys_field_type_id, name, description, system_flag, all_issue_type_flag, all_project_flag, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $systemFlag = 0;
        $fieldTypeResult = UbirimiContainer::get()['repository']->get(FieldType::class)->getByCode($fieldType);
        $fieldTypeId = $fieldTypeResult['id'];

        $allIssueTypeFlag = (count($issueType) == 1 && $issueType[0] == -1) ? 1 : 0;
        $allProjectFlag = (count($project) == 1 && $project[0] == -1) ? 1 : 0;

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("iissiiis", $clientId, $fieldTypeId, $name, $description, $systemFlag, $allIssueTypeFlag, $allProjectFlag, $date);
        $stmt->execute();

        $fieldId = UbirimiContainer::get()['db.connection']->insert_id;

        // add data if necessary

        if ($allIssueTypeFlag) {
            $issueTypeResult = UbirimiContainer::get()['repository']->get(IssueType::class)->getAll($clientId);
            $issueType = array();
            while ($type = $issueTypeResult->fetch_array(MYSQLI_ASSOC)) {
                $issueType[] = $type['id'];
            }
        }

        for ($i = 0; $i < count($issueType); $i++) {
            $queryIssueType = "INSERT INTO yongo_field_issue_type_data(field_id, issue_type_id) VALUES (?, ?)";
            if ($stmtIssueType = UbirimiContainer::get()['db.connection']->prepare($queryIssueType)) {

                $stmtIssueType->bind_param("ii", $fieldId, $issueType[$i]);
                $stmtIssueType->execute();
            }
        }

        if ($allProjectFlag) {
            $projectResult = UbirimiContainer::get()['repository']->get(YongoProject::class)->getByClientId($clientId);
            $project = array();
            while ($pr = $projectResult->fetch_array(MYSQLI_ASSOC)) {
                $project[] = $pr['id'];
            }
        }
        for ($i = 0; $i < count($project); $i++) {
            $queryProject = "INSERT INTO yongo_field_project_data(field_id, project_id) VALUES (?, ?)";
            if ($stmtProject = UbirimiContainer::get()['db.connection']->prepare($queryProject)) {

                $stmtProject->bind_param("ii", $fieldId, $project[$i]);
                $stmtProject->execute();
            }
        }

        return $fieldId;
    }

    public function updateMetaDataById($Id, $name, $description, $date) {
        $query = "update field set name = ?, description = ?, date_updated = ? where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("sssi", $name, $description, $date, $Id);
        $stmt->execute();
    }

    public function deleteDataByProjectId($projectId) {
        $query = "delete from yongo_field_project_data where project_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
    }

    public function deleteById($customFieldId) {
        $query = "delete from yongo_field where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $customFieldId);
        $stmt->execute();

        $query = "delete from yongo_field_configuration_data where field_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $customFieldId);
        $stmt->execute();

        $query = "delete from yongo_field_issue_type_data where field_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $customFieldId);
        $stmt->execute();

        $query = "delete from yongo_field_project_data where field_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $customFieldId);
        $stmt->execute();

        $query = "delete from yongo_issue_custom_field_data where field_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $customFieldId);
        $stmt->execute();

        $query = "delete from yongo_screen_data where field_id = ?";
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $customFieldId);
        $stmt->execute();
    }

    public function getByNameAndType($clientId, $name, $fieldType) {
        $query = "SELECT * from yongo_field where client_id = ? and sys_field_type_id = ? and name = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("iis", $clientId, $fieldType, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }
}
