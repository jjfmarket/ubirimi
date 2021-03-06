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

class WorkflowScheme {

    public $name;
    public $description;
    public $clientId;

    function __construct($clientId = null, $name = null, $description = null) {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->description = $description;

        return $this;
    }

    public function save($currentDate) {
        $query = "INSERT INTO yongo_workflow_scheme(client_id, name, description, date_created) VALUES (?, ?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("isss", $this->clientId, $this->name, $this->description, $currentDate);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function addData($workflowSchemeId, $workflowId, $currentDate) {
        $query = "INSERT INTO yongo_workflow_scheme_data(workflow_scheme_id, workflow_id, date_created) VALUES (?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("iis", $workflowSchemeId, $workflowId, $currentDate);
        $stmt->execute();
    }

    public function deleteDataByWorkflowSchemeId($Id) {
        $query = "delete from yongo_workflow_scheme_data where workflow_scheme_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
    }

    public function deleteById($Id) {
        $query = "delete from yongo_workflow_scheme where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
    }

    public function updateMetaDataById($Id, $name, $description) {
        $query = "update yongo_workflow_scheme set name = ?, description = ? where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ssi", $name, $description, $Id);
        $stmt->execute();
    }

    public function getDataById($Id) {
        $query = "select yongo_workflow_scheme_data.id, yongo_workflow_scheme_data.workflow_id, yongo_workflow.name, yongo_workflow.description, " .
                 "yongo_workflow_scheme_data.workflow_scheme_id " .
                 "from yongo_workflow_scheme_data " .
                 "left join yongo_workflow on yongo_workflow.id = yongo_workflow_scheme_data.workflow_id " .
                 "where workflow_scheme_id = ? ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getMetaDataById($Id) {
        $query = "select * " .
            "from yongo_workflow_scheme " .
            "where id = ? " .
            "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return null;
    }

    public function getMetaDataByClientId($clientId) {
        $query = "select * from yongo_workflow_scheme where client_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getWorkflows($workflowSchemeId) {
        $query = "select yongo_workflow.* " .
            "from yongo_workflow_scheme " .
            "left join yongo_workflow_scheme_data on yongo_workflow_scheme_data.workflow_scheme_id = yongo_workflow_scheme.id " .
            "left join yongo_workflow on yongo_workflow.id = yongo_workflow_scheme_data.workflow_id " .
            "where yongo_workflow_scheme.id = ? ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowSchemeId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getByWorkflowId($workflowId) {
        $query = "select yongo_workflow_scheme.id, yongo_workflow_scheme.name " .
            "from yongo_workflow_scheme_data " .
            "left join yongo_workflow_scheme on yongo_workflow_scheme.id = yongo_workflow_scheme_data.workflow_scheme_id " .
            "where yongo_workflow_scheme_data.workflow_id = ? " .
            "group by yongo_workflow_scheme_data.workflow_scheme_id";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $workflowId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function deleteByClientId($clientId) {
        $schemes = UbirimiContainer::get()['repository']->get(WorkflowScheme::class)->getMetaDataByClientId($clientId);
        if ($schemes) {
            while ($scheme = $schemes->fetch_array(MYSQLI_ASSOC)) {
                UbirimiContainer::get()['repository']->get(WorkflowScheme::class)->deleteDataByWorkflowSchemeId($scheme['id']);
                UbirimiContainer::get()['repository']->get(WorkflowScheme::class)->deleteById($scheme['id']);
            }
        }
    }

    public function getByClientIdAndName($clientId, $name) {
        $query = "select * from yongo_workflow_scheme where client_id = ? and LOWER(name) = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $clientId, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }
}
