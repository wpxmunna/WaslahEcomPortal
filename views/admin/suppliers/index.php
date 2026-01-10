<div class="page-header">
    <h1>Suppliers</h1>
    <a href="<?= url('admin/suppliers/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Supplier
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search suppliers..." value="<?= sanitize($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </div>
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
            <div class="col-md-2">
                <a href="<?= url('admin/suppliers') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Suppliers Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($suppliers)): ?>
        <div class="text-center py-5">
            <i class="fas fa-truck-loading fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No suppliers found</h5>
            <p class="text-muted">Add your first supplier to get started</p>
            <a href="<?= url('admin/suppliers/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Supplier
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Supplier</th>
                        <th>Contact</th>
                        <th class="text-center">Orders</th>
                        <th class="text-end">Total Purchases</th>
                        <th class="text-end">Balance Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                    <?php $balanceDue = $supplier['total_purchases'] - $supplier['total_paid']; ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= sanitize($supplier['name']) ?></strong>
                                <?php if ($supplier['code']): ?>
                                <span class="badge bg-secondary ms-1"><?= sanitize($supplier['code']) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($supplier['city']): ?>
                            <small class="text-muted"><?= sanitize($supplier['city']) ?>, <?= sanitize($supplier['country']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($supplier['contact_person']): ?>
                            <div><i class="fas fa-user text-muted me-1"></i> <?= sanitize($supplier['contact_person']) ?></div>
                            <?php endif; ?>
                            <?php if ($supplier['phone']): ?>
                            <div><i class="fas fa-phone text-muted me-1"></i> <?= sanitize($supplier['phone']) ?></div>
                            <?php endif; ?>
                            <?php if ($supplier['email']): ?>
                            <div><i class="fas fa-envelope text-muted me-1"></i> <?= sanitize($supplier['email']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info"><?= $supplier['order_count'] ?? 0 ?></span>
                        </td>
                        <td class="text-end">
                            <?= formatPrice($supplier['total_purchases']) ?>
                        </td>
                        <td class="text-end">
                            <?php if ($balanceDue > 0): ?>
                            <span class="text-danger fw-bold"><?= formatPrice($balanceDue) ?></span>
                            <?php else: ?>
                            <span class="text-success"><?= formatPrice(0) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($supplier['status'] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= url('admin/suppliers/view/' . $supplier['id']) ?>" class="btn btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= url('admin/suppliers/edit/' . $supplier['id']) ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (($supplier['order_count'] ?? 0) == 0): ?>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteSupplier(<?= $supplier['id'] ?>)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($pagination['current_page'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query($filters) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                    <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query($filters) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteSupplier(id) {
    if (confirm('Are you sure you want to delete this supplier?')) {
        window.location.href = '<?= url('admin/suppliers/delete') ?>/' + id;
    }
}
</script>
