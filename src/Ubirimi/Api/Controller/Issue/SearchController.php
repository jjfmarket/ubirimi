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

use Symfony\Component\HttpFoundation\JsonResponse;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Issue;


class SearchController extends UbirimiController
{
    public function indexAction()
    {
        $issuesResult = array();

        $getSearchParameters = $this->getRepository(Issue::class)->prepareDataForSearchFromURL($_GET, null);

        $getSearchParameters['page'] = null;

        $parseURLData = parse_url($_SERVER['REQUEST_URI']);
        if (isset($parseURLData['query'])) {
            if (Util::searchQueryNotEmpty($getSearchParameters)) {

                $issuesResultSet = $this->getRepository(Issue::class)->getByParameters($getSearchParameters, null, null, 1);
                while ($data = $issuesResultSet->fetch_array(MYSQLI_ASSOC)) {
                    $issuesResult[] = $data;
                }
            }
        }

        return new JsonResponse($issuesResult);
    }
}
