<div class="page-header">
    <h1>Stores</h1>
    <a href="<?= url('admin/stores/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Create Store
    </a>
</div>

<div class="row g-4">
    <?php foreach ($stores as $store): ?>
    <div class="col-lg-4 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1"><?= sanitize($store['name']) ?></h5>
                        <small class="text-muted"><?= $store['slug'] ?></small>
                    </div>
                    <div>
                        <?php if ($store['is_default']): ?>
                        <span class="badge bg-primary">Default</span>
                        <?php endif; ?>
                        <span class="badge <?= $store['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $store['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>

                <p class="text-muted small"><?= truncate($store['description'] ?? '', 100) ?></p>

                <div class="row text-center g-2 mb-3">
                    <div class="col-3">
                        <div class="border rounded p-2">
                            <h6 class="mb-0"><?= $store['stats']['products'] ?></h6>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-2">
                            <h6 class="mb-0"><?= $store['stats']['orders'] ?></h6>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-2">
                            <h6 class="mb-0"><?= $store['stats']['customers'] ?></h6>
                            <small class="text-muted">Customers</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-2">
                            <h6 class="mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($store['stats']['revenue'], 0) ?></h6>
                            <small class="text-muted">Revenue</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2">
                    <a href="<?= url('admin/stores/switch/' . $store['id']) ?>"
                       class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-exchange-alt me-1"></i> Switch
                    </a>
                    <a href="<?= url('admin/stores/edit/' . $store['id']) ?>"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <?php if (!$store['is_default']): ?>
                    <a href="<?= url('admin/stores/delete/' . $store['id']) ?>"
                       class="btn btn-sm btn-outline-danger"
                       data-confirm="Delete this store? This will also delete all its products and orders.">
                        <i class="fas fa-trash"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-2"></i> Multi-Store Information
    </div>
    <div class="card-body">
        <p class="mb-2"><strong>How multi-store works:</strong></p>
        <ul class="mb-0">
            <li>Each store has its own products, orders, and settings</li>
            <li>Use the store selector in the sidebar to switch between stores</li>
            <li>The default store is shown when no store is selected</li>
            <li>You can clone categories from an existing store when creating a new one</li>
            <li>Each store can have different currency, tax rate, and branding</li>
        </ul>
    </div>
</div>
