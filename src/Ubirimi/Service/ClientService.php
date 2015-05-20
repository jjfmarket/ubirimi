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
use Ubirimi\Repository\General\UbirimiClient;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\Util;

class ClientService
{
    public function add($pendingClientData)
    {
        try {
            $conn = UbirimiContainer::get()['db.connection'];

            $data = json_decode($pendingClientData['data'], true);

            $clientId = UbirimiContainer::get()['repository']->get(UbirimiClient::class)->create(
                null,
                $data['baseURL'],
                $data['adminEmail'],
                null,
                UbirimiClient::INSTANCE_TYPE_ON_DEMAND,
                Util::getServerCurrentDateTime()
            );

            // create the user
            $userId = UbirimiContainer::get()['repository']->get(UbirimiUser::class)->createAdministratorUser(
                $data['adminFirstName'],
                $data['adminLastName'],
                $data['adminUsername'],
                $data['adminPass'],
                $data['adminEmail'],
                $clientId,
                20, 1, 1,
                Util::getServerCurrentDateTime()
            );

            $columns = 'code#summary#priority#status#created#type#updated#reporter#assignee';
            UbirimiContainer::get()['repository']->get(UbirimiUser::class)->updateDisplayColumns($userId, $columns);

            UbirimiContainer::get()['repository']->get(UbirimiClient::class)->install($clientId);


            $emailContent = UbirimiContainer::get()['template']->render('_newAccount.php', array(
                    'username' => $data['adminUsername'],
                    'companyBaseURL' => $data['baseURL'],
                    'emailAddress' => $data['adminEmail']));

            $messageData = array(
                'from' => 'accounts@ubirimi.com',
                'to' => $data['adminEmail'],
                'clientId' => $clientId,
                'subject' => 'Your account details - Ubirimi.com',
                'content' => $emailContent,
                'date' => Util::getServerCurrentDateTime());

            UbirimiContainer::get()['messageQueue']->send('process_email', json_encode($messageData));

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollback();

            throw new \Exception(
                sprintf('Could not install client [%s]. Error [%s]', $data['baseURL'], $e->getMessage())
            );
        }
    }

    public function delete()
    {

    }
}