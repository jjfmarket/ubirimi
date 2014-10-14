<?php

use Ubirimi\Repository\User\User;
use Ubirimi\SystemProduct;
use Ubirimi\Util;

if (Util::checkUserIsLoggedIn()) {
    $clientSettings = $session->get('client/settings');
    $session->set('selected_product_id', SystemProduct::SYS_PRODUCT_DOCUMENTADOR);
} else {
    $httpHOST = Util::getHttpHost();
    $clientId = $this->getRepository('ubirimi.general.client')->getByBaseURL($httpHOST, 'array', 'id');
    $clientSettings = $this->getRepository('ubirimi.general.client')->getById($clientId);
    $loggedInUserId = null;

    $settingsDocumentator = $this->getRepository('ubirimi.general.client')->getDocumentatorSettings($clientId);

    $documentatorUseAnonymous = $settingsDocumentator['anonymous_use_flag'];
    $documentatorAnonymousViewUserProfiles = $settingsDocumentator['anonymous_view_user_profile_flag'];

    if (!($documentatorUseAnonymous && $documentatorAnonymousViewUserProfiles)) {
        Util::signOutAndRedirect();
        die();
    }
}

$userId = $_GET['id'];
$user = $this->getRepository('ubirimi.user.user')->getById($userId);
if ($user['client_id'] != $clientId) {
    header('Location: /general-settings/bad-link-access-denied');
    die();
}

$menuSelectedCategory = 'documentator';

$activities = $this->getRepository('ubirimi.user.user')->getDocumentatorActivityStream($userId);
$sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_DOCUMENTADOR_NAME. ' / ' . $user['first_name'] . ' ' . $user['last_name'] . ' / Activity';

require_once __DIR__ . '/../../Resources/views/user/Activity.php';