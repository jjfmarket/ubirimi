<?php

namespace Ubirimi\Yongo\Controller\Administration\Issue\SecurityScheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\SecurityScheme;
use Ubirimi\Repository\Log;
use Ubirimi\SystemProduct;

class EditLevelController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $issueSecuritySchemeLevelId = $request->get('id');
        $issueSecuritySchemeLevel = SecurityScheme::getLevelById($issueSecuritySchemeLevelId);
        $issueSecurityScheme = SecurityScheme::getMetaDataById($issueSecuritySchemeLevel['issue_security_scheme_id']);

        if ($issueSecurityScheme['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $emptyName = false;
        if ($request->request->has('edit_issue_security_scheme_level')) {
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $description = Util::cleanRegularInputField($request->request->get('description'));

            if (empty($name))
                $emptyName = true;

            if (!$emptyName) {
                $date = Util::getServerCurrentDateTime();
                SecurityScheme::updateLevelById($issueSecuritySchemeLevelId, $name, $description, $date);

                $this->getRepository('ubirimi.general.log')->add(
                    $session->get('client/id'),
                    SystemProduct::SYS_PRODUCT_YONGO,
                    $session->get('user/id'),
                    'UPDATE Yongo Issue Security Scheme Level ' . $name,
                    $date
                );

                return new RedirectResponse('/yongo/administration/issue-security-scheme-levels/' . $issueSecurityScheme['id']);
            }
        }

        $menuSelectedCategory = 'issue';
        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Update Issue Security Scheme Level';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/issue/security_scheme/EditLevel.php', get_defined_vars());
    }
}
