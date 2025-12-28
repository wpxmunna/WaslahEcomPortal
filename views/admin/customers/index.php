<div class="page-header">
    <h1>Customers</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by name, email, or phone..."
                           value="<?= sanitize($search) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <?php if ($search): ?>
            <div class="col-md-6">
                <a href="<?= url('admin/customers') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Clear Search
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (empty($customers)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h4>No Customers Found</h4>
        <p class="text-muted">
            <?= $search ? 'No customers match your search criteria.' : 'No customers have registered yet.' ?>
        </p>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Total Spent</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3">
                                <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <strong><?= sanitize($customer['name']) ?></strong>
                                <div class="small text-muted">
                                    Joined <?= formatDate($customer['created_at']) ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><?= $customer['email'] ?></td>
                    <td><?= $customer['phone'] ?: '-' ?></td>
                    <td class="text-center"><?= $customer['order_count'] ?></td>
                    <td class="text-end"><?= formatPrice($customer['total_spent']) ?></td>
                    <td class="text-center">
                        <span class="badge <?= $customer['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $customer['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('admin/customers/view/' . $customer['id']) ?>"
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="<?= url('admin/customers/toggle/' . $customer['id']) ?>"
                           class="btn btn-sm btn-outline-<?= $customer['status'] ? 'warning' : 'success' ?>"
                           title="<?= $customer['status'] ? 'Deactivate' : 'Activate' ?>">
                            <i class="fas fa-<?= $customer['status'] ? 'ban' : 'check' ?>"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php if ($pagination['current_page'] > 1): ?>
        <li class="page-item">
            <a class="page-link" href="<?= url('admin/customers?page=' . ($pagination['current_page'] - 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
            <a class="page-link" href="<?= url('admin/customers?page=' . $i . ($search ? '&search=' . urlencode($search) : '')) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>

        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
        <li class="page-item">
            <a class="page-link" href="<?= url('admin/customers?page=' . ($pagination['current_page'] + 1) . ($search ? '&search=' . urlencode($search) : '')) ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>
<?php endif; ?>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
</style>
