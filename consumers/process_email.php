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

use PhpAmqpLib\Connection\AMQPLazyConnection;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\SMTPServer;

require_once __DIR__ . '/../web/bootstrap_cli.php';

$connection = new AMQPLazyConnection(UbirimiContainer::get()['rmq.host'], UbirimiContainer::get()['rmq.port'], UbirimiContainer::get()['rmq.user'], UbirimiContainer::get()['rmq.pass']);
$channel = $connection->channel();
$channel->queue_declare('process_email', false, false, false, false);

$callback = function($msg) {
    $messageData = json_decode($msg->body, true);
    $smtpSettings = UbirimiContainer::get()['repository']->get(SMTPServer::class)->getByClientId($messageData['clientId']);
    $mailer = UbirimiContainer::get()['email']->getMailer($smtpSettings);

    $message = Swift_Message::newInstance($messageData['subject'])
        ->setFrom(array($messageData['from']))
        ->setTo(array($messageData['to']))
        ->setBody($messageData['content'], 'text/html');

    @$mailer->send($message);
};

$channel->basic_consume('process_email', '', false, true, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();