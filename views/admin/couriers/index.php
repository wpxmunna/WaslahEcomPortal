<div class="page-header">
    <h1>Couriers</h1>
    <a href="<?= url('admin/couriers/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Courier
    </a>
</div>

<?php if (empty($couriers)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-truck fa-3x text-muted mb-3"></i>
        <h4>No Couriers Yet</h4>
        <p class="text-muted">Add your first courier service to enable shipping.</p>
        <a href="<?= url('admin/couriers/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add Courier
        </a>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Courier</th>
                    <th>Code</th>
                    <th class="text-end">Base Rate</th>
                    <th class="text-end">Per KG Rate</th>
                    <th>Delivery Time</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($couriers as $courier): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if (!empty($courier['logo'])): ?>
                            <img src="<?= upload($courier['logo']) ?>" class="rounded me-3"
                                 style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                            <div class="rounded bg-light d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-truck text-muted"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <strong><?= sanitize($courier['name']) ?></strong>
                                <?php if ($courier['description']): ?>
                                <div class="small text-muted"><?= sanitize(substr($courier['description'], 0, 50)) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><code><?= $courier['code'] ?></code></td>
                    <td class="text-end"><?= formatPrice($courier['base_rate']) ?></td>
                    <td class="text-end"><?= formatPrice($courier['per_kg_rate']) ?></td>
                    <td><?= $courier['estimated_days'] ?: '-' ?></td>
                    <td class="text-center">
                        <span class="badge <?= $courier['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $courier['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('admin/couriers/edit/' . $courier['id']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= url('admin/couriers/delete/' . $courier['id']) ?>"
                           class="btn btn-sm btn-outline-danger"
                           data-confirm="Are you sure you want to delete this courier?">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
