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

namespace Ubirimi\Yongo\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
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

    public function comment($clientId, $loggedInUserId, $issue, $project, $content)
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

    public function link($issueId, $project, $comment)
    {
        $clientId = $project['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            $issue = UbirimiContainer::get()['repository']->get(Issue::class)->getByParameters(array('issue_id' => $issueId), $this->session->get('user/id'));
            $eventId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($this->session->get('client/id'), IssueEvent::EVENT_ISSUE_COMMENTED_CODE, 'id');
            $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {
                if ($userToNotify['user_id'] == $this->session->get('user/id') && !$userToNotify['notify_own_changes_flag']) {
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
                            'content' => $comment,
                            'user' => $userToNotify)
                    ),
                    $date);
            }
        }
    }

    public function share($issue, $userIds, $noteContent)
    {
        $clientId = $issue['client_id'];
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings) {

            $userThatShares = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($this->session->get('user/id'));
            for ($i = 0; $i < count($userIds); $i++) {

                $user = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($userIds[$i]);

                $subject = $smtpSettings['email_prefix'] . ' ' .
                    $userThatShares['first_name'] . ' ' .
                    $userThatShares['last_name'] . ' shared ' .
                    $issue['project_code'] . '-' . $issue['nr'] . ': ' . substr($issue['summary'], 0, 20) . ' with you';

                UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                    $smtpSettings['from_address'],
                    $user['email'],
                    null,
                    $subject,
                    Util::getTemplate('_issueShare.php', array(
                            'issue' => $issue,
                            'userThatShares' => $userThatShares,
                            'noteContent' => $noteContent)
                    ),
                    Util::getServerCurrentDateTime());
            }
        }
    }

    public function workLogged($issue, $project, $extraInformation)
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

                $subject = $smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue Work Logged " . $issue['project_code'] . '-' . $issue['nr'];

                UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                    $smtpSettings['from_address'],
                    $userToNotify['email'],
                    null,
                    $subject,
                    Util::getTemplate('_workLogged.php',array(
                            'issue' => $issue,
                            'project' => $project,
                            'extraInformation' => $extraInformation,
                            'user' => $this->session->get('user'))
                    ),
                    Util::getServerCurrentDateTime());
            }
        }
    }

    public function addAttachment($issue, $project, $extraInformation)
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

                $subject = $smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue Add Attachment " . $issue['project_code'] . '-' . $issue['nr'];

                UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                    $smtpSettings['from_address'],
                    $userToNotify['email'],
                    null,
                    $subject,
                    Util::getTemplate('_addAttachment.php',array(
                            'issue' => $issue,
                            'project' => $project,
                            'extraInformation' => $extraInformation,
                            'user' => $this->session->get('user'))
                    ),
                    Util::getServerCurrentDateTime());
            }
        }
    }

    public function workLogUpdated($issue, $project, $extraInformation)
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

                $subject = $smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue Work log Updated " . $issue['project_code'] . '-' . $issue['nr'];

                UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                    $smtpSettings['from_address'],
                    $userToNotify['email'],
                    null,
                    $subject,
                    Util::getTemplate('_workLogUpdated.php',array(
                            'issue' => $issue,
                            'project' => $project,
                            'extraInformation' => $extraInformation,
                            'user' => $this->session->get('user'))
                    ),
                    Util::getServerCurrentDateTime());
            }
        }
    }

    public function workLogDeleted($issue, $project, $extraInformation)
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

                $subject = $smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue Work log Deleted " . $issue['project_code'] . '-' . $issue['nr'];

                UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                    $smtpSettings['from_address'],
                    $userToNotify['email'],
                    null,
                    $subject,
                    Util::getTemplate('_workLogDeleted.php',array(
                            'issue' => $issue,
                            'project' => $project,
                            'extraInformation' => $extraInformation,
                            'user' => $this->session->get('user'))
                    ),
                    Util::getServerCurrentDateTime());
            }
        }
    }

    public function create($clientId, $issue, $project, $loggedInUserId) {

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

            $emailContent = UbirimiContainer::get()['template']->render('_newIssue.php', array(
                'issue' => $issue,
                'custom_fields_single_value' => $customFieldsSingleValue,
                'custom_fields_user_picker_multiple' => $customFieldsUserPickerMultiple,
                'components' => $components,
                'versions_fixed' => $versionsFixed,
                'versions_affected' => $versionsAffected));

            $messageData = array(
                'from' => $clientSmtpSettings['from_address'],
                'to' => $userToNotify['email'],
                'clientId' => $clientId,
                'subject' => sprintf("%s [Issue] - New issue CREATED %s-%s", $clientSmtpSettings['email_prefix'], $issue['project_code'], $issue['nr']),
                'content' => $emailContent,
                'date' => Util::getServerCurrentDateTime());

            UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
        }
    }

    public function update($clientId, $issue, $loggedInUserId, $fieldChanges) {

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

    public function delete($clientId, $issue, $project, $loggedInUser) {

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

    public function assign($clientId, $issueData, $oldUserAssignedName, $newUserAssignedName, $project, $loggedInUserId, $comment) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $eventAssignedId = UbirimiContainer::get()['repository']->get(IssueEvent::class)->getByClientIdAndCode($clientId, IssueEvent::EVENT_ISSUE_ASSIGNED_CODE, 'id');
        $projectId = $project['id'];
        $users = UbirimiContainer::get()['repository']->get(YongoProject::class)->getUsersForNotification($projectId, $eventAssignedId, $issueData, $loggedInUserId);
        $loggedInUser = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($loggedInUserId);

        while ($users && $user = $users->fetch_array(MYSQLI_ASSOC)) {

            if ($user['user_id'] == $loggedInUserId && !$user['notify_own_changes_flag']) {
                continue;
            }

            $subject = $clientSmtpSettings['email_prefix'] . ' ' .
                "[Issue] - Issue UPDATED " .
                $issueData['project_code'] . '-' .
                $issueData['nr'];

            $date = Util::getServerCurrentDateTime();

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                $clientSmtpSettings['from_address'],
                $user['email'],
                null,
                $subject,
                Util::getTemplate('_issueAssign.php', array(
                        'issue' => $issueData,
                        'comment' => $comment,
                        'project' => array('id' => $issueData['issue_project_id'], 'name' => $issueData['project_name']),
                        'loggedInUser' => $loggedInUser,
                        'oldUserAssignedName' => $oldUserAssignedName,
                        'newUserAssignedName' => $newUserAssignedName)
                ),
                $date);
        }
    }
}