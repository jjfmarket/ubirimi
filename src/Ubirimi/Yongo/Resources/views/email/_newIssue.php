<?php

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\LinkHelper;
use Ubirimi\SystemProduct;

$session = UbirimiContainer::get()['session'];
?>

<div style="background-color: #ffffff; border-radius: 5px; border: #CCCCCC 1px solid; padding: 10px; margin: 10px;">
    <?php require __DIR__ . '/_header.php'; ?>

    <div style="font: 17px Trebuchet MS, sans-serif;white-space: nowrap;padding-bottom: 5px;padding-top: 5px;text-align: left;padding-left: 2px;">
        <a style="text-decoration: none;" href="<?php echo $session->get('client/base_url') ?>/yongo/issue/<?php echo $issue['id'] ?>"><?php echo $issue['summary'] ?></a>
    </div>
    <div style="height: 10px"></div>

    <table width="100%" border="0">
        <tr>
            <td style="width="80">Project:</td>
            <td><a href="<?php echo $session->get('client/base_url') ?>/yongo/project/<?php echo $issue['issue_project_id'] ?>"><?php echo $issue['project_name'] ?></a></td>
        </tr>
        <tr>
            <td width="150">Issue Type:</td>
            <td><?php echo $issue['type_name'] ?></td>
        </tr>

        <tr>
            <td>Reporter:</td>
            <td>
                <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $issue['reporter'] ?>"><?php echo $issue['ur_first_name'] . ' ' . $issue['ur_last_name'] ?></a>
            </td>
        </tr>
        <tr>
            <td>Assignee:</td>
            <td>
                <?php if ($issue['ua_first_name']): ?>
                <a href="<?php echo $session->get('client/base_url') ?>/yongo/user/profile/<?php echo $issue['assignee'] ?>"><?php echo $issue['ua_first_name'] . ' ' . $issue['ua_last_name'] ?></a>
                <?php else: ?>
                    Unassigned
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td>Created:</td>
            <td><?php echo $issue['date_created'] ?></td>
        </tr>
        <?php if ($issue['due_date']): ?>
        <tr>
            <td>Due:</td>
            <td><?php echo $issue['due_date'] ?></td>
        </tr>
        <?php endif ?>
        <?php if (!empty($issue['description'])): ?>
            <tr>
                <td valign="top" width="80">Description:</td>
                <td><?php echo str_replace("\n",  '<br />', $issue['description']) ?></td>
            </tr>
        <?php endif ?>
        <?php if (!empty($issue['environment'])): ?>
            <tr>
                <td valign="top" width="80">Description:</td>
                <td><?php echo str_replace("\n",  '<br />', $issue['environment']) ?></td>
            </tr>
        <?php endif ?>
        <tr>
            <td>Priority:</td>
            <td><?php echo $issue['priority_name'] ?></td>
        </tr>
        <?php if ($versions_affected): ?>
        <tr>
            <td>Affects version/s:</td>
            <td>
                <?php
                    $arrayString = array();
                    while ($version = $versions_affected->fetch_array(MYSQLI_ASSOC)) {
                        $arrayString[] = $version['name'];
                    }
                ?>
                <?php echo implode($arrayString, ', ') ?>
            </td>
        </tr>
        <?php endif ?>
        <?php if ($versions_fixed): ?>
        <tr>
            <td>Fix versions:</td>
            <td>
                <?php
                    $arrayString = array();
                    while ($version = $versions_fixed->fetch_array(MYSQLI_ASSOC)) {
                        $arrayString[] = $version['name'];
                    }
                ?>
                <?php echo implode($arrayString, ', ') ?>
            </td>
        </tr>
        <?php endif ?>
        <?php if ($components): ?>
        <tr>
            <td>Components:</td>
            <td>
                <?php
                    $arrayString = array();
                    while ($component = $components->fetch_array(MYSQLI_ASSOC)) {
                        $arrayString[] = $component['name'];
                    }
                ?>
                <?php echo implode($arrayString, ', ') ?>
            </td>
        </tr>
        <?php endif ?>
        <?php if ($custom_fields_single_value): ?>
            <?php while ($data = $custom_fields_single_value->fetch_array(MYSQLI_ASSOC)): ?>
                <tr>
                    <td><?php echo $data['name'] ?>:</td>
                    <td><?php echo $data['value'] ?>:</td>
                </tr>
            <?php endwhile ?>
        <?php endif ?>
        <?php if ($custom_fields_user_picker_multiple): ?>
            <?php foreach ($custom_fields_user_picker_multiple as $fieldName => $data): ?>
                <tr>
                    <td><?php echo $data[0]['field_name'] ?>:</td>
                    <td>
                        <?php foreach ($data as $user): ?>
                            <?php echo LinkHelper::getUserProfileLink($user['user_id'], SystemProduct::SYS_PRODUCT_YONGO, $user['first_name'], $user['last_name']) ?>
                        <?php endforeach ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    </table>
</div>
<?php require __DIR__ . '/_footer.php' ?>