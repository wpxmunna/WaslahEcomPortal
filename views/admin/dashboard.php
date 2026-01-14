<div class="page-header">
    <h1>Dashboard</h1>
    <div>
        <span class="text-muted">Welcome back, <?= sanitize($user['name'] ?? 'Admin') ?>!</span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-gradient-primary">
            <i class="fas fa-shopping-cart stat-icon"></i>
            <h3><?= $stats['total_orders'] ?></h3>
            <p>Total Orders</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-gradient-success">
            <i class="fas fa-dollar-sign stat-icon"></i>
            <h3><?= formatPrice($stats['total_revenue']) ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-gradient-warning">
            <i class="fas fa-box stat-icon"></i>
            <h3><?= $productCount ?></h3>
            <p>Products</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-gradient-info">
            <i class="fas fa-users stat-icon"></i>
            <h3><?= $customerCount ?></h3>
            <p>Customers</p>
        </div>
    </div>
</div>

<!-- Today's Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="text-accent mb-0"><?= $stats['today_orders'] ?></h4>
                <small class="text-muted">Orders Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="text-success mb-0"><?= formatPrice($stats['today_revenue']) ?></h4>
                <small class="text-muted">Revenue Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="text-warning mb-0"><?= $stats['pending_orders'] ?></h4>
                <small class="text-muted">Pending Orders</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Orders</span>
                <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentOrders)): ?>
                <div class="empty-state py-4">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No orders yet</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>
                                    <a href="<?= url('admin/orders/view/' . $order['id']) ?>">
                                        <?= $order['order_number'] ?>
                                    </a>
                                </td>
                                <td><?= sanitize($order['customer_name'] ?? $order['shipping_name']) ?></td>
                                <td>
                                    <span class="badge <?= statusBadge($order['status']) ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                                <td><?= formatDate($order['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Low Stock Alert</span>
                <span class="badge bg-warning"><?= count($lowStockProducts) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($lowStockProducts)): ?>
                <div class="empty-state py-4">
                    <i class="fas fa-check-circle text-success"></i>
                    <p>All products in stock</p>
                </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= truncate($product['name'], 25) ?></strong>
                            <small class="d-block text-muted">SKU: <?= $product['sku'] ?? 'N/A' ?></small>
                        </div>
                        <span class="badge bg-danger"><?= $product['stock_quantity'] ?> left</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('admin/products/create') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i> Add Product
                    </a>
                    <a href="<?= url('admin/orders') ?>?status=pending" class="btn btn-outline-warning">
                        <i class="fas fa-clock me-2"></i> Pending Orders
                    </a>
                    <a href="<?= url('admin/reports') ?>" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i> View Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Business Quick Links -->
        <?php
        $businessLinks = [
            'facebook_page_url' => ['icon' => 'fab fa-facebook', 'label' => 'Facebook Page', 'color' => 'primary'],
            'whatsapp_link' => ['icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp', 'color' => 'success'],
            'pathao_login_url' => ['icon' => 'fas fa-shipping-fast', 'label' => 'Pathao', 'color' => 'warning'],
            'steadfast_url' => ['icon' => 'fas fa-truck', 'label' => 'SteadFast', 'color' => 'success']
        ];
        $hasLinks = false;
        foreach ($businessLinks as $key => $config) {
            if (getBusinessSetting($key)) {
                $hasLinks = true;
                break;
            }
        }
        ?>
        <?php if ($hasLinks): ?>
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-external-link-alt me-2"></i>Business Links</span>
                <a href="<?= url('admin/settings/business') ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php foreach ($businessLinks as $key => $config): ?>
                        <?php if ($url = getBusinessSetting($key)): ?>
                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="btn btn-outline-<?= $config['color'] ?> btn-sm">
                            <i class="<?= $config['icon'] ?> me-2"></i><?= $config['label'] ?>
                        </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
