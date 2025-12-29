<div class="page-header">
    <h1>Order <?= $order['order_number'] ?></h1>
    <div>
        <a href="<?= url('admin/orders/invoice/' . $order['id']) ?>" class="btn btn-outline-info" target="_blank">
            <i class="fas fa-file-invoice me-2"></i> Invoice
        </a>
        <a href="<?= url('admin/orders') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Orders
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">Order Items</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($item['image']): ?>
                                    <img src="<?= upload($item['image']) ?>" class="product-thumb me-3" alt="">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= sanitize($item['product_name']) ?></strong>
                                        <?php if ($item['variant_info']): ?>
                                        <div class="small text-muted"><?= $item['variant_info'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= $item['product_sku'] ?: 'N/A' ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-end"><?= formatPrice($item['unit_price']) ?></td>
                            <td class="text-end fw-bold"><?= formatPrice($item['total_price']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Subtotal</td>
                            <td class="text-end"><?= formatPrice($order['subtotal']) ?></td>
                        </tr>
                        <?php if ($order['discount_amount'] > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end">Discount</td>
                            <td class="text-end text-danger">-<?= formatPrice($order['discount_amount']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="4" class="text-end">Shipping</td>
                            <td class="text-end"><?= formatPrice($order['shipping_amount']) ?></td>
                        </tr>
                        <?php if ($order['tax_amount'] > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end">Tax</td>
                            <td class="text-end"><?= formatPrice($order['tax_amount']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold text-accent fs-5"><?= formatPrice($order['total_amount']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Addresses -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Shipping Address</div>
                    <div class="card-body">
                        <strong><?= sanitize($order['shipping_name']) ?></strong><br>
                        <?= sanitize($order['shipping_address_line1']) ?><br>
                        <?php if ($order['shipping_address_line2']): ?>
                        <?= sanitize($order['shipping_address_line2']) ?><br>
                        <?php endif; ?>
                        <?= sanitize($order['shipping_city']) ?>, <?= sanitize($order['shipping_state']) ?> <?= sanitize($order['shipping_postal_code']) ?><br>
                        <?= sanitize($order['shipping_country']) ?><br>
                        <strong>Phone:</strong> <?= sanitize($order['shipping_phone']) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Billing Address</div>
                    <div class="card-body">
                        <strong><?= sanitize($order['billing_name']) ?></strong><br>
                        <?= sanitize($order['billing_address_line1']) ?><br>
                        <?php if ($order['billing_address_line2']): ?>
                        <?= sanitize($order['billing_address_line2']) ?><br>
                        <?php endif; ?>
                        <?= sanitize($order['billing_city']) ?>, <?= sanitize($order['billing_state']) ?> <?= sanitize($order['billing_postal_code']) ?><br>
                        <?= sanitize($order['billing_country']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <?php if ($order['notes']): ?>
        <div class="card mb-4">
            <div class="card-header">Customer Notes</div>
            <div class="card-body">
                <?= nl2br(sanitize($order['notes'])) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pathao Courier Section -->
        <div class="card mb-4" id="pathaoSection">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-shipping-fast me-2"></i> Pathao Courier</span>
                <?php
                $pathaoShipment = null;
                if (!empty($order['shipment']) && ($order['shipment']['courier_name'] ?? '') === 'Pathao') {
                    $pathaoShipment = $order['shipment'];
                }
                ?>
                <?php if (!$pathaoShipment): ?>
                <button class="btn btn-sm btn-primary" onclick="createPathaoOrder(<?= $order['id'] ?>)" id="createPathaoBtn">
                    <i class="fas fa-plus me-1"></i> Create Pickup Request
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($pathaoShipment): ?>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Consignment ID:</strong><br>
                        <code class="fs-6"><?= sanitize($pathaoShipment['tracking_number']) ?></code>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong><br>
                        <span class="badge bg-info" id="pathaoStatus"><?= ucfirst($pathaoShipment['status'] ?? 'Pending') ?></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Delivery Fee:</strong><br>
                        <?= formatPrice($pathaoShipment['delivery_fee'] ?? 0) ?>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-info" onclick="checkPathaoStatus(<?= $order['id'] ?>)">
                        <i class="fas fa-sync me-1"></i> Refresh Status
                    </button>
                    <a href="https://merchant.pathao.com" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-external-link-alt me-1"></i> Pathao Dashboard
                    </a>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-3" id="noPathaoOrder">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <p class="mb-0">No Pathao pickup request created yet.</p>
                    <small>Click "Create Pickup Request" or change status to "Processing" to automatically create one.</small>
                </div>
                <div class="d-none" id="pathaoOrderCreated">
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Pathao pickup request created successfully!</strong>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Consignment ID:</strong><br>
                            <code class="fs-6" id="newConsignmentId"></code>
                        </div>
                        <div class="col-md-6">
                            <strong>Delivery Fee:</strong><br>
                            <span id="newDeliveryFee"></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shipment Tracking -->
        <?php if ($order['shipment'] && ($order['shipment']['courier_name'] ?? '') !== 'Pathao'): ?>
        <div class="card mb-4">
            <div class="card-header">Shipment Tracking</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Tracking Number:</strong><br>
                        <?= $order['shipment']['tracking_number'] ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Courier:</strong><br>
                        <?= $order['shipment']['courier_name'] ?? 'Standard Shipping' ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Status:</strong><br>
                        <span class="badge <?= statusBadge($order['shipment']['status']) ?>">
                            <?= CourierService::getStatusLabel($order['shipment']['status']) ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($order['shipment']['tracking'])): ?>
                <h6>Tracking History</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['shipment']['tracking'] as $track): ?>
                        <tr>
                            <td><?= formatDateTime($track['tracked_at']) ?></td>
                            <td><?= CourierService::getStatusLabel($track['status']) ?></td>
                            <td><?= $track['location'] ?: '-' ?></td>
                            <td><?= $track['description'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php if ($order['shipment']['status'] !== 'delivered'): ?>
                <button class="btn btn-sm btn-outline-primary" onclick="simulateShipment(<?= $order['shipment']['id'] ?>)">
                    <i class="fas fa-forward me-1"></i> Simulate Progress
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Order Status -->
        <div class="card mb-4">
            <div class="card-header">Order Status</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Order Status</label>
                    <select class="form-select" onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)">
                        <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $status): ?>
                        <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                            <?= ucfirst($status) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Status</label>
                    <span class="badge <?= statusBadge($order['payment_status']) ?> fs-6">
                        <?= ucfirst($order['payment_status']) ?>
                    </span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <p class="mb-0"><?= ucfirst($order['payment_method'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="card mb-4">
            <div class="card-header">Order Information</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Order Number</td>
                        <td class="text-end fw-bold"><?= $order['order_number'] ?></td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td class="text-end"><?= formatDateTime($order['created_at']) ?></td>
                    </tr>
                    <tr>
                        <td>Last Updated</td>
                        <td class="text-end"><?= formatDateTime($order['updated_at']) ?></td>
                    </tr>
                    <?php if ($order['user']): ?>
                    <tr>
                        <td>Customer</td>
                        <td class="text-end">
                            <a href="<?= url('admin/customers/view/' . $order['user']['id']) ?>">
                                <?= sanitize($order['user']['name']) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="text-end"><?= $order['user']['email'] ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td>Customer</td>
                        <td class="text-end">Guest</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Payment Info -->
        <?php if ($order['payment']): ?>
        <div class="card mb-4">
            <div class="card-header">Payment Details</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Gateway</td>
                        <td class="text-end"><?= ucfirst($order['payment']['gateway']) ?></td>
                    </tr>
                    <?php if ($order['payment']['transaction_id']): ?>
                    <tr>
                        <td>Transaction ID</td>
                        <td class="text-end"><code><?= $order['payment']['transaction_id'] ?></code></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Amount</td>
                        <td class="text-end"><?= formatPrice($order['payment']['amount']) ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td class="text-end">
                            <span class="badge <?= statusBadge($order['payment']['status']) ?>">
                                <?= ucfirst($order['payment']['status']) ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Shipping & Courier -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-truck me-2"></i> Shipping & Courier
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Courier Service</label>
                    <select class="form-select" id="courierSelect">
                        <option value="">-- Select Courier --</option>
                        <option value="pathao" <?= ($order['shipment']['courier_name'] ?? '') === 'Pathao' ? 'selected' : '' ?>>Pathao</option>
                        <option value="steadfast" <?= ($order['shipment']['courier_name'] ?? '') === 'Steadfast' ? 'selected' : '' ?>>Steadfast</option>
                        <option value="redx" <?= ($order['shipment']['courier_name'] ?? '') === 'RedX' ? 'selected' : '' ?>>RedX</option>
                        <option value="sundarban" <?= ($order['shipment']['courier_name'] ?? '') === 'Sundarban' ? 'selected' : '' ?>>Sundarban Courier</option>
                        <option value="sa_paribahan" <?= ($order['shipment']['courier_name'] ?? '') === 'SA Paribahan' ? 'selected' : '' ?>>SA Paribahan</option>
                        <option value="other" <?= ($order['shipment']['courier_name'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Shipping Charge</label>
                    <div class="input-group">
                        <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                        <input type="number" class="form-control" id="shippingCharge"
                               value="<?= $order['shipping_amount'] ?? 0 ?>" min="0" step="0.01">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tracking Number</label>
                    <input type="text" class="form-control" id="trackingNumber"
                           value="<?= $order['shipment']['tracking_number'] ?? '' ?>"
                           placeholder="Enter tracking number">
                </div>

                <button class="btn btn-primary w-100" onclick="updateShipping(<?= $order['id'] ?>)">
                    <i class="fas fa-save me-2"></i> Update Shipping
                </button>

                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Updating shipping will recalculate the order total.
                </div>
            </div>
        </div>

        <!-- Admin Notes -->
        <div class="card mb-4">
            <div class="card-header">Admin Notes</div>
            <div class="card-body">
                <textarea class="form-control" rows="3" id="adminNotes" placeholder="Internal notes..."><?= sanitize($order['admin_notes'] ?? '') ?></textarea>
                <button class="btn btn-sm btn-primary mt-2" onclick="saveAdminNotes(<?= $order['id'] ?>)">Save Notes</button>
            </div>
        </div>
    </div>
</div>

<script>
// Update order status
function updateOrderStatus(orderId, status) {
    fetch('<?= url('admin/orders/status') ?>/' + orderId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'status=' + status
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Status updated successfully');

            // If Pathao order was created, show it
            if (data.pathao && data.pathao.success) {
                showPathaoOrderCreated(data.pathao);
            }

            // Reload to show updated info
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating status');
        console.error(error);
    });
}

// Create Pathao order manually
function createPathaoOrder(orderId) {
    const btn = document.getElementById('createPathaoBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';

    fetch('<?= url('admin/orders/pathao') ?>/' + orderId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPathaoOrderCreated(data.data);
            btn.classList.add('d-none');
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create Pickup Request';
        }
    })
    .catch(error => {
        alert('Error creating Pathao order');
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus me-1"></i> Create Pickup Request';
    });
}

// Show Pathao order created message
function showPathaoOrderCreated(data) {
    document.getElementById('noPathaoOrder').classList.add('d-none');
    document.getElementById('pathaoOrderCreated').classList.remove('d-none');
    document.getElementById('newConsignmentId').textContent = data.consignment_id;
    document.getElementById('newDeliveryFee').textContent = '<?= CURRENCY_SYMBOL ?>' + (data.delivery_fee || 0);
}

// Check Pathao order status
function checkPathaoStatus(orderId) {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Checking...';

    fetch('<?= url('admin/orders/pathao-status') ?>/' + orderId)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            document.getElementById('pathaoStatus').textContent = data.data.order_status || 'Unknown';
            alert('Status: ' + (data.data.order_status || 'Unknown'));
        } else {
            alert('Could not fetch status: ' + (data.message || 'Unknown error'));
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync me-1"></i> Refresh Status';
    })
    .catch(error => {
        alert('Error checking status');
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync me-1"></i> Refresh Status';
    });
}

// Simulate shipment progress (for non-Pathao)
function simulateShipment(shipmentId) {
    fetch('<?= url('api/shipment/progress') ?>/' + shipmentId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Update shipping details
function updateShipping(orderId) {
    const courier = document.getElementById('courierSelect').value;
    const shippingCharge = document.getElementById('shippingCharge').value;
    const trackingNumber = document.getElementById('trackingNumber').value;

    const formData = new FormData();
    formData.append('courier', courier);
    formData.append('shipping_amount', shippingCharge);
    formData.append('tracking_number', trackingNumber);

    fetch('<?= url('admin/orders/update-shipping') ?>/' + orderId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Shipping updated successfully! New total: ' + data.new_total);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating shipping');
        console.error(error);
    });
}

// Save admin notes
function saveAdminNotes(orderId) {
    const notes = document.getElementById('adminNotes').value;

    const formData = new FormData();
    formData.append('admin_notes', notes);

    fetch('<?= url('admin/orders/save-notes') ?>/' + orderId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notes saved successfully!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error saving notes');
        console.error(error);
    });
}
</script>
