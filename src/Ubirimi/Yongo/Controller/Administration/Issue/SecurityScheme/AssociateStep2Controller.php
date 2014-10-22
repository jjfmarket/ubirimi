<?php

namespace Ubirimi\Yongo\Controller\Administration\Issue\SecurityScheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\SecurityScheme;

class AssociateStep2Controller extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();
        $projectId = $request->get('id');
        $schemeId = $request->get('scheme_id');
        $selectedScheme = SecurityScheme::getMetaDataById($schemeId);
        $project = $this->getRepository('yongo.project.project')->getById($projectId);

        $projectIssueSecuritySchemeId = $project['issue_security_scheme_id'];
        $projectIssueSecurityScheme = null;
        if ($projectIssueSecuritySchemeId)
            $projectIssueSecurityScheme = SecurityScheme::getMetaDataById($projectIssueSecuritySchemeId);

        $menuSelectedCategory = 'project';
        $selectedSchemeLevels = SecurityScheme::getLevelsByIssueSecuritySchemeId($schemeId);

        if ($request->request->has('cancel')) {
            return new RedirectResponse('/yongo/administration/project/issue-security/' . $projectId);
        } elseif ($request->request->has('associate')) {
            $oldNewLevel = array();
            foreach ($request->request as $key => $value) {
                if (substr($key, 0, 10) == 'new_level_') {
                    $newSecurityLevel = $request->request->get($key);
                    $oldSecurityLevel = str_replace('new_level_', '', $key);
                    if ($oldSecurityLevel == 0) {
                        $this->getRepository('yongo.project.project')->updateAllIssuesSecurityLevel($projectId, $newSecurityLevel);
                    } else {
                        $oldNewLevel[] = array($oldSecurityLevel, $newSecurityLevel);
                    }
                } else if ($key == 'no_level_set') {
                    $newSecurityLevel = $request->request->get($key);
                    $this->getRepository('yongo.project.project')->updateIssueSecurityLevelForUnsercuredIssues($projectId, $newSecurityLevel);
                }
            }

            if (count($oldNewLevel)) {
                $date = Util::getServerCurrentDateTime();
                $this->getRepository('yongo.project.project')->updateIssuesSecurityLevel($projectId, $oldNewLevel, $date);
            }

            $this->getRepository('yongo.project.project')->setIssueSecuritySchemeId($projectId, $schemeId);

            return new RedirectResponse('/yongo/administration/project/issue-security/' . $projectId);
        }

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Associate Issue Security';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/issue/security_scheme/AssociateStep2.php', get_defined_vars());
    }
}
