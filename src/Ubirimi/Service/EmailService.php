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

namespace Ubirimi\Service;

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Util;

class EmailService extends UbirimiService
{

    public function getMailer($smtpSettings) {
        $smtpSecurity = null;

        if ($smtpSettings['smtp_protocol'] == SMTPServer::PROTOCOL_SECURE_SMTP) {
            $smtpSecurity = 'ssl';
        }
        if (isset($smtpSettings['tls_flag'])) {
            $smtpSecurity = 'tls';
        }

        $transport = \Swift_SmtpTransport::newInstance($smtpSettings['hostname'], $smtpSettings['port'], $smtpSecurity)
                ->setUsername($smtpSettings['username'])
                ->setPassword($smtpSettings['password']);

        return \Swift_Mailer::newInstance($transport);
    }

    public function feedback($userData, $like, $improve, $newFeatures, $experience) {
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($userData['client_id']);

        $emailContent = UbirimiContainer::get()['template']->render('_feedback.php', array(
            'userData' => $userData,
            'like' => $like,
            'improve' => $improve,
            'newFeatures' => $newFeatures,
            'experience' => $experience
        ));

        $messageData = array(
            'from' => $smtpSettings['from_address'],
            'to' => 'support@ubirimi.com',
            'clientId' => $userData['client_id'],
            'subject' => 'Feedback - Ubirimi.com',
            'content' => $emailContent,
            'date' => Util::getServerCurrentDateTime());

        UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
    }

    public function passwordRestore($clientId, $address, $password) {
        $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        $emailContent = UbirimiContainer::get()['template']->render('_restorePassword.php', array(
            'password' => $password
        ));

        $messageData = array(
            'from' => $smtpSettings['from_address'],
            'to' => $address,
            'clientId' => $clientId,
            'subject' => 'Restore password - Ubirimi.com',
            'content' => $emailContent,
            'date' => Util::getServerCurrentDateTime());

        UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
    }

    public function newRegularUser($clientId, $firstName, $lastName, $email, $username, $password, $clientDomain) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $emailContent = UbirimiContainer::get()['template']->render('_newUser.php', array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'username' => $username,
            'password' => $password,
            'clientDomain' => $clientDomain));

        $messageData = array(
            'from' => $clientSmtpSettings['from_address'],
            'to' => $email,
            'clientId' => $clientId,
            'subject' => $clientSmtpSettings['email_prefix'] . ' ' . 'Ubirimi - A new account has been created for you',
            'content' => $emailContent,
            'date' => Util::getServerCurrentDateTime());

        UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
    }

    public function newHelpDeskUser($clientId, $firstName, $lastName, $email, $password, $clientDomain) {

        $clientSmtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($clientId);

        if (!$clientSmtpSettings) {
            return;
        }

        $emailContent = UbirimiContainer::get()['template']->render('_newUser.php', array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password' => $password,
            'isCustomer' => true,
            'clientDomain' => $clientDomain));

        $messageData = array(
            'from' => $clientSmtpSettings['from_address'],
            'to' => $email,
            'clientId' => $clientId,
            'subject' => $clientSmtpSettings['email_prefix'] . ' ' . 'Ubirimi - A new customer account has been created for you',
            'content' => $emailContent,
            'date' => Util::getServerCurrentDateTime());

        UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));
    }
}