<?php

    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();
    $content = Util::cleanRegularInputField($_POST['content']);
    $pageId = $_POST['entity_id'];
    $parentId = $_POST['parent_comment_id'];
    $date = Util::getServerCurrentDateTime();

    EntityComment::addComment($pageId, $loggedInUserId, $content, $date, $parentId);