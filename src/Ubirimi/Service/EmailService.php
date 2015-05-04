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

namespace Ubirimi\Service;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Email\Email as EmailRepository;
use Ubirimi\Repository\Email\EmailQueue;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Util;

class EmailService extends UbirimiService
{
    public function feedback($userData, $like, $improve, $newFeatures, $experience) {
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($userData['client_id']);

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($userData['client_id'],
            $smtpSettings['from_address'],
            array('domnulnopcea@gmail.com', 'domnuprofesor@gmail.com'),
            null,
            'Feedback - Ubirimi.com',
            Util::getTemplate('_feedback.php',array(
                'userData' => $userData,
                'like' => $like,
                'improve' => $improve,
                'newFeatures' => $newFeatures,
                'experience' => $experience
            )),
            Util::getServerCurrentDateTime());
    }

    public function passwordRestore($clientId, $address, $password) {
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
            $smtpSettings['from_address'],
            $address,
            null,
            'Restore password - Ubirimi.com',
            Util::getTemplate('_restorePassword.php',array(
                    'password' => $password
            )),
            Util::getServerCurrentDateTime());
    }

    public function newRegularUser($clientId, $firstName, $lastName, $email, $username, $password, $clientDomain) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $subject = $clientSmtpSettings['email_prefix'] . ' ' . 'Ubirimi - A new account has been created for you';

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
            $clientSmtpSettings['from_address'],
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

    public function newHelpDeskUser($clientId, $firstName, $lastName, $email, $password, $clientDomain) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $subject = $clientSmtpSettings['email_prefix'] . ' ' . 'Ubirimi - A new customer account has been created for you';

        UbirimiContainer::get()['repository']->get(EmailQueue::class)->add($clientId,
            $clientSmtpSettings['from_address'],
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
}