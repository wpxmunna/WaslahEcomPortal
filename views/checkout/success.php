<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h1>Thank You for Your Order!</h1>
                    <p class="lead text-muted">Your order has been placed successfully</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Details</h5>
                                <p class="mb-1"><strong>Order Number:</strong> <?= $order['order_number'] ?></p>
                                <p class="mb-1"><strong>Date:</strong> <?= formatDateTime($order['created_at']) ?></p>
                                <p class="mb-1"><strong>Payment:</strong>
                                    <span class="badge <?= statusBadge($order['payment_status']) ?>">
                                        <?= ucfirst($order['payment_status']) ?>
                                    </span>
                                </p>
                                <p class="mb-0"><strong>Status:</strong>
                                    <span class="badge <?= statusBadge($order['status']) ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5>Shipping Address</h5>
                                <p class="mb-0">
                                    <?= sanitize($order['shipping_name']) ?><br>
                                    <?= sanitize($order['shipping_address_line1']) ?><br>
                                    <?php if ($order['shipping_address_line2']): ?>
                                    <?= sanitize($order['shipping_address_line2']) ?><br>
                                    <?php endif; ?>
                                    <?= sanitize($order['shipping_city']) ?>, <?= sanitize($order['shipping_state']) ?> <?= sanitize($order['shipping_postal_code']) ?><br>
                                    <?= sanitize($order['shipping_country']) ?>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Order Items</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <?= sanitize($item['product_name']) ?>
                                            <?php if ($item['variant_info']): ?>
                                            <small class="text-muted d-block"><?= $item['variant_info'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end"><?= formatPrice($item['unit_price']) ?></td>
                                        <td class="text-end"><?= formatPrice($item['total_price']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end">Subtotal</td>
                                        <td class="text-end"><?= formatPrice($order['subtotal']) ?></td>
                                    </tr>
                                    <?php if ($order['discount_amount'] > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end">
                                            Discount
                                            <?php if (!empty($order['coupon_code'])): ?>
                                            <span class="badge bg-success ms-1"><?= sanitize($order['coupon_code']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-success">-<?= formatPrice($order['discount_amount']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="3" class="text-end">Shipping</td>
                                        <td class="text-end"><?= formatPrice($order['shipping_amount']) ?></td>
                                    </tr>
                                    <?php if ($order['tax_amount'] > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end">Tax</td>
                                        <td class="text-end"><?= formatPrice($order['tax_amount']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold text-accent"><?= formatPrice($order['total_amount']) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <?php if ($order['shipment']): ?>
                        <hr>
                        <h5 class="mb-3">Tracking Information</h5>
                        <p>
                            <strong>Tracking Number:</strong> <?= $order['shipment']['tracking_number'] ?><br>
                            <strong>Courier:</strong> <?= $order['shipment']['courier_name'] ?? 'Standard Shipping' ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= url('order/track/' . $order['order_number']) ?>" class="btn btn-outline-dark me-2">
                        <i class="fas fa-truck me-2"></i> Track Order
                    </a>
                    <a href="<?= url('shop') ?>" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                    </a>
                </div>

                <div class="text-center mt-4 text-muted">
                    <p>A confirmation email has been sent to your email address.</p>
                    <p>If you have any questions, please contact us at <?= SITE_EMAIL ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
