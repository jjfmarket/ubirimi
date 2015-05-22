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

namespace Ubirimi\Api\Controller\Issue;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\WorkLog;

class WorklogController extends UbirimiController
{
    public function indexAction(Request $request)
    {
        UbirimiContainer::get()['api.auth']->auth($request);
        $issueId = $request->get('id');

        /* request JSON example
        {
            "author": {
                id: 25412
            },
            "comment": "I did some work here.",
            "started": "2015-05-12",
            "timeSpent": "3h 20m"
        }
        */

        $worklogData = json_decode($request->getContent(), true);

        $this->getRepository(WorkLog::class)->addLog($issueId, $worklogData['author']['id'], $worklogData['timeSpent'], $worklogData['started'], $worklogData['comment'], Util::getServerCurrentDateTime());

        return new Response();
    }
}
