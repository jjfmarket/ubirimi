<?php

namespace Ubirimi\FrontendCOM\Controller\Administration;

use Ubirimi\UbirimiController;
use Ubirimi\Util;


class ClientsController extends UbirimiController
{
    public function indexAction()
    {
        Util::checkSuperUserIsLoggedIn();

        $clients = $this->getRepository('ubirimi.general.client')->getAll(array('sort_by' => 'client.date_created', 'sort_order' => 'desc'));

        $selectedOption = 'clients';

        return $this->render(__DIR__ . '/../../Resources/views/administration/Clients.php', get_defined_vars());
    }
}
