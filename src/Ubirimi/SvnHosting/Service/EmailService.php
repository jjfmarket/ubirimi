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

namespace Ubirimi\SvnHosting\Service;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Service\UbirimiService;
use Ubirimi\Util;

class EmailService extends UbirimiService
{
    public function passwordUpdate($clientId, $repositoryName, $user, $password, $baseURL)
    {
        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $emailContent = UbirimiContainer::get()['template']->render('_userChangePassword.php', array(
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'username' => $user['username'],
            'password' => $password,
            'repoName' => $repositoryName,
            'baseURL' => $baseURL,
            'clientId' => $clientId
        ));

        $messageData = array(
            'from' => $clientSmtpSettings['from_address'],
            'to' => $user['email'],
            'clientId' => $clientId,
            'subject' => $clientSmtpSettings['email_prefix'] . ' ' . 'Ubirimi - Password change for ' . $repositoryName . ' SVN Repository',
            'content' => $emailContent,
            'date' => Util::getServerCurrentDateTime());

        UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
    }

    public function newUserRepository($clientId, $firstName, $lastName, $username, $password, $email, $repositoryName, $baseURL) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $emailContent = UbirimiContainer::get()['template']->render('_newRepositoryUser.php', array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'password' => $password,
            'repoName' => $repositoryName,
            'baseURL' => $baseURL,
            'clientData' => UbirimiContainer::get()['session']->get('client')));

        $messageData = array(
            'from' => $clientSmtpSettings['from_address'],
            'to' => $email,
            'clientId' => $clientId,
            'subject' => $clientSmtpSettings['email_prefix'] . ' ' . 'Ubirimi - You have been granted access to ' . $repositoryName . ' SVN Repository',
            'content' => $emailContent,
            'date' => Util::getServerCurrentDateTime());

        UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
    }
}