<?php
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Permission\GlobalPermission;

require_once __DIR__ . '/../../Resources/views/_header.php';
?>
<body>
    <?php require_once __DIR__ . '/../../Resources/views/_menu.php'; ?>
    <?php Util::renderBreadCrumb('Spaces'); ?>

    <div class="pageContent">
        <?php if (Util::checkUserIsLoggedIn()): ?>
            <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
                <tr>
                    <?php if (UbirimiContainer::get()['repository']->get(UbirimiUser::class)->hasGlobalPermission($clientId, $loggedInUserId, GlobalPermission::GLOBAL_PERMISSION_DOCUMENTADOR_CREATE_SPACE)): ?>
                        <td><a id="btnNew" href="/documentador/administration/spaces/add" class="btn ubirimi-btn"><i class="icon-plus"></i> Create New Space</a></td>
                    <?php endif ?>
                </tr>
            </table>
        <?php endif ?>
        <?php if ($spaces): ?>
            <?php require_once __DIR__ . '/_listSpaces.php' ?>
        <?php else: ?>
            <div class="infoBox">There are no spaces defined.</div>
        <?php endif ?>
    </div>
    <?php require_once __DIR__ . '/../../Resources/views/_footer.php' ?>
</body>