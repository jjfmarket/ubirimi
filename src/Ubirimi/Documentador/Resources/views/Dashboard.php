<?php
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\User\UbirimiUser;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Permission\GlobalPermission;

require_once __DIR__ . '/_header.php';

?>
<body>
    <?php require_once __DIR__ . '/_menu.php'; ?>
    <?php Util::renderBreadCrumb('Dashboard'); ?>
    <div class="pageContent">
        <?php if (Util::checkUserIsLoggedIn()): ?>
            <ul class="nav nav-tabs" style="padding: 0px;">
                <li <?php if ('all-spaces' == $type): ?>class="active"<?php endif ?>>
                    <a href="/documentador/dashboard/all-spaces" title="All Spaces">All Spaces</a>
                </li>
                <li <?php if ('spaces' == $type): ?>class="active"<?php endif ?>>
                    <a href="/documentador/dashboard/spaces" title="Favourite Spaces">Favourite Spaces</a>
                </li>
                <li <?php if ('pages' == $type): ?>class="active" <?php endif ?>>
                    <a href="/documentador/dashboard/pages" title="Pages">Favourite Pages</a>
                </li>
            </ul>
        <?php endif ?>
        <?php if ($type == 'spaces'): ?>
            <?php if (Util::checkUserIsLoggedIn()): ?>
                <?php if (UbirimiContainer::get()['repository']->get(UbirimiUser::class)->hasGlobalPermission($clientId, $loggedInUserId, GlobalPermission::GLOBAL_PERMISSION_DOCUMENTADOR_CREATE_SPACE)): ?>
                    <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
                        <tr>
                            <td><a href="/documentador/administration/spaces/add" class="btn ubirimi-btn"><i class="icon-plus"></i> Create Space</a></td>
                        </tr>
                    </table>
                <?php endif ?>
            <?php endif ?>
            <?php if ($spaces): ?>
                <table  class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($space = $spaces->fetch_array(MYSQLI_ASSOC)): ?>
                            <tr>
                                <td>
                                    <a href="/documentador/pages/<?php echo $space['space_id'] ?>"><?php echo $space['name'] ?></a>
                                </td>
                            </tr>
                        <?php endwhile ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="infoBox">There are no favourite spaces defined.</div>
            <?php endif ?>
        <?php elseif ($type == 'pages'): ?>
            <div style="height: 4px"></div>
            <?php if ($pages): ?>
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($page = $pages->fetch_array(MYSQLI_ASSOC)): ?>
                        <tr>
                            <td><a href="/documentador/page/view/<?php echo $page['id'] ?>"><?php echo $page['name'] ?></a></td>
                        </tr>
                    <?php endwhile ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="infoBox">There are no favourite pages.</div>
            <?php endif ?>
        <?php elseif ($type == 'all-spaces'): ?>
            <div style="height: 4px"></div>
            <?php if ($spaces): ?>
                <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
                    <?php if (UbirimiContainer::get()['repository']->get(UbirimiUser::class)->hasGlobalPermission($clientId, $loggedInUserId, GlobalPermission::GLOBAL_PERMISSION_DOCUMENTADOR_CREATE_SPACE)): ?>
                        <tr>
                            <td><a id="btnNew" href="/documentador/administration/spaces/add" class="btn ubirimi-btn"><i class="icon-plus"></i> Create New Space</a></td>
                        </tr>
                    <?php endif ?>
                </table>
                <?php require_once __DIR__ . '/_listSpaces.php' ?>
            <?php else: ?>
                <div class="infoBox">There are no spaces defined.</div>
            <?php endif ?>
        <?php endif ?>
    </div>
    <?php require_once __DIR__ . '/_footer.php' ?>
</body>