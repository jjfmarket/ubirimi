<?php require_once __DIR__ . '/../../../GeneralSettings/Resources/views/email/_header.php' ?>

<div style="background-color: #F6F6F6; padding: 10px; margin: 10px; width: 720px;">
    <div style="color: #333333; font: 17px Trebuchet MS, sans-serif; white-space: nowrap; padding-top: 5px;text-align: left;padding-left: 2px;"><?php echo $userData['first_name'] ?> <?php echo $userData['last_name'] ?> sent the following feedback: </div>
    <br />
    <table cellpadding="2" cellspacing="0" border="0">
        <tr>
            <td><b>Likes:</b></td>
            <td><?php echo $like ?></td>
            </tr>
        <tr>
            <td><b>To be improved:</b></td>
            <td><?php echo $improve ?></td>
            </tr>
        <tr>
            <td><b>New features:</b></td>
            <td><?php echo $newFeatures ?></td>
            </tr>
        <tr>
            <td><b>Overall experience:</b></td>
            <td><?php echo $experience ?></td>
            </tr>

        </table>

    <div>User giving feedback: </div>
    <div>Email: <?php echo $userData['email'] ?></div>
    <div>Client ID: <?php echo $userData['client_id'] ?></div>
    <div>Username: <?php echo $userData['username'] ?></div>

</div>

<?php require_once __DIR__ . '/../../../GeneralSettings/Resources/views/email/_footer.php' ?>