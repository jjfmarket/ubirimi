<?php

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Yongo\Repository\Issue\IssueAttachment;

$session = UbirimiContainer::get()['session'];
$attachmentIds = $extraInformation['attachmentIds'];
$attachmentNames = array();
for ($i = 0; $i < count($attachmentIds); $i++) {
    $attachment = UbirimiContainer::get()['repository']->get(IssueAttachment::class)->getById($attachmentIds[$i]);
    $attachmentNames[] = $attachment['name'];
}
?>
<div style="background-color: #ffffff; border-radius: 5px; border: #CCCCCC 1px solid; padding: 10px; margin: 10px;">
    <?php require __DIR__ . '/_header.php'; ?>
    <div style="padding-top: 5px; color: #333333;font: 17px Trebuchet MS, sans-serif;white-space: nowrap;padding-bottom: 5px;text-align: left;padding-left: 2px;">
        <a style="text-decoration: none; " href="<?php echo $session->get('client/base_url') ?>/yongo/issue/<?php echo $issue['id'] ?>"><?php echo $issue['summary'] ?></a>
        <br />
    </div>

    <div style="height: 10px"></div>
    <div>Changed by: <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $user['id'] ?>"><?php echo $user['first_name'] . ' ' . $user['last_name'] ?></a></div>
    <div>Project: <a href="<?php echo $session->get('client/base_url') ?>/yongo/project/<?php echo $project['id'] ?>"><?php echo $project['name'] ?></a></div>
    <?php if (!empty($extraInformation['comment'])): ?>
        <div>Comment by: <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $user['id'] ?>"><?php echo $user['first_name'] . ' ' . $user['last_name'] ?></a></div>

        <div><?php echo str_replace("\n", '<br />', $extraInformation['comment']) ?></div>
    <?php endif ?>
    <div><?php if (1 === count($attachmentNames)) echo 'Attachment:'; else echo 'Attachments:' ?> <?php echo implode(', ', $attachmentNames) ?></div>
</div>

<?php require '_footer.php' ?>