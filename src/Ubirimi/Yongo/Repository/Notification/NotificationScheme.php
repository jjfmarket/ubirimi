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

namespace Ubirimi\Yongo\Repository\Notification;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Yongo\Repository\Issue\IssueEvent;

class NotificationScheme
{
    private $name;
    private $description;
    private $clientId;

    function __construct($clientId = null, $name = null, $description = null) {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->description = $description;

        return $this;
    }

    public function getAll() {
        $query = "select * " .
                 "from yongo_notification_scheme ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function save($currentDate) {
        $query = "INSERT INTO yongo_notification_scheme(client_id, name, description, date_created) VALUES (?, ?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("isss", $this->clientId, $this->name, $this->description, $currentDate);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function getByClientId($clientId) {
        $query = "select * " .
            "from yongo_notification_scheme " .
            "where client_id = ? ";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getMetaDataById($Id) {
        $query = "select * " .
            "from yongo_notification_scheme " .
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

    public function getMetaDataByNameAndClientId($clientId, $name) {
        $query = "select * from yongo_notification_scheme where client_id = ? and LOWER(name) = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("is", $clientId, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function updateMetaDataById($Id, $name, $description, $date) {
        $query = "update yongo_notification_scheme set name = ?, description = ?, date_updated = ? where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("sssi", $name, $description, $date, $Id);
        $stmt->execute();
    }

    public function addDataRaw($notificationSchemeId, $eventId, $permissionRoleId, $groupId, $userId, $currentAssignee, $reporter, $currentUser, $projectLead, $componentLead, $allWatchers, $userPickerMultipleSelection, $currentDate) {
        $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, permission_role_id, group_id, user_id, current_assignee, reporter, " .
                    "`current_user`, project_lead, component_lead, all_watchers, user_picker_multiple_selection, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("iiiiiiiiiiiis", $notificationSchemeId, $eventId, $permissionRoleId, $groupId, $userId, $currentAssignee, $reporter, $currentUser, $projectLead, $componentLead, $allWatchers, $userPickerMultipleSelection, $currentDate);
        $stmt->execute();

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public function addData($notificationSchemeId, $eventId, $notificationType, $user, $group, $role, $userPickerMultipleSelection, $currentDate) {
        switch ($notificationType) {
            case Notification::NOTIFICATION_TYPE_USER:
                $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, user_id, date_created) VALUES (?, ?, ?, ?)";

                $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

                $stmt->bind_param("iiis", $notificationSchemeId, $eventId, $user, $currentDate);
                $stmt->execute();

                return UbirimiContainer::get()['db.connection']->insert_id;

                break;

            case Notification::NOTIFICATION_TYPE_USER_PICKER_MULTIPLE_SELECTION:
                $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, user_picker_multiple_selection, date_created) VALUES (?, ?, ?, ?)";

                $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

                $stmt->bind_param("iiis", $notificationSchemeId, $eventId, $userPickerMultipleSelection, $currentDate);
                $stmt->execute();

                return UbirimiContainer::get()['db.connection']->insert_id;

                break;

            case Notification::NOTIFICATION_TYPE_GROUP:
                $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, group_id, date_created) VALUES (?, ?, ?, ?)";

                $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

                $stmt->bind_param("iiis", $notificationSchemeId, $eventId, $group, $currentDate);
                $stmt->execute();

                return UbirimiContainer::get()['db.connection']->insert_id;

                break;

            case Notification::NOTIFICATION_TYPE_PROJECT_ROLE:
                $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, permission_role_id, date_created) VALUES (?, ?, ?, ?)";

                $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

                $stmt->bind_param("iiis", $notificationSchemeId, $eventId, $role, $currentDate);
                $stmt->execute();

                return UbirimiContainer::get()['db.connection']->insert_id;

                break;

            case Notification::NOTIFICATION_TYPE_CURRENT_ASSIGNEE:
            case Notification::NOTIFICATION_TYPE_REPORTER:
            case Notification::NOTIFICATION_TYPE_CURRENT_USER:
            case Notification::NOTIFICATION_TYPE_PROJECT_LEAD:
            case Notification::NOTIFICATION_TYPE_COMPONENT_LEAD:
            case Notification::NOTIFICATION_TYPE_ALL_WATCHERS:
                $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, `" . $notificationType . "`, date_created) VALUES (?, ?, ?, ?)";

                $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
                $value = 1;
                $stmt->bind_param("iiis", $notificationSchemeId, $eventId, $value, $currentDate);
                $stmt->execute();

                return UbirimiContainer::get()['db.connection']->insert_id;

