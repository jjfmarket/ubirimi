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

namespace Ubirimi\Yongo\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Email\Email;
use Ubirimi\Repository\Email\EmailQueue;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\Service\UbirimiService;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\CustomField;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Issue\IssueComponent;
use Ubirimi\Yongo\Repository\Issue\IssueEvent;
use Ubirimi\Yongo\Repository\Issue\IssueVersion;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class IssueEmailService extends UbirimiService
{
    public function __construct(SessionInterface $session)
    {
        parent::__construct($session);
    }

    public function emailIssueComment($clientId, $loggedInUserId, $issue, $project, $content)
    {
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            // notify people
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_ISSUE_COMMENTED_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $loggedInUserId);

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {

                if ($userToNotify['user_id'] == $loggedInUserId && !$userToNotify['notify_own_changes_flag']) {
                    continue;
                }

                $subject = $smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue COMMENT " . $issue['project_code'] . '-' . $issue['nr'];

                $date = Util::getServerCurrentDateTime();

                UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                    $smtpSettings['from_address'],
                    $userToNotify['email'],
                    null,
                    $subject,
                    Util::getTemplate('_newComment.php',array(
                            'issue' => $issue,
                            'project' => $project,
                            'content' => $content,
                            'user' => $userToNotify)
                    ),
                    $date);
            }
        }
    }

    public function emailIssueLink($issueId, $project, $comment)
    {
        $clientId = $project['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            $issue = UbirimiContainer::get()['repository']->get(Issue::class)->getByParameters(array('issue_id' => $issueId), $this->session->get('user/id'));
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_ISSUE_COMMENTED_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {
                if ($userToNotify['user_id'] == $this->session->get('user/id') && $userToNotify['notify_own_changes_flag']) {
                    UbirimiContainer::get()['repository']->get(Email::class)->sendEmailNotificationNewComment($issue, $this->session->get('client/id'), $project, $userToNotify, $comment, $this->session->get('user'));
                }
                else {
                    UbirimiContainer::get()['repository']->get(Email::class)->sendEmailNotificationNewComment($issue, $this->session->get('client/id'), $project, $userToNotify, $comment, $this->session->get('user'));
                }
            }
        }
    }

    public function emailIssueShare($issue, $userIds, $noteContent)
    {
        $clientId = $issue['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            $userThatShares = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($this->session->get('user/id'));
            for ($i = 0; $i < count($userIds); $i++) {

                $user = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($userIds[$i]);

                UbirimiContainer::get()['repository']->get(Email::class)->shareIssue($this->session->get('client/id'), $issue, $userThatShares, $user['email'], $noteContent);
            }
        }
    }

    public function emailIssueWorkLogged($issue, $project, $extraInformation)
    {
        $clientId = $project['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            // notify people
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_WORK_LOGGED_ON_ISSUE_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {

                if ($userToNotify['user_id'] == $this->session->get('user/id') && !$userToNotify['notify_own_changes_flag']) {
                    continue;
                }

                UbirimiContainer::get()['repository']->get(Email::class)->sendEmailNotificationWorkLogged($issue, $this->session->get('client/id'), $project, $userToNotify, $extraInformation, $this->session->get('user'));
            }
        }
    }

    public function emailIssueAddAttachemnt($issue, $project, $extraInformation)
    {
        $clientId = $project['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            // notify people
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_ISSUE_UPDATED_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {

                if ($userToNotify['user_id'] == $this->session->get('user/id') && !$userToNotify['notify_own_changes_flag']) {
                    continue;
                }

                UbirimiContainer::get()['repository']->get(Email::class)->sendEmailNotificationAddAttachment($issue, $this->session->get('client/id'), $project, $userToNotify, $extraInformation, $this->session->get('user'));
            }
        }
    }

    public function emailIssueWorkLogUpdated($issue, $project, $extraInformation)
    {
        $clientId = $project['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            // notify people
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_ISSUE_WORKLOG_UPDATED_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {

                if ($userToNotify['user_id'] == $this->session->get('user/id') && !$userToNotify['notify_own_changes_flag']) {
                    continue;
                }

                UbirimiContainer::get()['repository']->get(Email::class)->sendEmailNotificationWorkLogUpdated($issue, $this->session->get('client/id'), $project, $userToNotify, $extraInformation, $this->session->get('user'));
            }
        }
    }

    public function emailIssueWorkLogDeleted($issue, $project, $extraInformation)
    {
        $clientId = $project['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            // notify people
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_ISSUE_WORKLOG_DELETED_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {

                if ($userToNotify['user_id'] == $this->session->get('user/id') && !$userToNotify['notify_own_changes_flag']) {
                    continue;
                }

                UbirimiContainer::get()['repository']->get(Email::class)->sendEmailNotificationWorkLogDeleted($issue, $this->session->get('client/id'), $project, $userToNotify, $extraInformation, $this->session->get('user'));
            }
        }
    }

    public function emailIssueCreate($clientId, $issue, $project, $loggedInUserId) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $eventCreatedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_CREATED_CODE, 'id');
        $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($project['id'], $eventCreatedId, $issue, $loggedInUserId);

        while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {
            if ($userToNotify['user_id'] == $loggedInUserId && !$userToNotify['notify_own_changes_flag']) {
                continue;
            }

            $issueId = $issue['id'];
            $projectId = $issue['issue_project_id'];
            $versionsAffected = UbirimiContainer::get()['repository']->get(IssueVersion::class)->getByIssueIdAndProjectId($issueId, $projectId, Issue::ISSUE_AFFECTED_VERSION_FLAG);
            $versionsFixed = UbirimiContainer::get()['repository']->get(IssueVersion::class)->getByIssueIdAndProjectId($issueId, $projectId, Issue::ISSUE_FIX_VERSION_FLAG);
            $components = UbirimiContainer::get()['repository']->get(IssueComponent::class)->getByIssueIdAndProjectId($issueId, $projectId);

            $customFieldsSingleValue = UbirimiContainer::get()['repository']->get(CustomField::class)->getCustomFieldsData($issueId);
            $customFieldsUserPickerMultiple = UbirimiContainer::get()['repository']->get(CustomField::class)->getUserPickerData($issueId);

            $subject = $clientSmtpSettings['email_prefix'] . ' ' .
                "[Issue] - New issue CREATED " .
                $issue['project_code'] . '-' .
                $issue['nr'];

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                $clientSmtpSettings['from_address'],
                $userToNotify['email'],
                null,
                $subject,
                Util::getTemplate('_newIssue.php', array(
                        'issue' => $issue,
                        'custom_fields_single_value' => $customFieldsSingleValue,
                        'custom_fields_user_picker_multiple' => $customFieldsUserPickerMultiple,
                        'components' => $components,
                        'versions_fixed' => $versionsFixed,
                        'versions_affected' => $versionsAffected)
                ),
                Util::getServerCurrentDateTime());
        }
    }

    public function emailIssueUpdate($clientId, $issue, $loggedInUserId, $fieldChanges) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $projectId = $issue['issue_project_id'];
        $eventUpdatedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_UPDATED_CODE, 'id');
        $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($projectId, $eventUpdatedId, $issue, $loggedInUserId);
        $project = UbirimiContainer::get()['repository']->get(YongoProject::class)->getById($projectId);
        $loggedInUser = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($loggedInUserId);

        while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {
            if ($userToNotify['user_id'] == $loggedInUserId && !$userToNotify['notify_own_changes_flag']) {
                continue;
            }

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                $clientSmtpSettings['from_address'],
                $userToNotify['email'],
                null,
                $clientSmtpSettings['email_prefix'] . ' ' . "[Issue] - Issue UPDATED " . $issue['project_code'] . '-' . $issue['nr'],
                Util::getTemplate('_issueUpdated.php', array(
                        'issue' => $issue,
                        'project' => $project,
                        'user' => $loggedInUser,
                        'fieldChanges' => $fieldChanges)
                ),
                Util::getServerCurrentDateTime());
        }
    }

    public function emailIssueDelete($clientId, $issue, $project, $loggedInUser) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $projectId = $issue['issue_project_id'];
        $loggedInUserId = $loggedInUser['id'];

        $eventDeletedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_DELETED_CODE, 'id');
        $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($projectId, $eventDeletedId, $issue, $loggedInUserId);

        while ($users && $user = $users->fetch_array(MYSQLI_ASSOC)) {
            if ($user['user_id'] == $loggedInUserId && !$user['notify_own_changes_flag']) {
                continue;
            }

            $subject = $clientSmtpSettings['email_prefix'] . ' ' .
                "[Issue] - Issue DELETED " .
                $issue['project_code'] . '-' .
                $issue['nr'];

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                $clientSmtpSettings['from_address'],
                $user['email'],
                null,
                $subject,
                Util::getTemplate('_deleteIssue.php', array('issue' => $issue, 'loggedInUser' => $loggedInUser, 'project' => $project)),
                Util::getServerCurrentDateTime());

        }
    }
}