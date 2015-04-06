<?php

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Documentador\Repository\Entity\Entity;
use Ubirimi\LinkHelper;
use Ubirimi\SystemProduct;
use Ubirimi\Util;

require_once __DIR__ . '/../_header.php';
?>
<body>
    <?php require_once __DIR__ . '/../_menu.php'; ?>
    <?php
        $breadCrumb = '<a href="/documentador/spaces" class="linkNoUnderline">Spaces</a> > ' . $space['name'] . ' > ' .
            '<a class="linkNoUnderline" href="/documentador/blog/recent/' . $spaceId . '">Recent Blog Posts</a>';

        Util::renderBreadCrumb($breadCrumb);
    ?>

    <div class="doc-left-side">
        <div><a href="/documentador/pages/<?php echo $spaceId ?>"><img src="/documentador/img/pages.png" /> <b>Pages</b></a></div>
        <div><a href="/documentador/blog/recent/<?php echo $spaceId ?>"><img src="/documentador/img/rss.png" /> <b>Blog</b></a></div>

        <div>
            <?php
                if ($pagesInSpace) {
                    $blogPages = UbirimiContainer::get()['repository']->get(Entity::class)->getBlogTreeNavigation($pagesInSpace);
                    foreach ($blogPages as $year => $data) {
                        echo '<div id="header_tree_' . $year . '">';
                        echo '<a href="#"><img style="vertical-align: middle;" id="tree_show_content_year_' . $year . '" src="/documentador/img/arrow_down.png" /></a>' . $year . '<br />';
                        foreach ($data as $month => $pages) {
                            echo '<div style="display:none" id="tree_show_content_month_' . $year . '_' . $month . '">&nbsp;&nbsp;&nbsp;&nbsp; <a href="#"><img style="vertical-align: middle;" src="/documentador/img/arrow_down.png" /></a> ' . $month . '</div>';
                            foreach ($pages as $page) {
                                echo '<div style="display:none" id="tree_month_' . $year . '_' . $month . '_' . $page['id'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bullet; ' . LinkHelper::getDocumentadorPageLink($page['id'], $page['name']) . '</div>';
                            }
                        }
                        echo '</div>';
                    }

                }
            ?>
        </div>
    </div>

    <div class="pageContent" style="overflow: hidden; margin-left: 285px">
        <?php if ($pagesInSpace && $pagesInSpace->num_rows): ?>
            <?php $pagesInSpace->data_seek(0); ?>
            <?php $index = 0; ?>
            <?php while ($page = $pagesInSpace->fetch_array(MYSQLI_ASSOC)): ?>
                <div class="headerPageText"><?php echo LinkHelper::getDocumentadorPageLink($page['id'], $page['name']) ?></div>
                <div>
                    <?php
                        $date = date("F j, Y", strtotime($page['date_created']));
                        echo LinkHelper::getUserProfileLink($page['user_id'], SystemProduct::SYS_PRODUCT_DOCUMENTADOR, $page['first_name'], $page['last_name']) . ' posted on ' . $date;
                    ?>
                </div>
                <div>
                    <?php echo $page['content']; ?>
                </div>
                <div>
                    <?php echo LinkHelper::getDocumentadorPageLink($page['id'], 'Edit', null, 'edit') ?>
                </div>
                <?php if ($pagesInSpace->num_rows - 1 != $index): ?>
                    <hr size="1" />
                <?php endif ?>
                <?php $index++; ?>
            <?php endwhile ?>
        <?php else: ?>
            <div>There are no blog posts</div>
        <?php endif ?>
    </div>
    <?php require_once __DIR__ . '/../_footer.php' ?>
</body>