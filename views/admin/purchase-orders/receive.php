<div class="page-header">
    <div>
        <h1>Receive Stock</h1>
        <p class="text-muted mb-0"><?= sanitize($po['po_number']) ?> - <?= sanitize($po['supplier_name']) ?></p>
    </div>
    <a href="<?= url('admin/purchase-orders/view/' . $po['id']) ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Order
    </a>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Enter the quantity received for each item. Stock will be automatically updated in your inventory.
</div>

<form action="<?= url('admin/purchase-orders/process-receipt/' . $po['id']) ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i> Items to Receive</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Ordered</th>
                            <th class="text-center">Previously Received</th>
                            <th class="text-center">Remaining</th>
                            <th class="text-center" style="width: 150px;">Receive Now</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($po['items'] as $item): ?>
                        <?php $remaining = $item['quantity_ordered'] - $item['quantity_received']; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($item['product_image']): ?>
                                    <img src="<?= asset($item['product_image']) ?>" alt="" class="me-2" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?= sanitize($item['product_name']) ?></div>
                                        <?php if ($item['product_sku']): ?>
                                        <small class="text-muted">SKU: <?= sanitize($item['product_sku']) ?></small>
                                        <?php endif; ?>
                                        <?php if ($item['variant_info']): ?>
                                        <br><small class="text-muted"><?= sanitize($item['variant_info']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary fs-6"><?= $item['quantity_ordered'] ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($item['quantity_received'] > 0): ?>
                                <span class="badge bg-success fs-6"><?= $item['quantity_received'] ?></span>
                                <?php else: ?>
                                <span class="badge bg-light text-muted fs-6">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($remaining > 0): ?>
                                <span class="badge bg-warning fs-6"><?= $remaining ?></span>
                                <?php else: ?>
                                <span class="badge bg-success fs-6"><i class="fas fa-check"></i></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($remaining > 0): ?>
                                <input type="number" name="received[<?= $item['id'] ?>]" class="form-control text-center" min="0" max="<?= $remaining ?>" value="<?= $remaining ?>">
                                <?php else: ?>
                                <span class="text-success"><i class="fas fa-check-circle"></i> Complete</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fas fa-check me-2"></i> Confirm Receipt
        </button>
        <a href="<?= url('admin/purchase-orders/view/' . $po['id']) ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
    </div>
</form>
