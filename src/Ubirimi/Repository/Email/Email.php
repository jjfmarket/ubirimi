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