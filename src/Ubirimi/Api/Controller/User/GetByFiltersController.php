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

namespace Ubirimi\Api\Controller\User;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\UbirimiController;

class GetByFiltersController extends UbirimiController
{
    public function indexAction(Request $request)
    {
        $filters = json_decode($request->getContent(), true);
        $result = array();

        if (array_key_exists('email_address', $filters) && array_key_exists('is_administrator', $filters)) {
            $result = $this->getRepository(UbirimiUser::class)->getByEmailAddressAndIsClientAdministrator(mb_strtolower($filters['email_address']));
        }

        if (array_key_exists('username', $filters) && array_key_exists('domain', $filters)) {
            $baseURL = 'http://' . $filters['domain'] . '.ubirimi.net';
            $result = $this->getRepository(UbirimiUser::class)->getByUsernameAndBaseURL(mb_strtolower($filters['username']), mb_strtolower($baseURL));
        }

        return new JsonResponse($result);
    }
}
