<?php
use Ubirimi\Container\UbirimiContainer;

$session = UbirimiContainer::get()['session'];
?>

<div style="background-color: #ffffff; border-radius: 5px; border: #CCCCCC 1px solid; padding: 10px; margin: 10px;">
    <?php require __DIR__ . '/_header.php'; ?>

    <div style="font: 17px Trebuchet MS, sans-serif;white-space: nowrap;padding-bottom: 5px;padding-top: 5px;text-align: left;padding-left: 2px;">
        <a style="text-decoration: none;" href="<?php echo $session->get('client/base_url') ?>/yongo/issue/<?php echo $issue['id'] ?>"><?php echo $issue['summary'] ?></a>
    </div>
    <div style="height: 10px"></div>

    <div>This issue has been deleted.</div>
    <div style="height: 10px"></div>

    <div>Deleted by: <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $loggedInUser['id'] ?>"><?php echo $loggedInUser['first_name'] . ' ' . $loggedInUser['last_name'] ?></a></div>
    <div>
        Project: <a href="<?php echo $session->get('client/base_url') ?>/yongo/project/<?php echo $project['id'] ?>"><?php echo $project['name'] ?></a>
    </div>
</div>

<?php require_once __DIR__ . '/_footer.php' ?>