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

namespace Ubirimi\SvnHosting\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\SvnHosting\Repository\SvnRepository;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class DeleteRepositoryController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $Id = $request->request->get('svn_id');

        $repo = $this->getRepository(SvnRepository::class)->getById($Id);

        $this->getRepository(SvnRepository::class)->deleteById($Id);
        $this->getRepository(SvnRepository::class)->updateHtpasswd($repo['id'], $session->get('client/id'));
        $this->getRepository(SvnRepository::class)->updateAuthz();

        /* delete the content from hdd */
        $path = UbirimiContainer::get()['subversion.path'] . $session->get('client/id') . '/' . Util::slugify($repo['name']);
        system("rm -rf $path");

        /* refresh apache config */
        $this->getRepository(SvnRepository::class)->refreshApacheConfig();

        $this->getLogger()->addInfo('DELETE SVN Repository ' . $repo['name'], $this->getLoggerContext());

        return new Response('');
    }
}
