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

namespace Ubirimi\Yongo\Controller\Administration\Project\IssueTypeFieldConfiguration;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\FieldConfigurationScheme;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class SelectSchemeController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $projectId = $request->get('id');
        $project = $this->getRepository(YongoProject::class)->getById($projectId);
        if ($project['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }
        $fieldConfigurationSchemes = $this->getRepository(FieldConfigurationScheme::class)->getByClient($session->get('client/id'));

        $menuSelectedCategory = 'project';

        if ($request->request->has('associate')) {
            $issueTypeFieldSchemeId = $request->request->get('issue_type_field_scheme');
            $this->getRepository(YongoProject::class)->updateFieldConfigurationScheme($projectId, $issueTypeFieldSchemeId);

            return new RedirectResponse('/yongo/administration/project/fields/' . $projectId);
        }

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Select Issue Type Field Configuration';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/project/SelectIssueTypeFieldScheme.php', get_defined_vars());
    }
}
