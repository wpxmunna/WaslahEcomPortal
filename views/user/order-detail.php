<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('account') ?>">My Account</a></li>
                <li class="breadcrumb-item"><a href="<?= url('account/orders') ?>">Orders</a></li>
                <li class="breadcrumb-item active"><?= $order['order_number'] ?></li>
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Order <?= $order['order_number'] ?></h3>
                        <span class="badge <?= statusBadge($order['status']) ?> fs-6">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>

                    <div class="row g-4">
                        <!-- Order Info -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Order Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted">Order Number:</td>
                                            <td class="text-end fw-bold"><?= $order['order_number'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Date:</td>
                                            <td class="text-end"><?= formatDate($order['created_at'], 'd M Y, h:i A') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Payment Method:</td>
                                            <td class="text-end"><?= ucfirst($order['payment_method'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Payment Status:</td>
                                            <td class="text-end">
                                                <span class="badge <?= $order['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= ucfirst($order['payment_status'] ?? 'Pending') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Shipping Address</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1 fw-bold"><?= sanitize($order['shipping_name'] ?? '') ?></p>
                                    <p class="mb-1"><?= sanitize($order['shipping_address'] ?? $order['shipping_address_line1'] ?? '') ?></p>
                                    <?php if (!empty($order['shipping_address_line2'])): ?>
                                    <p class="mb-1"><?= sanitize($order['shipping_address_line2']) ?></p>
                                    <?php endif; ?>
                                    <p class="mb-1">
                                        <?= sanitize($order['shipping_city'] ?? '') ?>
                                        <?= !empty($order['shipping_state']) ? ', ' . sanitize($order['shipping_state']) : '' ?>
                                        <?= !empty($order['shipping_zip']) ? ' ' . sanitize($order['shipping_zip']) : '' ?>
                                    </p>
                                    <?php if (!empty($order['shipping_phone'])): ?>
                                    <p class="mb-0"><i class="fas fa-phone me-1"></i> <?= sanitize($order['shipping_phone']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Order Items</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Quantity</th>
                                                    <th class="text-end">Price</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($order['items'] ?? [] as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($item['image'])): ?>
                                                            <img src="<?= upload($item['image']) ?>" alt="" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                            <?php endif; ?>
                                                            <div>
                                                                <h6 class="mb-1"><?= sanitize($item['product_name'] ?? $item['name'] ?? 'Product') ?></h6>
                                                                <?php if (!empty($item['variant_name'])): ?>
                                                                <small class="text-muted"><?= sanitize($item['variant_name']) ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle"><?= $item['quantity'] ?></td>
                                                    <td class="text-end align-middle"><?= formatPrice($item['price']) ?></td>
                                                    <td class="text-end align-middle fw-bold"><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-md-6 ms-auto">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Order Summary</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="text-end"><?= formatPrice($order['subtotal'] ?? $order['total_amount']) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping:</td>
                                            <td class="text-end"><?= formatPrice($order['shipping_cost'] ?? 0) ?></td>
                                        </tr>
                                        <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                        <tr>
                                            <td>Discount:</td>
                                            <td class="text-end text-danger">-<?= formatPrice($order['discount_amount']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr class="border-top">
                                            <td class="fw-bold fs-5">Total:</td>
                                            <td class="text-end fw-bold fs-5 text-primary"><?= formatPrice($order['total_amount']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tracking Info -->
                        <?php if (!empty($order['tracking_number'])): ?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">Tracking Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1 text-muted">Tracking Number</p>
                                            <p class="mb-0 fw-bold"><?= sanitize($order['tracking_number']) ?></p>
                                        </div>
                                        <a href="<?= url('order/track/' . $order['id']) ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-map-marker-alt me-2"></i>Track Order
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="<?= url('account/orders') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                                </a>
                                <?php if ($order['status'] === 'delivered'): ?>
                                <a href="<?= url('shop') ?>" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i>Order Again
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
