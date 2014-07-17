<?php

namespace Ubirimi\Yongo\Repository\Issue;
use Ubirimi\Container\UbirimiContainer;

class IssueEvent {

    const EVENT_ISSUE_CREATED_CODE = 1;
    const EVENT_ISSUE_UPDATED_CODE = 2;
    const EVENT_ISSUE_ASSIGNED_CODE = 3;
    const EVENT_ISSUE_RESOLVED_CODE = 4;
    const EVENT_ISSUE_CLOSED_CODE = 5;
    const EVENT_ISSUE_COMMENTED_CODE = 6;
    const EVENT_ISSUE_COMMENT_EDITED_CODE = 7;
    const EVENT_ISSUE_REOPENED_CODE = 8;
    const EVENT_ISSUE_DELETED_CODE = 9;
    const EVENT_ISSUE_WORK_STARTED_CODE = 10;
    const EVENT_ISSUE_WORK_STOPPED_CODE = 11;
    const EVENT_GENERIC_CODE = 12;
    const EVENT_ISSUE_MOVED  = 13;

    private $name;
    private $description;
    private $clientId;

    function __construct($clientId, $name, $description) {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->description = $description;

        return $this;
    }

    public function save($currentDate) {
        $query = "INSERT INTO event(client_id, name, description, date_created) VALUES (?, ?, ?, ?)";
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("isss", $this->clientId, $this->name, $this->description, $currentDate);
            $stmt->execute();

            return UbirimiContainer::get()['db.connection']->insert_id;
        }
    }

    public static function getByClient($clientId) {
        $query = "SELECT event.* " .
            "FROM event " .
            "WHERE event.client_id = ? " .
            "order by system_flag desc, name";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $clientId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getById($Id) {
        $query = "SELECT event.* " .
            "FROM event " .
            "WHERE event.id = ? " .
            "limit 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows) {
                return $result->fetch_array(MYSQLI_ASSOC);
            } else
                return null;
        }
    }

    public static function getByClientIdAndCode($clientId, $code, $returnedField = null) {
        $query = "SELECT event.* " .
            "FROM event " .
            "WHERE event.client_id = ? and code = ? " .
            "limit 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("ii", $clientId, $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                $value = $data;
                if ($returnedField)
                    $value = $data[$returnedField];

                return $value;
            } else
                return null;
        }
    }

    public static function updateById($Id, $name, $description, $date) {
        $query = "update event set name = ?, description = ?, date_updated = ? where id = ? limit 1";
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("sssi", $name, $description, $date, $Id);
            $stmt->execute();
        }
    }

    public static function getEventByWorkflowDataId($workflowDataId) {
        $query = "SELECT definition_data " .
            "FROM workflow_post_function_data " .
            "WHERE workflow_data_id = ? " .
            "and definition_data like 'event=%' " .
            "limit 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $workflowDataId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                $definitionData = explode("=", $data['definition_data']);
                $eventId = $definitionData[1];

                $event = IssueEvent::getById($eventId);
                return $event;
            } else
                return null;
        }
    }

    public static function getWorkflowsByEventId($clientId, $eventId) {
        $query = "SELECT workflow.id, workflow.name " .
            "FROM workflow " .
            "left join workflow_data on workflow_data.workflow_id = workflow.id " .
            "left join workflow_post_function_data on workflow_post_function_data.workflow_data_id = workflow_data.id " .
            "WHERE workflow.client_id = ? " .
            "and workflow_post_function_data.definition_data = ? " .
            "group by workflow.id";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $eventString = 'event=' . $eventId;
            $stmt->bind_param("is", $clientId, $eventString);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows) {
                return $result;
            } else
                return null;
        }
    }

    public static function getNotificationSchemesByEventId($clientId, $eventId) {
        $query = "SELECT notification_scheme.id, notification_scheme.name " .
            "FROM notification_scheme " .
            "left join notification_scheme_data on notification_scheme_data.notification_scheme_id = notification_scheme.id " .
            "WHERE notification_scheme.client_id = ? " .
            "and notification_scheme_data.event_id = ? " .
            "group by notification_scheme.id";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("ii", $clientId, $eventId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows) {
                return $result;
            } else
                return null;
        }
    }

    public static function deleteById($eventId) {
        $query = "delete from event where id = ? limit 1";
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("i", $eventId);
            $stmt->execute();
        }
    }
}