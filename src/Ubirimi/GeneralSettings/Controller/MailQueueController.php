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

namespace Ubirimi\GeneralSettings\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Repository\Email\EmailQueue;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class MailQueueController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();
        $clientId = $session->get('client/id');

        $menuSelectedCategory = 'general_mail';
        $session->set('selected_product_id', -1);

        $total = 0;
        $mailsInQueue = $this->getRepository(EmailQueue::class)->getByClientId($clientId);
        if ($mailsInQueue) {
            $total = $mailsInQueue->num_rows;
        }

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / GeneralSettings Settings / Mail Queue';

        return $this->render(__DIR__ . '/../Resources/views/MailQueue.php', get_defined_vars());
    }
}