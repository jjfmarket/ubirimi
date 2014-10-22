<?php

namespace Ubirimi\Yongo\Controller\Administration\Project\PermissionScheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Permission\Scheme;

class SelectSchemeController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $projectId = $request->get('id');
        $project = $this->getRepository('yongo.project.project')->getById($projectId);
        if ($project['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        if ($request->request->has('associate')) {

            $permissionSchemeId = $request->request->get('perm_scheme');

            $this->getRepository('yongo.project.project')->updatePermissionScheme($projectId, $permissionSchemeId);

            return new RedirectResponse('/yongo/administration/project/permissions/' . $projectId);
        }

        $permissionSchemes = Scheme::getByClientId($session->get('client/id'));

        $menuSelectedCategory = 'project';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Select Project Permission Scheme';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/project/permission_scheme/Select.php', get_defined_vars());
    }
}
