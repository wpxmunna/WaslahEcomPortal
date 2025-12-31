<div class="page-header">
    <h1>Edit Admin User</h1>
    <a href="<?= url('admin/users') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<?php if ($isCurrentUser): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    You are editing your own account. Some options are restricted.
</div>
<?php endif; ?>

<form action="<?= url('admin/users/update/' . $user['id']) ?>" method="POST">
    <?= csrfField() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">User Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" required
                                   value="<?= sanitize($user['name']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control" name="email" required
                                   value="<?= sanitize($user['email']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone"
                               value="<?= sanitize($user['phone'] ?? '') ?>">
                    </div>

                    <hr>
                    <h6 class="text-muted mb-3">Change Password (leave blank to keep current)</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password"
                                   minlength="6" placeholder="Enter new password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                   minlength="6" placeholder="Confirm new password">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Role & Status</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select class="form-select" name="role" required <?= $isCurrentUser ? 'disabled' : '' ?>>
                            <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $user['role'] === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($isCurrentUser): ?>
                        <input type="hidden" name="role" value="<?= $user['role'] ?>">
                        <small class="text-warning">You cannot change your own role</small>
                        <?php else: ?>
                        <small class="text-muted">
                            Administrators have full access. Managers have limited access.
                        </small>
                        <?php endif; ?>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1"
                               <?= $user['status'] ? 'checked' : '' ?>
                               <?= $isCurrentUser ? 'disabled' : '' ?>>
                        <label class="form-check-label" for="status">Active</label>
                        <?php if ($isCurrentUser): ?>
                        <input type="hidden" name="status" value="1">
                        <br><small class="text-warning">You cannot deactivate yourself</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Account Info</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Created</td>
                            <td class="text-end"><?= formatDateTime($user['created_at']) ?></td>
                        </tr>
                        <tr>
                            <td>Last Login</td>
                            <td class="text-end">
                                <?= !empty($user['last_login']) ? formatDateTime($user['last_login']) : 'Never' ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2">
                <i class="fas fa-save me-2"></i> Update User
            </button>

            <?php if (!$isCurrentUser): ?>
            <button type="button" class="btn btn-outline-danger w-100" onclick="deleteUser()">
                <i class="fas fa-trash me-2"></i> Delete User
            </button>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if (!$isCurrentUser): ?>
<script>
function deleteUser() {
    if (confirm('Are you sure you want to delete this admin user?\n\nThis action cannot be undone.')) {
        window.location.href = '<?= url('admin/users/delete/' . $user['id']) ?>';
    }
}
</script>
<?php endif; ?>
