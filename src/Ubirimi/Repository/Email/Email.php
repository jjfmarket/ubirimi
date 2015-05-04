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