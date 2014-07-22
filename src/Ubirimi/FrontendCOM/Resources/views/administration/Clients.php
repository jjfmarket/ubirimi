<?php
    use Ubirimi\Repository\User\User;
    use Ubirimi\Repository\Client;
    use Ubirimi\Yongo\Repository\Issue\Issue;

    require_once __DIR__ . '/_header.php';
?>
<body>

<?php require_once __DIR__ . '/_topMenu.php'; ?>
<div class="pageContent">
    <table width="100%" class="headerPageBackground">
        <tr>
            <td>
                <div class="headerPageText">
                    Admin Home > Clients > Overview
                </div>
            </td>
        </tr>
    </table>

    <?php require_once __DIR__ . '/_menu.php' ?>

    <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
        <tr>
            <td><a id="btnDeleteClient" href="#" class="btn ubirimi-btn"><i class="icon-remove"></i> Delete</a></td>
        </tr>
    </table>

    <table class="table table-hover table-condensed">
        <thead>
        <tr>
            <th></th>
            <th>Client</th>
            <th>Stats</th>
            <th>Domain</th>
            <th>Email</th>
            <th>Created at</th>
            <th>Installed</th>
            <th>Last login</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($client = $clients->fetch_array(MYSQLI_ASSOC)): ?>
            <tr id="table_row_<?php echo $client['id'] ?>">
                <td width="22"><input type="checkbox" value="1" id="el_check_<?php echo $client['id'] ?>"/></td>
                <td><?php echo $client['company_name'] ?></td>
                <td>
                    <strong><?php echo count(Client::getProjects($client['id'], 'array')) ?></strong>P
                    <strong>
                        <?php
                            $users = User::getByClientId($client['id']);
                            echo null !== $users ? count($users) : '0';
                        ?></strong>U
                    <strong>
                    <?php
                        $issues = Issue::getByParameters(array('client_id' => $client['id']));
                        echo null !== $issues ? count($issues->fetch_all(MYSQLI_ASSOC)) : '0';
                    ?></strong>I
                </td>
                <td><?php echo $client['company_domain'] ?><br /></td>
                <td><?php echo $client['contact_email'] ?></td>
                <td><?php echo date('d F', strtotime($client['date_created'])) ?><br /></td>
                <td><?php if ($client['installed_flag']) echo 'YES'; else echo 'NO' ?></td>
                <td><?php echo null !== $client['last_login'] ? date('d M Y', strtotime($client['last_login'])) : 'NA' ?></td>
            </tr>
        <?php endwhile ?>
        </tbody>
    </table>

    <div id="modalDeleteClient"></div>
</div>
</body>
