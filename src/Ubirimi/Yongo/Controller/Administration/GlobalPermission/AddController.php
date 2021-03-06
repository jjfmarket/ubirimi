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

namespace Ubirimi\Yongo\Controller\Administration\GlobalPermission;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Repository\User\UbirimiGroup;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Permission\GlobalPermission;

class AddController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $allGroups = $this->getRepository(UbirimiGroup::class)->getByClientIdAndProductId($session->get('client/id'), SystemProduct::SYS_PRODUCT_YONGO);
        $globalPermissions = $this->getRepository(GlobalPermission::class)->getByProductId(SystemProduct::SYS_PRODUCT_YONGO);

        if ($request->request->has('confirm_new_permission')) {
            $permissionId = $request->request->get('permission');
            $groupId = $request->request->get('group');
            $currentDate = Util::getServerCurrentDateTime();
            $group = $this->getRepository(UbirimiGroup::class)->getMetadataById($groupId);
            $permission = $this->getRepository(GlobalPermission::class)->getById($permissionId);

            $date = Util::getServerCurrentDateTime();

            // check if the group is already added
            $permissionData = $this->getRepository(GlobalPermission::class)->getDataByPermissionIdAndGroupId(
                $session->get('client/id'),
                $permissionId,
                $groupId
            );

            if (!$permissionData) {
                $this->getRepository(GlobalPermission::class)->addDataForGroupId($session->get('client/id'), $permissionId, $groupId, $date);

                $this->getLogger()->addInfo('ADD Yongo Global Permission ' . $permission['name'] . ' to group ' . $group['name'], $this->getLoggerContext());
            }

            return new RedirectResponse('/yongo/administration/global-permissions');
        }

        $menuSelectedCategory = 'user';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Create Global Permission';

        return $this->render(__DIR__ . '/../../../Resources/views/administration/global_permission/Add.php', get_defined_vars());
    }
}
