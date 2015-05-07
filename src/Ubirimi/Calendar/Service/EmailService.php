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

namespace Ubirimi\Calendar\Service;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Email\Email;
use Ubirimi\Repository\Email\EmailQueue;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\Service\UbirimiService;
use Ubirimi\Util;

class EmailService extends UbirimiService
{
    public function shareCalendar($calendar, $userThatShares, $usersToShareWith, $noteContent)
    {
        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($calendar['client_id']);

        if (!$clientSmtpSettings) {
            return;
        }

        $usersToShareWithCount = count($usersToShareWith);
        for ($i = 0; $i < $usersToShareWithCount; $i++) {
            $user = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($usersToShareWith[$i]);
            $subject = $clientSmtpSettings['email_prefix'] . ' ' .
            $userThatShares['first_name'] . ' ' .
            $userThatShares['last_name'] . ' shared calendar ' .
            $calendar['name'] . ' with you';

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($calendar['client_id'],
                $clientSmtpSettings['from_address'],
                $user['email'],
                null,
                $subject,
                Util::getTemplate('_share.php', array('calendar' => $calendar,
                                                      'userThatShares' => $userThatShares,
                                                      'noteContent' => $noteContent)),
                Util::getServerCurrentDateTime());
        }

    }

    public function shareEvent($clientId, $event, $userThatShares, $usersToShareWith, $noteContent)
    {
        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $usersToShareWithCount = count($usersToShareWith);
        for ($i = 0; $i < $usersToShareWithCount; $i++) {
            $user = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->getById($usersToShareWith[$i]);
            $subject = $clientSmtpSettings['email_prefix'] . ' ' .
            $userThatShares['first_name'] . ' ' .
            $userThatShares['last_name'] . ' shared event ' .
            $event['name'] . ' with you';

            UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
                $clientSmtpSettings['from_address'],
                $user['email'],
                null,
                $subject,
                Util::getTemplate('_eventShare.php', array('event' => $event,
                                                           'userThatShares' => $userThatShares,
                                                           'noteContent' => $noteContent)),
                Util::getServerCurrentDateTime());

        }
    }
}