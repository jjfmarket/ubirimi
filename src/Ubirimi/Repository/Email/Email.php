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

namespace Ubirimi\Repository\Email;

use Swift_Mailer;
use Swift_Message;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\CustomField;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Issue\IssueComponent;
use Ubirimi\Yongo\Repository\Issue\IssueEvent;
use Ubirimi\Yongo\Repository\Issue\IssueVersion;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class Email {

    public function sendNewUserNotificationEmail($clientId, $firstName, $lastName, $username, $password, $email, $clientDomain) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        $subject = Email::$smtpSettings['email_prefix'] . ' ' . 'Ubirimi - A new account has been created for you';

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                        Email::$smtpSettings['from_address'],
                        $email,
                        null,
                        $subject,
                        Util::getTemplate('_newUser.php', array(
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'username' => $username,
                            'password' => $password,
                            'clientDomain' => $clientDomain)
                        ),
                        Util::getServerCurrentDateTime());
    }

    public function sendNewCustomerNotificationEmail($clientId, $firstName, $lastName, $email, $password, $clientDomain) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        $subject = Email::$smtpSettings['email_prefix'] . ' ' . 'Ubirimi - A new customer account has been created for you';

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                        Email::$smtpSettings['from_address'],
                        $email,
                        null,
                        $subject,
                        Util::getTemplate('_newUser.php', array(
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'email' => $email,
                            'password' => $password,
                            'isCustomer' => true,
                            'clientDomain' => $clientDomain)
                        ),
                        Util::getServerCurrentDateTime());
    }

    public function sendNewUserRepositoryNotificationEmail($clientId, $firstName, $lastName, $username, $password, $email, $repositoryName, $baseURL) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                        Email::$smtpSettings['from_address'],
                        $email,
                        null,
                        Email::$smtpSettings['email_prefix'] . ' ' . 'Ubirimi - You have been granted access to ' . $repositoryName . ' SVN Repository',
                        Util::getTemplate('_newRepositoryUser.php',array('first_name' => $firstName,
                                                                         'last_name' => $lastName,
                                                                         'username' => $username,
                                                                         'password' => $password,
                                                                         'repoName' => $repositoryName,
                                                                         'baseURL' => $baseURL,
                                                                         'clientData' => UbirimiContainer::get()['session']->get('client'))),
                        Util::getServerCurrentDateTime());
    }

    public function sendUserChangedPasswordForRepositoryNotificationEmail($clientId, $firstName, $lastName, $username, $password, $email, $repositoryName, $baseURL) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                        Email::$smtpSettings['from_address'],
                        $email,
                        null,
                        Email::$smtpSettings['email_prefix'] . ' ' . 'Ubirimi - Password change for ' . $repositoryName . ' SVN Repository',
                        Util::getTemplate('_userChangePassword.php', array('first_name' => $firstName,
                                                                                 'last_name' => $lastName,
                                                                                 'username' => $username,
                                                                                 'password' => $password,
                                                                                 'repoName' => $repositoryName,
                                                                                 'baseURL' => $baseURL,
                                                                                 'clientData' => UbirimiContainer::get()['session']->get('client'))),
                        Util::getServerCurrentDateTime());
    }

    public function triggerAssignIssueNotification($clientId, $issue, $oldUserAssignedName, $newUserAssignedName, $project, $loggedInUserId, $comment) {


    }

    public function getMailer($clientId) {

        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if ($smtpSettings['smtp_protocol'] == SMTPServer::PROTOCOL_SECURE_SMTP)
            $smtpSecurity = 'ssl';

        if (isset($smtpSettings['tls_flag']))
            $smtpSecurity = 'tls';

        $transport = Swift_SmtpTransport::newInstance($smtpSettings['hostname'], $smtpSettings['port'], $smtpSecurity)
                            ->setUsername($smtpSettings['username'])
                            ->setPassword($smtpSettings['password']);

        return Swift_Mailer::newInstance($transport);
    }

    /* @TODO: remove when email refactoring has been done */
    private function getEmailHeader($product = null) {
        $text = '<div style="background-color: #F6F6F6; padding: 10px; margin: 10px; width: 720px;">';
        $text .= '<div style="color: #333333;font: 17px Trebuchet MS, sans-serif;white-space: nowrap;padding-bottom: 5px;padding-top: 5px;text-align: left;padding-left: 2px;">';

        $text .= '<a href="https://www.ubirimi.com"><img src="https://www.ubirimi.com/img/email-logo-yongo.png" border="0" /></a>';
        $text .= '<div><img src="https://www.ubirimi.com/img/bg.page.png" /></div>';
        $text .= '</div>';

        return $text;
    }

    private function getEmailFooter() {
        return '</div>';
    }

    public function sendEmailIssueAssign($issue, $clientId, $oldUserAssignedName, $newUserAssignedName, $user, $comment, $loggedInUser) {

    }

    public function sendEmailIssueChanged($issue, $project, $loggedInUser, $clientId, $fieldChanges, $userToNotify) {

    }

    public function triggerIssueUpdatedNotification($clientId, $issue, $loggedInUserId, $changedFields) {


    }

    public function sendEmailNotificationWorkLogged($issue, $clientId, $project, $userToNotify, $extraInformation, $user) {

    }

    public function sendEmailNotificationAddAttachment($issue, $clientId, $project, $userToNotify, $extraInformation, $user) {

    }

    public function sendEmailNotificationWorkLogUpdated($issue, $clientId, $project, $userToNotify, $extraInformation, $user) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        $subject = Email::$smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue Work log Updated " . $issue['project_code'] . '-' . $issue['nr'];

        $date = Util::getServerCurrentDateTime();

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
            Email::$smtpSettings['from_address'],
            $userToNotify['email'],
            null,
            $subject,
            Util::getTemplate('_workLogUpdated.php',array(
                    'issue' => $issue,
                    'project' => $project,
                    'extraInformation' => $extraInformation,
                    'user' => $user)
            ),
            $date);
    }

    public function sendEmailNotificationWorkLogDeleted($issue, $clientId, $project, $userToNotify, $extraInformation, $user) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        $subject = Email::$smtpSettings['email_prefix'] . ' ' . "[Issue] - Issue Work log Deleted " . $issue['project_code'] . '-' . $issue['nr'];

        $date = Util::getServerCurrentDateTime();

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
            Email::$smtpSettings['from_address'],
            $userToNotify['email'],
            null,
            $subject,
            Util::getTemplate('_workLogDeleted.php',array(
                    'issue' => $issue,
                    'project' => $project,
                    'extraInformation' => $extraInformation,
                    'user' => $user)
            ),
            $date);
    }

    public function sendEmailRetrievePassword($clientId, $address, $password) {
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        $tpl = UbirimiContainer::get()['savant'];
        $tpl->assign(array('password' => $password));

        $message = Swift_Message::newInstance('Restore password - Ubirimi.com')
                        ->setFrom(array('support@ubirimi.com'))
                        ->setTo(array($address))
                        ->setBody($tpl->fetch('_restorePassword.php'), 'text/html');

        $mailer = UbirimiContainer::get()['repository']->get(Email::class)->getMailer($smtpSettings);

        $mailer->send($message);
    }

    private function sendEmailDeleteIssue($issue, $clientId, $user, $loggedInUser, $project) {

    }

    public function triggerDeleteIssueNotification($clientId, $issue, $project, $loggedInUser) {

    }

    public function sendFeedback($userData, $like, $improve, $newFeatures, $experience) {

        $text = UbirimiContainer::get()['repository']->get(Email::class)->getEmailHeader();
        $text .= '<div style="color: #333333; font: 17px Trebuchet MS, sans-serif; white-space: nowrap; padding-top: 5px;text-align: left;padding-left: 2px;">' . $userData['first_name'] . ' ' . $userData['last_name'] . ' sent the following feedback: </div>';
        $text .= '<br />';
        $text .= '<table cellpadding="2" cellspacing="0" border="0">';
            $text .= '<tr>';
                $text .= '<td><b>Likes:</b></td>';
                $text .= '<td>' . $like . '</td>';
            $text .= '</tr>';
            $text .= '<tr>';
                $text .= '<td><b>To be improved:</b></td>';
                $text .= '<td>' . $improve . '</td>';
            $text .= '</tr>';
            $text .= '<tr>';
                $text .= '<td><b>New features:</b></td>';
                $text .= '<td>' . $newFeatures . '</td>';
            $text .= '</tr>';
            $text .= '<tr>';
                $text .= '<td><b>Overall experience:</b></td>';
                $text .= '<td>' . $experience . '</td>';
            $text .= '</tr>';

        $text .= '</table>';

        $text .= '<div>User giving feedback: </div>';
        $text .= '<div>Email: ' . $userData['email'] . '</div>';
        $text .= '<div>Client ID: ' . $userData['client_id'] . '</div>';
        $text .= '<div>Username: ' . $userData['username'] . '</div>';

        $text .= UbirimiContainer::get()['repository']->get(Email::class)->getEmailFooter();

        if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
            $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance('Feedback - Ubirimi.com')
                ->setFrom(array('no-reply@ubirimi.com'))
                ->setTo(array('domnulnopcea@gmail.com', 'domnuprofesor@gmail.com'))
                ->setBody($text, 'text/html');

            $mailer->send($message);
        }
    }

    public function shareIssue($clientId, $issue, $userThatShares, $userToSendEmailAddress, $noteContent) {

    }

    public function shareCalendar($clientId, $calendar, $userThatShares, $userToSendEmailAddress, $noteContent) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (Email::$smtpSettings) {
            $subject = Email::$smtpSettings['email_prefix'] . ' ' .
                $userThatShares['first_name'] . ' ' .
                $userThatShares['last_name'] . ' shared calendar ' .
                $calendar['name'] . ' with you';

            $date = Util::getServerCurrentDateTime();

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                Email::$smtpSettings['from_address'],
                $userToSendEmailAddress,
                null,
                $subject,
                Util::getTemplate('_share.php', array('calendar' => $calendar,
                    'userThatShares' => $userThatShares,
                    'noteContent' => $noteContent)),
                $date);
        }
    }

    public function shareEvent($clientId, $event, $userThatShares, $userToSendEmailAddress, $noteContent) {
        Email::$smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (Email::$smtpSettings) {
            $subject = Email::$smtpSettings['email_prefix'] . ' ' .
                $userThatShares['first_name'] . ' ' .
                $userThatShares['last_name'] . ' shared event ' .
                $event['name'] . ' with you';

            $date = Util::getServerCurrentDateTime();

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                Email::$smtpSettings['from_address'],
                $userToSendEmailAddress,
                null,
                $subject,
                Util::getTemplate('_eventShare.php', array('event' => $event,
                    'userThatShares' => $userThatShares,
                    'noteContent' => $noteContent)),
                $date);
        }
    }
}