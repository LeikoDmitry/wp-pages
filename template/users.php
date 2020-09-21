<?php
$users = get_query_var(WORDPRESS_TYPE_CODE_QUERY_VAR, []);
?>
<table class="table table-striped">
    <tr>
        <td>ID</td>
        <td>Name</td>
        <td>Username</td>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td>
                <a data-toggle="modal" href="#userModal" class="text-decoration-underline text-body"
                   data-user_id="<?php echo ($user->id ?? ''); ?>" class="user-detail"><?php echo ($user->id ?? ''); ?>
                </a>
            </td>
            <td>
                <a data-toggle="modal" href="#userModal" class="text-decoration-underline text-body"
                   data-user_id="<?php echo ($user->id ?? ''); ?>" class="user-detail"><?php echo ($user->name ?? ''); ?>
                </a>
            </td>
            <td>
                <a data-toggle="modal" href="#userModal" class="text-decoration-underline text-body"
                   data-user_id="<?php echo ($user->id ?? ''); ?>"
                   class="user-detail"><?php echo ($user->username ?? ''); ?>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<!-- Modal -->
<?php include_once 'dialog.php'; ?>
