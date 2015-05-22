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
use Ubirimi\Yongo\Repository\Field\Field;
use Ubirimi\Yongo\Repository\Issue\Issue;

class UpdateController extends UbirimiController
{
    public function indexAction(Request $request)
    {
        UbirimiContainer::get()['api.auth']->auth($request);
        $issueId = $request->get('id');

        $fieldsJSON = json_decode($request->getContent(), true);

        if ($fieldsJSON) {
            $fields = $fieldsJSON['fields'];
            dump($fields);
            foreach ($fields as $fieldKey => $fieldValue) {
                switch ($fieldKey) {
                    case Field::FIELD_SUMMARY_CODE:
                        $this->getRepository(Issue::class)->updateStringField($issueId, Field::FIELD_SUMMARY_CODE, $fieldValue);
                        break;
                    case Field::FIELD_DESCRIPTION_CODE:
                        $this->getRepository(Issue::class)->updateStringField($issueId, Field::FIELD_DESCRIPTION_CODE, $fieldValue);
                        break;
                    case Field::FIELD_ENVIRONMENT_CODE:
                        $this->getRepository(Issue::class)->updateStringField($issueId, Field::FIELD_ENVIRONMENT_CODE, $fieldValue);
                        break;
                    case Field::FIELD_DUE_DATE_CODE:
                        $this->getRepository(Issue::class)->updateStringField($issueId, Field::FIELD_DUE_DATE_CODE, $fieldValue);
                        break;
                }
            }
        }

        return new Response();
    }
}
