<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item active">Track Order</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4">Track Your Order</h1>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Order Number:</strong> <?= $order['order_number'] ?></p>
                                <p class="mb-1"><strong>Order Date:</strong> <?= formatDateTime($order['created_at']) ?></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1">
                                    <strong>Status:</strong>
                                    <span class="badge <?= statusBadge($order['status']) ?> fs-6">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($order['shipment']): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i> Shipment Tracking
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tracking Number:</strong> <?= $order['shipment']['tracking_number'] ?></p>
                                <p class="mb-0"><strong>Courier:</strong> <?= $order['shipment']['courier_name'] ?? 'Standard Shipping' ?></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <?php if ($order['shipment']['shipped_at']): ?>
                                <p class="mb-1"><strong>Shipped:</strong> <?= formatDateTime($order['shipment']['shipped_at']) ?></p>
                                <?php endif; ?>
                                <?php if ($order['shipment']['delivered_at']): ?>
                                <p class="mb-0"><strong>Delivered:</strong> <?= formatDateTime($order['shipment']['delivered_at']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Tracking Progress -->
                        <div class="tracking-progress mb-4">
                            <?php
                            $statuses = ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered'];
                            $currentIndex = array_search($order['shipment']['status'], $statuses);
                            ?>
                            <div class="d-flex justify-content-between position-relative">
                                <div class="progress position-absolute" style="height: 3px; top: 15px; left: 5%; right: 5%;">
                                    <div class="progress-bar bg-success" style="width: <?= ($currentIndex / 4) * 100 ?>%"></div>
                                </div>
                                <?php foreach ($statuses as $index => $status): ?>
                                <div class="text-center position-relative" style="z-index: 1;">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                         style="width: 35px; height: 35px; background: <?= $index <= $currentIndex ? '#28a745' : '#dee2e6' ?>; color: white;">
                                        <i class="fas <?= CourierService::getStatusIcon($status) ?>" style="font-size: 14px;"></i>
                                    </div>
                                    <div class="small mt-2"><?= CourierService::getStatusLabel($status) ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Tracking History -->
                        <?php if (!empty($order['shipment']['tracking'])): ?>
                        <h6 class="mb-3">Tracking History</h6>
                        <div class="tracking-history">
                            <?php foreach ($order['shipment']['tracking'] as $track): ?>
                            <div class="d-flex mb-3">
                                <div class="me-3 text-center" style="width: 100px;">
                                    <div class="small text-muted"><?= formatDate($track['tracked_at'], 'M d') ?></div>
                                    <div class="small"><?= formatDate($track['tracked_at'], 'h:i A') ?></div>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= CourierService::getStatusLabel($track['status']) ?></div>
                                    <div class="text-muted small">
                                        <?= $track['description'] ?>
                                        <?php if ($track['location']): ?>
                                        <br><i class="fas fa-map-marker-alt me-1"></i> <?= $track['location'] ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Your order is being prepared. Tracking information will be available once shipped.
                </div>
                <?php endif; ?>

                <!-- Order Items -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($order['items'] as $item): ?>
                        <div class="d-flex align-items-center mb-3">
                            <?php if ($item['image']): ?>
                            <img src="<?= upload($item['image']) ?>" alt="" style="width: 60px; height: 75px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                            <div class="ms-3 flex-grow-1">
                                <h6 class="mb-0"><?= sanitize($item['product_name']) ?></h6>
                                <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                            </div>
                            <span class="fw-bold"><?= formatPrice($item['total_price']) ?></span>
                        </div>
                        <?php endforeach; ?>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-accent"><?= formatPrice($order['total_amount']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= url('shop') ?>" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
