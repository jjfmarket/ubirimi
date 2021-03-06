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

namespace Ubirimi\Yongo\Controller\Administration\IssueTypeScheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\IssueTypeScheme;


class CopyController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $issueTypeSchemeId = $request->get('id');
        $type = $request->get('type');

        $issueTypeScheme = $this->getRepository(IssueTypeScheme::class)->getMetaDataById($issueTypeSchemeId);

        if ($issueTypeScheme['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $emptyName = false;
        $duplicateName = false;

        if ($request->request->has('copy_issue_type_scheme')) {
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $description = Util::cleanRegularInputField($request->request->get('description'));

            if (empty($name)) {
                $emptyName = true;
            }

            $duplicateIssueTypeScheme = $this->getRepository(IssueTypeScheme::class)->getMetaDataByNameAndClientId(
                $session->get('client/id'),
                mb_strtolower($name)
            );

            if ($duplicateIssueTypeScheme)
                $duplicateName = true;

            if (!$emptyName && !$duplicateName) {
                $copiedIssueTypeScheme = new IssueTypeScheme($session->get('client/id'), $name, $description, $type);

                $currentDate = Util::getServerCurrentDateTime();
                $copiedIssueTypeSchemeId = $copiedIssueTypeScheme->save($currentDate);

                $issueTypeSchemeData = $this->getRepository(IssueTypeScheme::class)->getDataById($issueTypeSchemeId);

                while ($issueTypeSchemeData && $data = $issueTypeSchemeData->fetch_array(MYSQLI_ASSOC)) {
                    $copiedIssueTypeScheme->addData($copiedIssueTypeSchemeId, $data['issue_type_id'], $currentDate);
                }

                $this->getLogger()->addInfo('Copy Yongo Issue Type Scheme ' . $issueTypeScheme['name'], $this->getLoggerContext());

                if ('workflow' == $type) {
                    return new RedirectResponse('/yongo/administration/workflows/issue-type-schemes');
                }

                return new RedirectResponse('/yongo/administration/issue-type-schemes');
            }
        }

        $menuSelectedCategory = 'issue';
        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Copy Issue Type Scheme';

        return $this->render(__DIR__ . '/../../../Resources/views/administration/issue/issue_type_scheme/Copy.php', get_defined_vars());
    }
}



