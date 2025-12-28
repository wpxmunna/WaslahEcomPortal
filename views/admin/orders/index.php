<div class="page-header">
    <h1>Orders</h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form class="row g-3" method="GET">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search order number or customer..."
                       value="<?= $filters['search'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= ($filters['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($orders['data'])): ?>
        <div class="empty-state py-5">
            <i class="fas fa-shopping-cart"></i>
            <h4>No orders found</h4>
            <p>Orders will appear here when customers place them</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders['data'] as $order): ?>
                    <tr>
                        <td>
                            <a href="<?= url('admin/orders/view/' . $order['id']) ?>">
                                <strong><?= $order['order_number'] ?></strong>
                            </a>
                        </td>
                        <td>
                            <?= sanitize($order['shipping_name']) ?>
                            <div class="small text-muted"><?= $order['shipping_phone'] ?></div>
                        </td>
                        <td>
                            <?php
                            $itemCount = $this->db->count('order_items', 'order_id = ?', [$order['id']]);
                            echo $itemCount . ' item' . ($itemCount > 1 ? 's' : '');
                            ?>
                        </td>
                        <td class="fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                        <td>
                            <span class="badge <?= statusBadge($order['payment_status']) ?>">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" style="width: 130px;"
                                    onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)">
                                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                                <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><?= formatDate($order['created_at']) ?></td>
                        <td>
                            <a href="<?= url('admin/orders/view/' . $order['id']) ?>"
                               class="btn btn-sm btn-outline-primary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= url('admin/orders/invoice/' . $order['id']) ?>"
                               class="btn btn-sm btn-outline-info" title="Invoice" target="_blank">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($orders['total_pages'] > 1): ?>
        <div class="card-footer">
            <?= pagination($orders, url('admin/orders')) ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
