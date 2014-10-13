<?php

namespace Ubirimi\Yongo\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Email\Email;
use Ubirimi\Repository\User\User;
use Ubirimi\Service\UbirimiService;
use Ubirimi\Yongo\Repository\Issue\Event;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Project\Project;

class IssueEmailService extends UbirimiService
{
    /**
     * @var \Ubirimi\Yongo\Service\WorkflowService;
     */
    private $workflowService;

    public function __construct(SessionInterface $session, WorkflowService $service)
    {
        parent::__construct($session);

        $this->workflowService = $service;
    }

    public function emailIssueNew($issue)
    {
        $project = $this->getRepository('yongo.project.project')->getById($issue['issue_project_id']);

        if ($this->workflowService->hasEvent($this->session->get('client/id'), $issue['issue_project_id'], $issue['type'])) {
            $smtpSettings = $this->session->get('client/settings/smtp');
            if ($smtpSettings) {

                Email::$smtpSettings = $smtpSettings;

                Email::triggerNewIssueNotification($this->session->get('client/id'), $issue, $project, $this->session->get('user/id'));
            }
        }
    }

    public function emailIssueUpdate($issue, $oldIssueData, $fieldChanges)
    {
        $smtpSettings = $this->session->get('client/settings/smtp');

        Email::$smtpSettings = $smtpSettings;

        Email::triggerIssueUpdatedNotification($this->session->get('client/id'), $oldIssueData, $this->session->get('user/id'), $fieldChanges);

    }

    public function emailIssueDelete($issue, $project, $extraInformation)
    {
        $smtpSettings = $this->session->get('client/settings/smtp');
        if ($smtpSettings) {

            Email::$smtpSettings = $smtpSettings;
            Email::triggerDeleteIssueNotification($this->session->get('client/id'), $issue, $project, $extraInformation);
        }
    }

    public function emailIssueComment($issue, $project, $content)
    {
        if ($this->session->get('client/settings/smtp')) {

            Email::$smtpSettings = $this->session->get('client/settings/smtp');

            // notify people
            $eventId = Event::getByClientIdAndCode($this->session->get('client/id'), Event::EVENT_ISSUE_COMMENTED_CODE, 'id');
            $users = $this->getRepository('yongo.project.project')->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {

                if ($userToNotify['user_id'] == $this->session->get('user/id') && !$userToNotify['notify_own_changes_flag']) {
                    continue;
                }

                Email::sendEmailNotificationNewComment($issue, $this->session->get('client/id'), $project, $userToNotify, $content, $this->session->get('user'));
            }
        }
    }

    public function emailIssueLink($issueId, $project, $comment)
    {
        $smtpSettings = $this->session->get('client/settings/smtp');
        if ($smtpSettings) {
            Email::$smtpSettings = $smtpSettings;

            $issue = UbirimiContainer::getRepository('yongo.issue.issue')->getByParameters(array('issue_id' => $issueId), $this->session->get('user/id'));
            $eventId = Event::getByClientIdAndCode($this->session->get('client/id'), Event::EVENT_ISSUE_COMMENTED_CODE, 'id');
            $users = $this->getRepository('yongo.project.project')->getUsersForNotification($issue['issue_project_id'], $eventId, $issue, $this->session->get('user/id'));

            while ($users && $userToNotify = $users->fetch_array(MYSQLI_ASSOC)) {
                if ($userToNotify['user_id'] == $this->session->get('user/id') && $userToNotify['notify_own_changes_flag']) {
                    Email::sendEmailNotificationNewComment($issue, $this->session->get('client/id'), $project, $userToNotify, $comment, $this->session->get('user'));
                }
                else {
                    Email::sendEmailNotificationNewComment($issue, $this->session->get('client/id'), $project, $userToNotify, $comment, $this->session->get('user'));
                }
            }
        }
    }

    public function emailIssueShare($issue, $userIds, $noteContent)
    {
        $smtpSettings = $this->session->get('client/settings/smtp');

        if ($smtpSettings) {

            Email::$smtpSettings = $smtpSettings;
            $userThatShares = $this->getRepository('ubirimi.user.user')->getById($this->session->get('user/id'));
            for ($i = 0; $i < count($userIds); $i++) {

                $user = $this->getRepository('ubirimi.user.user')->getById($userIds[$i]);

                Email::shareIssue($this->session->get('client/id'), $issue, $userThatShares, $user['email'], $noteContent);
            }
        }
    }
}