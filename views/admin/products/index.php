<div class="page-header">
    <h1>Products</h1>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Product
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($products['data'])): ?>
        <div class="empty-state py-5">
            <i class="fas fa-box"></i>
            <h4>No products yet</h4>
            <p>Add your first product to get started</p>
            <a href="<?= url('admin/products/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Product
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th style="width: 80px;">Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products['data'] as $product): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected[]" value="<?= $product['id'] ?>">
                        </td>
                        <td>
                            <?php if ($product['image']): ?>
                            <img src="<?= upload($product['image']) ?>" class="product-thumb" alt="">
                            <?php else: ?>
                            <div class="product-thumb img-placeholder"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= sanitize($product['name']) ?></strong>
                            <div class="text-muted small">SKU: <?= $product['sku'] ?: 'N/A' ?></div>
                        </td>
                        <td><?= $product['category_name'] ?: '-' ?></td>
                        <td>
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                            <span class="text-decoration-line-through text-muted"><?= formatPrice($product['price']) ?></span>
                            <br>
                            <span class="text-accent fw-bold"><?= formatPrice($product['sale_price']) ?></span>
                            <?php else: ?>
                            <span class="fw-bold"><?= formatPrice($product['price']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                            <span class="badge bg-danger"><?= $product['stock_quantity'] ?></span>
                            <?php else: ?>
                            <span class="badge bg-success"><?= $product['stock_quantity'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= statusBadge($product['status']) ?>">
                                <?= ucfirst($product['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url('admin/products/edit/' . $product['id']) ?>"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= url('product/' . $product['slug']) ?>"
                               class="btn btn-sm btn-outline-info" title="View" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('admin/products/delete/' . $product['id']) ?>"
                               class="btn btn-sm btn-outline-danger" title="Delete"
                               data-confirm="Delete this product?">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($products['total_pages'] > 1): ?>
        <div class="card-footer">
            <?= pagination($products, url('admin/products')) ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
