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
    public function newUser($firstName, $lastName, $username, $password, $email, $clientDomain, $clientId)
    {
        UbirimiContainer::get()['repository']->get(EmailRepository::class)->sendNewUserNotificationEmail($clientId, $firstName, $lastName, $username, $password, $email, $clientDomain);
    }

    public function newUserCustomer($firstName, $lastName, $password, $email, $clientDomain, $clientId)
    {
        UbirimiContainer::get()['repository']->get(EmailRepository::class)->sendNewCustomerNotificationEmail($clientId, $firstName, $lastName, $email, $password, $clientDomain);
    }

    public function feedback($userData, $like, $improve, $newFeatures, $experience)
    {
        UbirimiContainer::get()['repository']->get(EmailRepository::class)->sendFeedback($userData, $like, $improve, $newFeatures, $experience);
    }

    public function passwordRecover($clientId, $email, $password)
    {
    }

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

    public function emailFeedback($userData, $like, $improve, $newFeatures, $experience) {
        $text = $this->getEmailHeader();
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

        $text .= $this->getEmailFooter();

        $transport = \Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
        $mailer = \Swift_Mailer::newInstance($transport);
        $message = \Swift_Message::newInstance('Feedback - Ubirimi.com')
            ->setFrom(array('no-reply@ubirimi.com'))
            ->setTo(array('domnulnopcea@gmail.com', 'domnuprofesor@gmail.com'))
            ->setBody($text, 'text/html');

        $mailer->send($message);
    }

    public function emailPasswordRecover($clientId, $address, $password) {
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
}