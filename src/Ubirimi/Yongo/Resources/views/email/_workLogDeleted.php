<?php
use Ubirimi\Container\UbirimiContainer;

$session = UbirimiContainer::get()['session'];
?>
    <div style="background-color: #ffffff; border-radius: 5px; border: #CCCCCC 1px solid; padding: 10px; margin: 10px;">
        <?php require __DIR__ . '/_header.php'; ?>
        <div style="padding-top: 5px; color: #333333;font: 17px Trebuchet MS, sans-serif;white-space: nowrap;padding-bottom: 5px;text-align: left;padding-left: 2px;">
            <span>Worklog deleted on <a style="text-decoration: none; " href="<?php echo $session->get('client/base_url') ?>/yongo/issue/<?php echo $issue['id'] ?>"><?php echo $issue['project_code'] ?>-<?php echo $issue['nr'] ?></a> </span><?php echo $issue['summary'] ?>
            <br />
        </div>

        <div style="height: 10px"></div>
        <div>Project: <a href="<?php echo $session->get('client/base_url') ?>/yongo/project/<?php echo $project['id'] ?>"><?php echo $project['name'] ?></a></div>
        <div>Changed by: <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $user['id'] ?>"><?php echo $user['first_name'] . ' ' . $user['last_name'] ?></a></div>
        <div>Time Spent: <?php echo $extraInformation['time_spent']; ?></div>
        <div>Remaining Estimate: <?php echo $extraInformation['remaining_estimate']; ?></div>
    </div>

<?php require '_footer.php' ?>