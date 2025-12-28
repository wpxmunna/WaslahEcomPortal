<div class="page-header">
    <h1>Categories</h1>
    <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Category
    </a>
</div>

<?php if (empty($categories)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
        <h4>No Categories Yet</h4>
        <p class="text-muted">Create your first category to organize products.</p>
        <a href="<?= url('admin/categories/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add Category
        </a>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Parent</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">Order</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if ($category['image']): ?>
                            <img src="<?= upload($category['image']) ?>" class="rounded me-3"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <?php elseif ($category['icon']): ?>
                            <div class="rounded bg-light d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="<?= $category['icon'] ?> text-primary"></i>
                            </div>
                            <?php else: ?>
                            <div class="rounded bg-light d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-folder text-muted"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <strong><?= sanitize($category['name']) ?></strong>
                                <div class="small text-muted"><?= $category['slug'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if ($category['parent_id']): ?>
                            <?= sanitize($category['parent_name'] ?? '-') ?>
                        <?php else: ?>
                            <span class="badge bg-info">Parent</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= $category['product_count'] ?? 0 ?></td>
                    <td class="text-center"><?= $category['sort_order'] ?></td>
                    <td class="text-center">
                        <span class="badge <?= $category['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $category['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('admin/categories/edit/' . $category['id']) ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= url('admin/categories/delete/' . $category['id']) ?>"
                           class="btn btn-sm btn-outline-danger"
                           data-confirm="Are you sure you want to delete this category?">
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
