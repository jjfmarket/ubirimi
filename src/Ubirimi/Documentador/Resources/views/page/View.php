<?php

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Documentador\Repository\Entity\Entity;
use Ubirimi\Documentador\Repository\Entity\EntityComment;
use Ubirimi\Documentador\Repository\Entity\EntityType;
use Ubirimi\LinkHelper;
use Ubirimi\SystemProduct;
use Ubirimi\Util;

require_once __DIR__ . '/../_header.php';
?>
<body>
    <?php require_once __DIR__ . '/../_menu.php'; ?>
    <?php
        if (EntityType::ENTITY_BLANK_PAGE == $page['documentator_entity_type_id'] || EntityType::ENTITY_FILE_LIST == $page['documentator_entity_type_id']) {
            $breadCrumb = '<a href="/documentador/spaces" class="linkNoUnderline">Spaces</a> > ' . $page['space_name'] . ' > ' .
                '<a class="linkNoUnderline" href="/documentador/pages/' . $spaceId . '">Pages</a> > ';

            if ($parentPage) {
                $breadCrumb .= LinkHelper::getDocumentadorPageLink($parentPage['id'], $parentPage['name'], 'linkNoUnderline') . ' > ';
            }

            $breadCrumb .= $page['name'];

        } else if (EntityType::ENTITY_BLOG_POST == $page['documentator_entity_type_id']) {
            $breadCrumb = '<a href="/documentador/blog/recent/' . $spaceId . '" class="linkNoUnderline">Blog</a> > ' . $pageYear . ' > ' .
                '<a class="linkNoUnderline" href="/documentador/pages/' . $spaceId . '">' . $pageMonth . '</a> > ' . $pageDay . ' > ' . $page['name'];

        }

        Util::renderBreadCrumb($breadCrumb);
    ?>

    <div class="doc-left-side">
        <div><a href="/documentador/pages/<?php echo $spaceId ?>"><img src="/documentador/img/pages.png" /> <b>Pages</b></a></div>
        <div><img src="/documentador/img/rss.png" /> <b>Blog</b></div>

        <div>
            <?php if (EntityType::ENTITY_BLANK_PAGE == $page['documentator_entity_type_id']): ?>
                <?php echo UbirimiContainer::get()['repository']->get(Entity::class)->renderTreeNavigation($treeStructure, 0, 0, true); ?>
            <?php elseif (EntityType::ENTITY_BLOG_POST == $page['documentator_entity_type_id']): ?>
                <?php
                    $blogPages = UbirimiContainer::get()['repository']->get(Entity::class)->getBlogTreeNavigation($pagesInSpace);
                    echo '<div>';

                    foreach ($blogPages as $year => $data) {
                        echo '<div id="header_tree_' . $year . '">';
                        echo '<a href="#"><img style="vertical-align: middle;" id="tree_show_content_year_' . $year . '" src="/documentador/img/arrow_down.png" /></a>' . $year . '<br />';
                        foreach ($data as $month => $pages) {
                            $visibilityYear = ($pageYear == $year) ? 'display: block' : 'display: none';
                            echo '<div style="' . $visibilityYear . '" id="tree_show_content_month_' . $year . '_' . $month . '">&nbsp;&nbsp;&nbsp;&nbsp; <a href="#"><img style="vertical-align: middle;" src="/documentador/img/arrow_down.png" /></a> ' . $month . '</div>';
                            foreach ($pages as $page) {
                                $visibilityMonth = ($pageYear == $year && $pageMonth == $month) ? 'display: block' : 'display: none';
                                echo '<div style="' . $visibilityMonth . '" id="tree_month_' . $year . '_' . $month . '_' . $page['id'] . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bullet; ' . LinkHelper::getDocumentadorPageLink($page['id'], $page['name']) . '</div>';
                            }
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                ?>
            <?php endif ?>
        </div>
    </div>

    <div class="pageContent" style="overflow: hidden; margin-left: 285px">
        <?php if ($page): ?>
            <?php if (Util::checkUserIsLoggedIn()): ?>
                <?php require_once __DIR__ . '/_buttons.php' ?>
            <?php endif ?>
            <?php
                $lastEditedText = ' last edited by ';

                if ($lastRevision) {
                    $date = date("F j, Y", strtotime($lastRevision['date_created']));
                    $lastEditedText .= LinkHelper::getUserProfileLink($lastRevision['user_id'], SystemProduct::SYS_PRODUCT_DOCUMENTADOR, $lastRevision['first_name'], $lastRevision['last_name']) . ' on ' . $date;
                } else {
                    $date = date("F j, Y", strtotime($page['date_created']));
                    $lastEditedText .= LinkHelper::getUserProfileLink($page['user_id'], SystemProduct::SYS_PRODUCT_DOCUMENTADOR, $page['first_name'], $page['last_name']) . ' on ' . $date;
                }

                $linkAttachments = '';
                if ($attachments)
                    $linkAttachments = '<a href="/documentador/page/attachments/' . $entityId . '"><img border="0" src="/img/attachment.png" height="10px" /></a> <a href="/documentador/page/attachments/' . $entityId . '">' . $attachments->num_rows . '</a>';
            ?>
            <div class="smallDescription"><?php echo $linkAttachments ?> Added by <?php echo LinkHelper::getUserProfileLink($page['user_id'], SystemProduct::SYS_PRODUCT_DOCUMENTADOR, $page['first_name'], $page['last_name']) ?>, <?php echo $lastEditedText ?></div>

            <div>
                <?php
                    if ($revisionId) {
                        echo '<div class="infoBox">';
                        echo '<div>You are viewing an old version of this page. View the <a href="/documentador/page/view/' . $entityId . '">current version</a>.</div>';
                        echo '<div>Restore this Version | <a href="/documentador/page/history/' . $entityId . '">View Page History</a></div>';
                        echo '</div>';
                        echo $revision['content'];
                    } else {
                        echo $page['content'];
                    }
                ?>
            </div>

            <?php if ($page['documentator_entity_type_id'] == EntityType::ENTITY_FILE_LIST): ?>
                <?php if ($pageFiles): ?>
                    <br />
                    <?php require_once __DIR__ . '/_listFiles.php' ?>
                <?php endif ?>
                <br />
                <form name="page_upload_file" method="post" enctype="multipart/form-data" action="/documentador/entity/upload/<?php echo $entityId ?>">
                    <div style="border: dashed blue 1px; padding: 8px">
                        <div>To upload more files click the button bellow and then press Add Files</div>
                        <input style="padding: 4px" name="entity_upload_file[]" type="file" multiple="" value="Upload Files"/>
                        <input class="btn ubirimi-btn" type="submit" value="Add Files" />
                    </div>
                </form>
            <?php endif ?>
            <br />
            <div>
                <?php if ($childPages): ?>
                    <?php require_once __DIR__ . '/_listChildPagesSmall.php' ?>
                <?php endif ?>

                <div id="pageCommentsSection" style="display: block; clear: both;">
                    <?php if ($childPages && $comments): ?>
                        <br />
                    <?php endif ?>

                    <?php if ($comments): ?>
                        <div class="headerPageText" style="border-bottom: 1px solid #DDDDDD;"><?php echo count($comments) ?> Comment<?php if (count($comments) > 1) echo 's' ?></div>
                        <div style="float: left; display: block; width: 100%">
                            <?php
                                $htmlLayout = '';
                                UbirimiContainer::get()['repository']->get(EntityComment::class)->getCommentsLayoutHTML($comments, $htmlLayout, null, 0);
                                echo $htmlLayout;
                            ?>
                        </div>
                    <?php endif ?>
                </div>

                <div style="display: block; clear: both;">
                    <br />
                    <?php if (Util::checkUserIsLoggedIn()): ?>
                        <textarea class="inputTextAreaLarge" id="doc_view_page_add_comment_content" style="width: 100%">Add a comment</textarea>
                        <div style="height: 2px"></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <div>
                                        <input type="button" name="add_comment" id="doc_view_page_add_comment" value="Add Comment" class="btn ubirimi-btn"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    <?php endif ?>
                </div>
            </div>
            <?php if (Util::checkUserIsLoggedIn()): ?>
                <?php require_once __DIR__ . '/_childPagesSubmenu.php' ?>

                <input type="hidden" value="<?php echo $entityId ?>" id="entity_id" />
                <input type="hidden" value="<?php echo $spaceId ?>" id="space_id" />
                <div class="ubirimiModalDialog" id="modalDeleteComment"></div>
                <div class="ubirimiModalDialog" id="modalRemovePage"></div>
                <div class="ubirimiModalDialog" id="modalDeleteFile"></div>
            <?php endif ?>
        <?php else: ?>
            <div class="infoBox">This page does not exist.</div>
        <?php endif ?>
    </div>
    <?php require_once __DIR__ . '/../_footer.php' ?>
</body>