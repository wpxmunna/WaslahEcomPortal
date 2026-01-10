<div class="page-header">
    <h1>Edit Purchase Order</h1>
    <a href="<?= url('admin/purchase-orders/view/' . $po['id']) ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<form action="<?= url('admin/purchase-orders/update/' . $po['id']) ?>" method="POST" id="poForm">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Order Details
                        <span class="badge bg-secondary ms-2"><?= sanitize($po['po_number']) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>" <?= $po['supplier_id'] == $supplier['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($supplier['name']) ?>
                                    <?php if ($supplier['code']): ?>(<?= sanitize($supplier['code']) ?>)<?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?= $po['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="pending" <?= $po['status'] === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                                <option value="approved" <?= $po['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="ordered" <?= $po['status'] === 'ordered' ? 'selected' : '' ?>>Ordered</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="<?= $po['order_date'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" name="expected_date" class="form-control" value="<?= $po['expected_date'] ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"><?= sanitize($po['notes']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Order Items</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()">
                        <i class="fas fa-plus me-1"></i> Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="itemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40%;">Product</th>
                                    <th style="width: 15%;">SKU</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 15%;">Unit Cost</th>
                                    <th style="width: 15%;">Total</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <?php foreach ($po['items'] as $item): ?>
                                <tr class="item-row">
                                    <td>
                                        <input type="hidden" name="product_id[]" class="product-id" value="<?= $item['product_id'] ?>">
                                        <input type="text" name="product_name[]" class="form-control product-name" required value="<?= sanitize($item['product_name']) ?>" list="productList">
                                    </td>
                                    <td>
                                        <input type="text" name="product_sku[]" class="form-control product-sku" value="<?= sanitize($item['product_sku']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="quantity[]" class="form-control item-qty" required min="1" value="<?= $item['quantity_ordered'] ?>" onchange="calculateRowTotal(this)">
                                    </td>
                                    <td>
                                        <input type="number" name="unit_cost[]" class="form-control item-cost" required min="0" step="0.01" value="<?= $item['unit_cost'] ?>" onchange="calculateRowTotal(this)">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-total" readonly value="<?= number_format($item['total_cost'], 2) ?>">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                    <td><strong id="subtotalDisplay"><?= number_format($po['subtotal'], 2) ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Product Catalog -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i> Quick Add from Catalog</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="card h-100 product-card" onclick="addProductToOrder(<?= htmlspecialchars(json_encode([
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'sku' => $product['sku'],
                                'cost_price' => $product['cost_price'] ?? 0
                            ])) ?>)">
                                <div class="card-body p-2 text-center">
                                    <?php if ($product['image']): ?>
                                    <img src="<?= asset($product['image']) ?>" alt="" class="img-fluid mb-2" style="max-height: 60px;">
                                    <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center mb-2" style="height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="small fw-bold text-truncate"><?= sanitize($product['name']) ?></div>
                                    <div class="small text-muted"><?= sanitize($product['sku'] ?: 'No SKU') ?></div>
                                    <div class="small text-primary"><?= formatPrice($product['cost_price'] ?? 0) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal</span>
                        <strong id="summarySubtotal">৳<?= number_format($po['subtotal'], 2) ?></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control" value="<?= $po['tax_amount'] ?>" min="0" step="0.01" onchange="calculateTotals()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Shipping Amount</label>
                        <input type="number" name="shipping_amount" class="form-control" value="<?= $po['shipping_amount'] ?>" min="0" step="0.01" onchange="calculateTotals()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Discount</label>
                        <input type="number" name="discount_amount" class="form-control" value="<?= $po['discount_amount'] ?>" min="0" step="0.01" onchange="calculateTotals()">
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-0">
                        <span class="h5">Total</span>
                        <strong class="h5 text-primary" id="summaryTotal">৳<?= number_format($po['total_amount'], 2) ?></strong>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-save me-2"></i> Update Purchase Order
            </button>
        </div>
    </div>
</form>

<!-- Product datalist -->
<datalist id="productList">
    <?php foreach ($products as $product): ?>
    <option value="<?= sanitize($product['name']) ?>" data-id="<?= $product['id'] ?>" data-sku="<?= sanitize($product['sku']) ?>" data-cost="<?= $product['cost_price'] ?? 0 ?>">
    <?php endforeach; ?>
</datalist>

<style>
.product-card {
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
}
.product-card:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
}
</style>

<script>
const products = <?= json_encode($products) ?>;

function addItemRow() {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td>
            <input type="hidden" name="product_id[]" class="product-id">
            <input type="text" name="product_name[]" class="form-control product-name" required placeholder="Product name" list="productList">
        </td>
        <td>
            <input type="text" name="product_sku[]" class="form-control product-sku" placeholder="SKU">
        </td>
        <td>
            <input type="number" name="quantity[]" class="form-control item-qty" required min="1" value="1" onchange="calculateRowTotal(this)">
        </td>
        <td>
            <input type="number" name="unit_cost[]" class="form-control item-cost" required min="0" step="0.01" value="0" onchange="calculateRowTotal(this)">
        </td>
        <td>
            <input type="text" class="form-control item-total" readonly value="0.00">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItemRow(this)" title="Remove">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
}

function removeItemRow(btn) {
    const tbody = document.getElementById('itemsBody');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
        calculateTotals();
    }
}

function calculateRowTotal(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
    const total = qty * cost;
    row.querySelector('.item-total').value = total.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const total = parseFloat(row.querySelector('.item-total').value) || 0;
        subtotal += total;
    });

    const tax = parseFloat(document.querySelector('[name="tax_amount"]').value) || 0;
    const shipping = parseFloat(document.querySelector('[name="shipping_amount"]').value) || 0;
    const discount = parseFloat(document.querySelector('[name="discount_amount"]').value) || 0;

    const grandTotal = subtotal + tax + shipping - discount;

    document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
    document.getElementById('summarySubtotal').textContent = '৳' + subtotal.toFixed(2);
    document.getElementById('summaryTotal').textContent = '৳' + grandTotal.toFixed(2);
}

function addProductToOrder(product) {
    // Check if product already in list
    const existingRow = document.querySelector(`.product-id[value="${product.id}"]`);
    if (existingRow) {
        const row = existingRow.closest('tr');
        const qtyInput = row.querySelector('.item-qty');
        qtyInput.value = parseInt(qtyInput.value) + 1;
        calculateRowTotal(qtyInput);
        return;
    }

    // Find empty row or add new
    let targetRow = null;
    document.querySelectorAll('.item-row').forEach(row => {
        if (!row.querySelector('.product-name').value && !targetRow) {
            targetRow = row;
        }
    });

    if (!targetRow) {
        addItemRow();
        targetRow = document.querySelector('.item-row:last-child');
    }

    targetRow.querySelector('.product-id').value = product.id;
    targetRow.querySelector('.product-name').value = product.name;
    targetRow.querySelector('.product-sku').value = product.sku || '';
    targetRow.querySelector('.item-cost').value = product.cost_price || 0;
    targetRow.querySelector('.item-qty').value = 1;

    calculateRowTotal(targetRow.querySelector('.item-qty'));
}

// Initialize
calculateTotals();
</script>
