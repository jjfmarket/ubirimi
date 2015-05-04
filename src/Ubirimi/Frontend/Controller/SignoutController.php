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
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class SignoutController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        if ($session->has('client/id')) {
            $clientBaseURL = $session->get('client/base_url');
            $this->getLogger()->addInfo('LOG OUT', $this->getLoggerContext());
            $session->invalidate();
        } else {
            // session not active anymore
            $clientBaseURL = Util::getHttpHost() . '/?login=true';
        }

        return new RedirectResponse($clientBaseURL);
    }
}
