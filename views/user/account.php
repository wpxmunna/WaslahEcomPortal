<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <?php include VIEW_PATH . '/user/partials/sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <div class="account-content">
                    <h3 class="mb-4">Welcome, <?= sanitize($user['name']) ?>!</h3>

                    <!-- Stats -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= $stats['total_orders'] ?></h4>
                                    <small>Total Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= formatPrice($stats['total_spent'] ?? 0) ?></h4>
                                    <small>Total Spent</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-heart fa-2x mb-2"></i>
                                    <h4 class="mb-0"><?= count($user['addresses'] ?? []) ?></h4>
                                    <small>Saved Addresses</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Orders</h5>
                            <a href="<?= url('account/orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentOrders)): ?>
                            <p class="text-muted text-center mb-0">No orders yet. <a href="<?= url('shop') ?>">Start shopping</a></p>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td><strong><?= $order['order_number'] ?></strong></td>
                                            <td><?= formatDate($order['created_at']) ?></td>
                                            <td>
                                                <span class="badge <?= statusBadge($order['status']) ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= formatPrice($order['total_amount']) ?></td>
                                            <td>
                                                <a href="<?= url('account/order/' . $order['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
