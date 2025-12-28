<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('account') ?>">My Account</a></li>
                <li class="breadcrumb-item active">Orders</li>
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
                    <h3 class="mb-4">My Orders</h3>

                    <?php if (empty($orders['data'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h4>No orders yet</h4>
                        <p>You haven't placed any orders. Start shopping now!</p>
                        <a href="<?= url('shop') ?>" class="btn btn-primary">Shop Now</a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders['data'] as $order): ?>
                                <tr>
                                    <td><strong><?= $order['order_number'] ?></strong></td>
                                    <td><?= formatDate($order['created_at']) ?></td>
                                    <td>
                                        <span class="badge <?= statusBadge($order['status']) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= statusBadge($order['payment_status']) ?>">
                                            <?= ucfirst($order['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold"><?= formatPrice($order['total_amount']) ?></td>
                                    <td>
                                        <a href="<?= url('account/order/' . $order['id']) ?>" class="btn btn-sm btn-outline-dark">
                                            View
                                        </a>
                                        <a href="<?= url('order/track/' . $order['order_number']) ?>" class="btn btn-sm btn-outline-primary">
                                            Track
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?= pagination($orders, url('account/orders')) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
