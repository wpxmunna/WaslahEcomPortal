<div class="page-header">
    <h1>Admin Users</h1>
    <a href="<?= url('admin/users/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Admin User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-users-cog"></i>
            <p>No admin users found</p>
            <a href="<?= url('admin/users/create') ?>" class="btn btn-primary">Add First Admin User</a>
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3" style="width: 40px; height: 40px; background: var(--accent); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <strong><?= sanitize($u['name']) ?></strong>
                                <?php if ($u['phone']): ?>
                                <div class="small text-muted"><?= sanitize($u['phone']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= sanitize($u['email']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                        <span class="badge bg-danger">Administrator</span>
                        <?php else: ?>
                        <span class="badge bg-info">Manager</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($u['last_login'])): ?>
                        <?= formatDateTime($u['last_login']) ?>
                        <?php else: ?>
                        <span class="text-muted">Never</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $isCurrentUser = ($u['id'] == ($_SESSION['user_id'] ?? 0)); ?>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   <?= $u['status'] ? 'checked' : '' ?>
                                   <?= $isCurrentUser ? 'disabled' : '' ?>
                                   onchange="toggleStatus(<?= $u['id'] ?>)"
                                   title="<?= $isCurrentUser ? 'Cannot deactivate yourself' : '' ?>">
                        </div>
                    </td>
                    <td>
                        <a href="<?= url('admin/users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if (!$isCurrentUser): ?>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $u['id'] ?>, '<?= sanitize($u['name']) ?>')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="p-3">
            <ul class="pagination mb-0 justify-content-center">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('admin/users?page=' . $i) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i> Role Permissions
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><span class="badge bg-danger me-2">Administrator</span></h6>
                <ul class="small text-muted">
                    <li>Full access to all admin features</li>
                    <li>Can manage admin users</li>
                    <li>Can change system settings</li>
                    <li>Can manage stores, couriers, payments</li>
                    <li>Can access reports</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6><span class="badge bg-info me-2">Manager</span></h6>
                <ul class="small text-muted">
                    <li>Can manage products, categories, colors</li>
                    <li>Can view and process orders</li>
                    <li>Can manage customers</li>
                    <li>Can manage coupons</li>
                    <li>No access to settings, stores, reports</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(id) {
    fetch('<?= url('admin/users/toggle') ?>/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Error: ' + data.message);
                location.reload();
            }
        });
}

function deleteUser(id, name) {
    if (confirm('Are you sure you want to delete admin user "' + name + '"?\n\nThis action cannot be undone.')) {
        window.location.href = '<?= url('admin/users/delete') ?>/' + id;
    }
}
</script>
