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

namespace Ubirimi\Yongo\Repository\Issue;

use Ubirimi\Container\UbirimiContainer;

class IssueType
{
    public function getAll($clientId) {
        $query = "SELECT yongo_issue_type.* " .
                 "FROM yongo_issue_type " .
                 "where yongo_issue_type.client_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getAllSubTasks($clientId) {
        $query = "SELECT yongo_issue_type.* " .
                 "FROM yongo_issue_type " .
                 "where yongo_issue_type.client_id = ? and sub_task_flag = 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getById($Id) {
        $query = "SELECT id, name, description, client_id FROM yongo_issue_type WHERE id = ? LIMIT 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function updateById($Id, $name, $description, $date) {
        $query = 'UPDATE yongo_issue_type SET ' .
                 'name = ?, description = ?, date_updated = ? ' .
                 'WHERE id = ? ' .
                 'LIMIT 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("sssi", $name, $description, $date, $Id);
        $stmt->execute();
    }

    public function getSchemesForIssueTypeId($typeId, $type) {
        $query = "select yongo_issue_type_scheme.name, yongo_issue_type_scheme.description, yongo_issue_type_scheme.id " .
            "from yongo_issue_type " .
            "left join yongo_issue_type_scheme_data on yongo_issue_type_scheme_data.issue_type_id = yongo_issue_type.id " .
            "left join yongo_issue_type_scheme on yongo_issue_type_scheme.id = yongo_issue_type_scheme_data.issue_type_scheme_id " .
            "where yongo_issue_type.id = ? " .
            "and yongo_issue_type_scheme.type = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $typeId, $type);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getByProjects($projectIdsArray) {
        $query = "select distinct yongo_issue_type.id, yongo_issue_type.name " .
            "from yongo_project " .
            "left join yongo_issue_type_scheme_data on yongo_issue_type_scheme_data.issue_type_scheme_id = yongo_project.issue_type_scheme_id " .
            "left join yongo_issue_type on yongo_issue_type.id = yongo_issue_type_scheme_data.issue_type_id " .
            "where yongo_project.id  IN (" . implode(',', $projectIdsArray) . ')';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function deleteById($Id) {
        $query = 'delete from yongo_issue_type where id = ? limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $query = 'delete from yongo_field_issue_type_data where issue_type_id = ?';
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $query = 'delete from yongo_issue_type_field_configuration_data where issue_type_id = ?';
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();

        $query = 'delete from yongo_issue_type_screen_scheme_data where issue_type_id = ?';
        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
    }
}
