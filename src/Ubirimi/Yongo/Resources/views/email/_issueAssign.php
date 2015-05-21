<?php
use Ubirimi\Container\UbirimiContainer;

$session = UbirimiContainer::get()['session'];
?>

<div style="background-color: #ffffff; border-radius: 5px; border: #CCCCCC 1px solid; padding: 10px; margin: 10px;">
<?php require __DIR__ . '/_header.php'; ?>

    <div style="font: 17px Trebuchet MS, sans-serif;white-space: nowrap;padding-bottom: 5px;padding-top: 5px;text-align: left;padding-left: 2px;">
        <a style="text-decoration: none; " href="<?php echo $session->get('client/base_url') ?>/yongo/issue/<?php echo $issue['id'] ?>"><?php echo $issue['project_code'] ?>-<?php echo $issue['nr'] ?></a> <?php echo $issue['summary'] ?>
    </div>
    <div style="height: 10px"></div>

    <div>Assignee:
        <span style="text-decoration: line-through; background-color: #F78181;"><?php echo $oldUserAssignedName ?></span> <span style="background-color: #BCF5A9;"><?php echo $newUserAssignedName ?></span>
    </div>

    <?php if (isset($comment) && !empty($comment)): ?>
        <div>Comment:</div>
        <?php echo str_replace("\n", '<br />', $comment) ?>
    <?php endif ?>
    <div>Assigned by: <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $loggedInUser['id'] ?>"><?php echo $loggedInUser['first_name'] . ' ' . $loggedInUser['last_name'] ?></a></div>
    <div>
        Project: <a href="<?php echo $session->get('client/base_url') ?>/yongo/project/<?php echo $project['id'] ?>"><?php echo $project['name'] ?></a>
    </div>
</div>

<?php require __DIR__ . '/_footer.php' ?>