<div class="page-header">
    <h1>Coupons</h1>
    <a href="<?= url('admin/coupons/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Add Coupon
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($coupons)): ?>
        <div class="empty-state">
            <i class="fas fa-ticket-alt"></i>
            <p>No coupons found</p>
            <a href="<?= url('admin/coupons/create') ?>" class="btn btn-primary">Create First Coupon</a>
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min. Amount</th>
                    <th>Usage</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                <tr>
                    <td>
                        <code class="fs-6"><?= sanitize($coupon['code']) ?></code>
                    </td>
                    <td>
                        <?php
                        $typeBadges = [
                            'fixed' => 'bg-secondary',
                            'percentage' => 'bg-info',
                            'free_shipping' => 'bg-success',
                            'gift_item' => 'bg-warning',
                            'buy_x_get_y' => 'bg-primary'
                        ];
                        $typeLabels = [
                            'fixed' => 'Fixed',
                            'percentage' => 'Percentage',
                            'free_shipping' => 'Free Shipping',
                            'gift_item' => 'Gift Item',
                            'buy_x_get_y' => 'Buy X Get Y'
                        ];
                        ?>
                        <span class="badge <?= $typeBadges[$coupon['type']] ?? 'bg-secondary' ?>">
                            <?= $typeLabels[$coupon['type']] ?? ucfirst($coupon['type']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($coupon['type'] === 'percentage'): ?>
                        <?= $coupon['value'] ?>%
                        <?php if ($coupon['maximum_discount']): ?>
                        <small class="text-muted">(max <?= formatPrice($coupon['maximum_discount']) ?>)</small>
                        <?php endif; ?>
                        <?php elseif ($coupon['type'] === 'free_shipping'): ?>
                        <span class="text-success"><i class="fas fa-shipping-fast"></i> Free</span>
                        <?php elseif ($coupon['type'] === 'gift_item'): ?>
                        <span class="text-warning" title="<?= sanitize($coupon['gift_product_name'] ?? 'Gift') ?>">
                            <i class="fas fa-gift"></i> <?= truncate($coupon['gift_product_name'] ?? 'Gift', 20) ?>
                        </span>
                        <?php elseif ($coupon['type'] === 'buy_x_get_y'): ?>
                        Buy <?= $coupon['buy_quantity'] ?> Get <?= $coupon['get_quantity'] ?>
                        <?php else: ?>
                        <?= formatPrice($coupon['value']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($coupon['minimum_amount'] > 0): ?>
                        <?= formatPrice($coupon['minimum_amount']) ?>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $coupon['used_count'] ?>
                        <?php if ($coupon['usage_limit']): ?>
                        / <?= $coupon['usage_limit'] ?>
                        <?php else: ?>
                        <span class="text-muted">/ Unlimited</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($coupon['expires_at']): ?>
                            <?php if (strtotime($coupon['expires_at']) < time()): ?>
                            <span class="text-danger"><?= date('M d, Y', strtotime($coupon['expires_at'])) ?></span>
                            <?php else: ?>
                            <?= date('M d, Y', strtotime($coupon['expires_at'])) ?>
                            <?php endif; ?>
                        <?php else: ?>
                        <span class="text-muted">Never</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   <?= $coupon['status'] ? 'checked' : '' ?>
                                   onchange="toggleStatus(<?= $coupon['id'] ?>)">
                        </div>
                    </td>
                    <td>
                        <a href="<?= url('admin/coupons/edit/' . $coupon['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCoupon(<?= $coupon['id'] ?>)" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleStatus(id) {
    fetch('<?= url('admin/coupons/toggle') ?>/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert('Error: ' + data.message);
                location.reload();
            }
        });
}

function deleteCoupon(id) {
    if (confirm('Are you sure you want to delete this coupon?')) {
        window.location.href = '<?= url('admin/coupons/delete') ?>/' + id;
    }
}
</script>
