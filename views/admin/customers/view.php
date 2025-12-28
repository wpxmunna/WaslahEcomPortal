<div class="page-header">
    <h1>Customer Details</h1>
    <a href="<?= url('admin/customers') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Customers
    </a>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar-circle-lg mx-auto mb-3">
                    <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                </div>
                <h4><?= sanitize($customer['name']) ?></h4>
                <p class="text-muted mb-1"><?= $customer['email'] ?></p>
                <?php if ($customer['phone']): ?>
                <p class="text-muted"><?= $customer['phone'] ?></p>
                <?php endif; ?>

                <span class="badge <?= $customer['status'] ? 'bg-success' : 'bg-secondary' ?> mb-3">
                    <?= $customer['status'] ? 'Active' : 'Inactive' ?>
                </span>

                <hr>

                <div class="row text-center">
                    <div class="col-4">
                        <h5 class="mb-0"><?= $stats['total_orders'] ?></h5>
                        <small class="text-muted">Orders</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0"><?= formatPrice($stats['total_spent']) ?></h5>
                        <small class="text-muted">Spent</small>
                    </div>
                    <div class="col-4">
                        <h5 class="mb-0"><?= $stats['last_order'] ? formatDate($stats['last_order']) : '-' ?></h5>
                        <small class="text-muted">Last Order</small>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <small class="text-muted">Joined <?= formatDateTime($customer['created_at']) ?></small>
            </div>
        </div>

        <!-- Addresses -->
        <div class="card mb-4">
            <div class="card-header">Saved Addresses</div>
            <div class="card-body">
                <?php if (empty($addresses)): ?>
                <p class="text-muted mb-0">No saved addresses</p>
                <?php else: ?>
                    <?php foreach ($addresses as $address): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <?php if ($address['is_default']): ?>
                        <span class="badge bg-primary mb-1">Default</span>
                        <?php endif; ?>
                        <p class="mb-0">
                            <strong><?= sanitize($address['name']) ?></strong><br>
                            <?= sanitize($address['address_line1']) ?><br>
                            <?php if ($address['address_line2']): ?>
                            <?= sanitize($address['address_line2']) ?><br>
                            <?php endif; ?>
                            <?= sanitize($address['city']) ?>, <?= sanitize($address['state']) ?> <?= sanitize($address['postal_code']) ?><br>
                            <?= sanitize($address['country']) ?>
                        </p>
                        <?php if ($address['phone']): ?>
                        <small class="text-muted">Phone: <?= $address['phone'] ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">Recent Orders</div>
            <div class="card-body p-0">
                <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No orders yet</p>
                </div>
                <?php else: ?>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= $order['order_number'] ?></strong></td>
                            <td><?= formatDate($order['created_at']) ?></td>
                            <td class="text-end"><?= formatPrice($order['total_amount']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= statusBadge($order['status']) ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('admin/orders/view/' . $order['id']) ?>"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-color);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 32px;
}
</style>
