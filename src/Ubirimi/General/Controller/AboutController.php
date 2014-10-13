<?php

namespace Ubirimi\General\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Repository\Client;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class AboutController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        if (Util::checkUserIsLoggedIn()) {
            $clientSettings = $session->get('client/settings');
        } else {
            $clientId = $this->getRepository('ubirimi.general.client')->getClientIdAnonymous();
            $clientSettings = $this->getRepository('ubirimi.general.client')->getSettings($clientId);
            $loggedInUserId = null;
        }

        $session->set('selected_product_id', -2);

        $menuSelectedCategory = 'ubirimi_about';

        $sectionPageTitle = $clientSettings['title_name'] . ' / About Ubirimi';

        return $this->render(__DIR__ . '/../Resources/views/About.php', get_defined_vars());
    }
}