                break;
        }
    }

    public function getDataByNotificationSchemeId($notificationSchemeId) {
        $query = "select yongo_notification_scheme_data.* " .
                     "from yongo_notification_scheme_data " .
                     "where yongo_notification_scheme_data.notification_scheme_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $notificationSchemeId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function getDataByNotificationSchemeIdAndEventId($notificationSchemeId, $eventId) {
        $query = "select yongo_notification_scheme_data.id, general_user.first_name, general_user.last_name, general_user.id as user_id, general_group.id as group_id, general_group.name as group_name, yongo_notification_scheme_data.current_assignee, yongo_notification_scheme_data.reporter,  " .
            "yongo_notification_scheme_data.all_watchers, yongo_field.name as custom_field_name, yongo_field.id as custom_field_id, " .
            "yongo_notification_scheme_data.current_user, yongo_notification_scheme_data.permission_role_id, yongo_notification_scheme_data.project_lead, yongo_notification_scheme_data.component_lead, " .
            "yongo_permission_role.name as role_name, " .
            "yongo_event.id as event_id, yongo_event.name as event_name " .
            "from yongo_notification_scheme_data " .
            "left join yongo_event on yongo_event.id = yongo_notification_scheme_data.event_id " .
            "left join general_user on general_user.id = yongo_notification_scheme_data.user_id " .
            "left join `general_group` on  `general_group`.id = yongo_notification_scheme_data.group_id " .
            "left join yongo_permission_role on yongo_permission_role.id = yongo_notification_scheme_data.permission_role_id " .
            "left join yongo_field on yongo_field.id = yongo_notification_scheme_data.user_picker_multiple_selection " .
            "where yongo_notification_scheme_data.notification_scheme_id = ? and " .
                "yongo_notification_scheme_data.event_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $notificationSchemeId, $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows)
            return $result;
        else
            return null;
    }

    public function deleteDataById($notificationSchemeDataId) {
        $query = "delete from yongo_notification_scheme_data where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("i", $notificationSchemeDataId);
        $stmt->execute();
    }

    public function deleteDataByNotificationSchemeId($notificationSchemeId) {
        $query = "delete from yongo_notification_scheme_data where notification_scheme_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("i", $notificationSchemeId);
        $stmt->execute();
    }

    public function addDefaultNotifications($clientId, $notificationSchemeId, $date) {
        $eventCreatedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_CREATED_CODE, 'id');

        $eventUpdatedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_UPDATED_CODE, 'id');
        $eventAssignedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_ASSIGNED_CODE, 'id');

        $eventResolvedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_RESOLVED_CODE, 'id');

        $eventClosedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_CLOSED_CODE, 'id');
        $eventCommentedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_COMMENTED_CODE, 'id');

        $eventCommentEditedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_COMMENT_EDITED_CODE, 'id');
        $eventReopenedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_REOPENED_CODE, 'id');
        $eventWorkStartedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_WORK_STARTED_CODE, 'id');

        $eventWorkStoppedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_WORK_STOPPED_CODE, 'id');
        $eventDeletedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_DELETED_CODE, 'id');

        $eventMovedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_MOVED_CODE, 'id');
        $eventWorkLoggedOnIssueId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_WORK_LOGGED_ON_ISSUE_CODE, 'id');
        $eventWorkLogUpdatedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_WORKLOG_UPDATED_CODE, 'id');
        $eventWorkLogDeletedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_WORKLOG_DELETED_CODE, 'id');

        $eventGenericId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_GENERIC_CODE, 'id');

        $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, reporter, date_created) VALUES " .
            "(" . $notificationSchemeId . "," . $eventCreatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventUpdatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventAssignedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventResolvedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventClosedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventCommentedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventCommentEditedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventReopenedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventDeletedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkStartedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventGenericId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventMovedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLoggedOnIssueId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLogUpdatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLogDeletedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkStoppedId . ", 1, '" . $date . "')";

        UbirimiContainer::get()['db.connection']->query($query);

        $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, current_assignee, date_created) VALUES " .
            "(" . $notificationSchemeId . "," . $eventCreatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventUpdatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventAssignedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventResolvedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventClosedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventCommentedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventCommentEditedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventReopenedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventDeletedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkStartedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventGenericId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventMovedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLoggedOnIssueId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLogUpdatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLogDeletedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkStoppedId . ", 1, '" . $date . "')";

        UbirimiContainer::get()['db.connection']->query($query);

        $query = "INSERT INTO yongo_notification_scheme_data(notification_scheme_id, event_id, all_watchers, date_created) VALUES " .
            "(" . $notificationSchemeId . "," . $eventCreatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventUpdatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventAssignedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventResolvedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventClosedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventCommentedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventCommentEditedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventReopenedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventDeletedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkStartedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventGenericId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventMovedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLoggedOnIssueId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLogUpdatedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkLogDeletedId . ", 1, '" . $date . "')," .
            "(" . $notificationSchemeId . "," . $eventWorkStoppedId . ", 1, '" . $date . "')";

        UbirimiContainer::get()['db.connection']->query($query);
    }

    public function deleteById($notificationSchemeId) {
        $query = "delete from yongo_notification_scheme where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("i", $notificationSchemeId);
        $stmt->execute();
    }

    public function deleteByClientId($clientId) {
        $notificationSchemes = UbirimiContainer::get()['repository']->get(NotificationScheme::class)->getByClientId($clientId);
        while ($notificationSchemes && $notificationScheme = $notificationSchemes->fetch_array(MYSQLI_ASSOC)) {
            UbirimiContainer::get()['repository']->get(NotificationScheme::class)->deleteDataByNotificationSchemeId($notificationScheme['id']);
            UbirimiContainer::get()['repository']->get(NotificationScheme::class)->deleteById($notificationScheme['id']);
        }
    }
}
