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

namespace Ubirimi\Frontend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\General\UbirimiClient;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Permission\Permission;

class SigninController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        $content = 'Signin.php';
        $signInError = null;

        $httpHOST = Util::getHttpHost();

        $clientSettings = $this->getRepository(UbirimiClient::class)->getSettingsByBaseURL($httpHOST);
        $clientId = $clientSettings['id'];
        $client = $this->getRepository(UbirimiClient::class)->getById($clientId);

        if ($session->has('user') && $httpHOST == $session->get('client/base_url')) {
            return new RedirectResponse($httpHOST . '/yongo/dashboard');
        }

        $context = $request->get('context');

        $loginParameter = $request->get('login');
        // check if this client has projects that can be browsed anonymously. if yes redirect to yongo dashboard

        $projects = $this->getRepository(UbirimiClient::class)->getProjectsByPermission(
            $clientId,
            null,
            Permission::PERM_BROWSE_PROJECTS,
            'array'
        );

        if ($loginParameter !== 'true' && count($projects)) {
            return new RedirectResponse($httpHOST . '/yongo/dashboard');
        }

        if ($request->request->has('sign_in')) {

            $username = $request->request->get('username');
            $password = $request->request->get('password');

            $userData = $this->getRepository(UbirimiUser::class)->getByUsernameAndClientId($username, $clientId);
            if ($userData['id']) {
                if (UbirimiContainer::get()['password']->check($password, $userData['password'])) {
                    $session->invalidate();
                    $clientId = $userData['client_id'];

                    UbirimiContainer::get()['warmup']->warmUpClient($userData, true, true);
                    UbirimiContainer::get()['login.time']->userSaveLoginTime($userData['id']);

                    $date = Util::getServerCurrentDateTime();
                    $this->getLogger()->addInfo('LOG IN', $this->getLoggerContext());

                    if ($context) {
                        return new RedirectResponse($httpHOST . $context);
                    } else {
                        return new RedirectResponse($httpHOST . '/yongo/dashboard');
                    }

                } else {
                    $signInError = true;
                }
            } else {
                $signInError = true;
            }
        } else if ($request->request->has('create_account')) {
            return new RedirectResponse('/sign-up');
        }

        return $this->render(__DIR__ . '/../Resources/views/_main.php', get_defined_vars());
    }
}
