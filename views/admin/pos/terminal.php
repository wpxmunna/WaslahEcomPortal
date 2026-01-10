<?php
/**
 * Enhanced POS Terminal View
 * Features: Hold/Recall, Categories, Split Payment, Keyboard Shortcuts,
 * Customer Database, Item Discounts, Quick Cash, Daily Summary, Barcode, Refunds
 */
?>

<style>
.pos-terminal {
    background: #f8f9fa;
    min-height: calc(100vh - 120px);
    padding: 1rem 0;
}
.pos-terminal .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-radius: 10px;
    margin-bottom: 1rem;
}
.pos-terminal .card-header {
    background: #fff;
    border-bottom: 1px solid #eee;
    padding: 0.75rem 1rem;
}
.pos-terminal .card-body {
    padding: 1rem;
}
.pos-terminal .card-footer {
    background: #fff;
    border-top: 1px solid #eee;
    padding: 1rem;
}
.product-card {
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
    background: #fff;
}
.product-card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.product-card.out-of-stock { opacity: 0.5; pointer-events: none; }
.category-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}
.category-tab {
    padding: 6px 14px;
    border-radius: 20px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 13px;
    font-weight: 500;
}
.category-tab:hover, .category-tab.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}
.cart-item {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}
.cart-item:last-child { border-bottom: none; }
.held-order {
    background: #fff3cd;
    border-left: 3px solid #ffc107;
    padding: 10px 12px;
    margin-bottom: 8px;
    cursor: pointer;
    border-radius: 6px;
}
.held-order:hover { background: #ffe69c; }
.quick-cash-btn { min-width: 60px; }
.summary-widget {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    padding: 1.25rem !important;
}
.summary-widget .h4 { font-weight: 600; }
.summary-widget small { opacity: 0.9; }
kbd {
    display: inline-block;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 3px;
    margin-right: 4px;
}
.badge kbd {
    font-family: inherit;
}
.split-payment-row {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-bottom: 8px;
}
.item-discount-input { width: 60px; }
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    margin-bottom: 0.5rem;
}
.page-header h1 {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}
.text-accent { color: #667eea !important; }
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea, #764ba2) !important;
}
</style>

<div class="d-flex justify-content-between align-items-start flex-wrap mb-3 gap-3">
    <div>
        <h1 class="h3 mb-2"><i class="fas fa-cash-register me-2"></i>Point of Sale</h1>
        <div class="d-flex flex-wrap gap-1">
            <span class="badge bg-light text-dark border py-2 px-2"><kbd class="bg-dark text-white px-1">F1</kbd> Search</span>
            <span class="badge bg-light text-dark border py-2 px-2"><kbd class="bg-dark text-white px-1">F2</kbd> Hold</span>
            <span class="badge bg-light text-dark border py-2 px-2"><kbd class="bg-dark text-white px-1">F3</kbd> Recall</span>
            <span class="badge bg-light text-dark border py-2 px-2"><kbd class="bg-dark text-white px-1">F4</kbd> Clear</span>
            <span class="badge bg-light text-dark border py-2 px-2"><kbd class="bg-dark text-white px-1">F8</kbd> Split</span>
            <span class="badge bg-success text-white py-2 px-2"><kbd class="bg-white text-success px-1">F12</kbd> Pay</span>
        </div>
    </div>
    <?php if ($activeShift): ?>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/pos/refund') ?>" class="btn btn-outline-warning btn-sm">
            <i class="fas fa-undo me-1"></i>Refund
        </a>
        <a href="<?= url('admin/pos/transactions') ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-list me-1"></i>Transactions
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if (!$activeShift): ?>
<!-- No Active Shift -->
<div class="row justify-content-center py-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white py-3">
                <h5 class="mb-0"><i class="fas fa-play-circle me-2"></i>Open New Shift</h5>
            </div>
            <form action="<?= url('admin/pos/open-shift') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-medium">Terminal</label>
                        <select name="terminal_id" class="form-select form-select-lg" required>
                            <?php foreach ($terminals as $terminal): ?>
                            <option value="<?= $terminal['id'] ?>"><?= htmlspecialchars($terminal['terminal_name'] ?? 'Terminal ' . $terminal['id']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Opening Cash Amount</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" name="opening_cash" class="form-control" value="0" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 btn-lg py-3">
                        <i class="fas fa-play me-2"></i>Start Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Active Shift - POS Terminal -->
<div class="row g-3 pos-terminal">
    <!-- Left: Products -->
    <div class="col-lg-8">
        <!-- Daily Summary Widget -->
        <?php if ($dailySummary): ?>
        <div class="summary-widget p-3 mb-3">
            <div class="row text-center">
                <div class="col">
                    <div class="h4 mb-0"><?= $dailySummary['transactions'] ?? 0 ?></div>
                    <small>Sales</small>
                </div>
                <div class="col">
                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($dailySummary['total_sales'] ?? 0, 0) ?></div>
                    <small>Revenue</small>
                </div>
                <div class="col">
                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($dailySummary['total_cash'] ?? 0, 0) ?></div>
                    <small>Cash</small>
                </div>
                <div class="col">
                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($dailySummary['total_card'] ?? 0, 0) ?></div>
                    <small>Card</small>
                </div>
                <div class="col">
                    <div class="h4 mb-0"><?= CURRENCY_SYMBOL ?><?= number_format($dailySummary['avg_transaction'] ?? 0, 0) ?></div>
                    <small>Avg Sale</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="product-search" class="form-control" placeholder="Search or scan barcode... (F1)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <input type="text" id="barcode-input" class="form-control" placeholder="Barcode/SKU">
                            <button class="btn btn-outline-secondary" type="button" onclick="lookupBarcode()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge bg-success p-2">
                            <i class="fas fa-clock me-1"></i><?= $activeShift['opening_time'] ? date('h:i A', strtotime($activeShift['opening_time'])) : date('h:i A') ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Category Tabs -->
                <div class="category-tabs">
                    <span class="category-tab active" data-category="all">All Products</span>
                    <?php foreach ($categories as $cat): ?>
                    <span class="category-tab" data-category="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></span>
                    <?php endforeach; ?>
                </div>

                <!-- Products Grid -->
                <div id="products-grid" class="row g-2" style="max-height: 50vh; overflow-y: auto;">
                    <?php foreach ($products as $product): ?>
                    <div class="col-xl-2 col-lg-3 col-md-3 col-4 product-item <?= $product['stock_quantity'] < 1 ? 'out-of-stock' : '' ?>"
                         data-id="<?= $product['id'] ?>"
                         data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                         data-price="<?= $product['price'] ?>"
                         data-stock="<?= $product['stock_quantity'] ?>"
                         data-sku="<?= htmlspecialchars($product['sku'] ?? '', ENT_QUOTES) ?>"
                         data-category="<?= $product['category_id'] ?? '' ?>">
                        <div class="card product-card h-100" onclick="addToCart(this.parentElement)">
                            <div class="card-body text-center p-2">
                                <?php if ($product['image']): ?>
                                <img src="<?= url('uploads/products/' . $product['image']) ?>" class="img-fluid mb-1 rounded" style="height: 50px; object-fit: contain;">
                                <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center mb-1 rounded" style="height: 50px;">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div class="small text-truncate" title="<?= htmlspecialchars($product['name']) ?>"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="text-accent fw-bold small"><?= CURRENCY_SYMBOL ?><?= number_format($product['price'], 0) ?></div>
                                <small class="text-muted" style="font-size: 10px;">Stock: <?= $product['stock_quantity'] ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Cart & Actions -->
    <div class="col-lg-4">
        <!-- Held Orders -->
        <?php if (!empty($heldOrders)): ?>
        <div class="card mb-3">
            <div class="card-header bg-warning text-dark py-2">
                <i class="fas fa-pause-circle me-1"></i> Held Orders (<?= count($heldOrders) ?>)
            </div>
            <div class="card-body p-2" style="max-height: 120px; overflow-y: auto;">
                <?php foreach ($heldOrders as $held): ?>
                <div class="held-order" onclick="recallOrder(<?= $held['id'] ?>)">
                    <div class="d-flex justify-content-between">
                        <strong><?= $held['hold_number'] ?></strong>
                        <span><?= CURRENCY_SYMBOL ?><?= number_format($held['total'], 0) ?></span>
                    </div>
                    <small class="text-muted"><?= count($held['items']) ?> items - <?= date('h:i A', strtotime($held['created_at'])) ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cart -->
        <div class="card">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-shopping-cart me-2"></i>Current Sale</span>
                <span class="badge bg-light text-dark" id="cart-count">0 items</span>
            </div>
            <div class="card-body p-0">
                <!-- Customer Search -->
                <div class="p-2 border-bottom">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="customer-search" class="form-control" placeholder="Search customer by phone/name...">
                    </div>
                    <div id="customer-results" class="mt-1" style="display: none;"></div>
                    <div id="selected-customer" class="mt-1 small" style="display: none;"></div>
                </div>

                <!-- Cart Items -->
                <div id="cart-items" style="max-height: 200px; overflow-y: auto;">
                    <div id="empty-cart" class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-2x mb-2 d-block"></i>
                        Cart is empty
                    </div>
                    <div id="cart-body"></div>
                </div>

                <!-- Totals -->
                <div class="border-top p-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal"><?= CURRENCY_SYMBOL ?>0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small mb-1">
                        <span>Discount:</span>
                        <div class="input-group input-group-sm" style="width: 100px;">
                            <input type="number" id="discount-amount" class="form-control form-control-sm text-end" value="0" min="0" onchange="updateTotals()">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Tax (<?= TAX_RATE ?? 0 ?>%):</span>
                        <span id="cart-tax"><?= CURRENCY_SYMBOL ?>0</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold h5 mb-0 pt-2 border-top">
                        <span>Total:</span>
                        <span id="cart-total" class="text-accent"><?= CURRENCY_SYMBOL ?>0</span>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="card-footer">
                <div class="mb-2">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="payment-type" id="pay-single" value="single" checked>
                        <label class="btn btn-outline-primary btn-sm" for="pay-single">Single Payment</label>
                        <input type="radio" class="btn-check" name="payment-type" id="pay-split" value="split">
                        <label class="btn btn-outline-primary btn-sm" for="pay-split">Split Payment (F8)</label>
                    </div>
                </div>

                <!-- Single Payment -->
                <div id="single-payment">
                    <select id="payment-method" class="form-select form-select-sm mb-2">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_banking">Mobile Banking</option>
                    </select>
                    <!-- Quick Cash Buttons -->
                    <div id="quick-cash-section" class="mb-2">
                        <div class="d-flex gap-1 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="setQuickCash(100)">100</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="setQuickCash(500)">500</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="setQuickCash(1000)">1000</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-cash-btn" onclick="setQuickCash(2000)">2000</button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="setExactAmount()">Exact</button>
                        </div>
                        <input type="number" id="cash-received" class="form-control form-control-sm mt-1" placeholder="Cash received" style="display: none;">
                    </div>
                </div>

                <!-- Split Payment -->
                <div id="split-payment" style="display: none;">
                    <div class="split-payment-row">
                        <span style="width: 60px;">Cash:</span>
                        <input type="number" id="split-cash" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="split-payment-row">
                        <span style="width: 60px;">Card:</span>
                        <input type="number" id="split-card" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="split-payment-row">
                        <span style="width: 60px;">Mobile:</span>
                        <input type="number" id="split-mobile" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span>Remaining:</span>
                        <span id="split-remaining" class="text-danger fw-bold"><?= CURRENCY_SYMBOL ?>0</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row g-2 mt-2">
                    <div class="col-4">
                        <button type="button" class="btn btn-secondary w-100" onclick="clearCart()" title="F4">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-warning w-100" onclick="holdOrder()" title="F2">
                            <i class="fas fa-pause"></i>
                        </button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-success w-100" onclick="completeSale()" id="btn-complete" title="F12">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-body py-2">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-info w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#recallModal" title="F3">
                            <i class="fas fa-history me-1"></i>Recall (F3)
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-danger w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
                            <i class="fas fa-stop me-1"></i>End Shift
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recall Orders Modal -->
<div class="modal fade" id="recallModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history me-2"></i>Held Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="held-orders-list">
                <p class="text-muted text-center">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Close Shift Modal -->
<div class="modal fade" id="closeShiftModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/pos/close-shift') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Close Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="d-flex justify-content-between"><span>Opening Cash:</span><strong><?= CURRENCY_SYMBOL ?><?= number_format($activeShift['opening_cash'], 2) ?></strong></div>
                        <div class="d-flex justify-content-between"><span>Expected Cash:</span><strong id="expected-cash"><?= CURRENCY_SYMBOL ?>0.00</strong></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Actual Closing Cash</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" name="closing_cash" class="form-control" required min="0" step="0.01">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Close Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sale Complete Modal -->
<div class="modal fade" id="saleCompleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Sale Complete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Transaction Successful!</h4>
                <p class="mb-1">Receipt #: <strong id="receipt-number"></strong></p>
                <p class="h3 text-accent" id="sale-total"></p>
                <div id="change-display" class="alert alert-warning" style="display: none;">
                    Change: <strong id="change-amount"></strong>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="print-receipt-btn" class="btn btn-primary" target="_blank">
                    <i class="fas fa-print me-1"></i>Print Receipt
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const cart = [];
let selectedCustomer = null;
const TAX_RATE = <?= TAX_RATE ?? 0 ?>;
const CURRENCY = '<?= CURRENCY_SYMBOL ?>';
const SHIFT_ID = <?= $activeShift['id'] ?>;
const CSRF_TOKEN = '<?= Session::getCsrfToken() ?>';
const OPENING_CASH = <?= $activeShift['opening_cash'] ?>;

// Category filtering
document.querySelectorAll('.category-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const category = this.dataset.category;
        document.querySelectorAll('.product-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Product search
document.getElementById('product-search').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
        const name = item.dataset.name.toLowerCase();
        const sku = item.dataset.sku.toLowerCase();
        item.style.display = (name.includes(search) || sku.includes(search)) ? '' : 'none';
    });
});

// Barcode lookup
document.getElementById('barcode-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        lookupBarcode();
    }
});

function lookupBarcode() {
    const code = document.getElementById('barcode-input').value.trim();
    if (!code) return;

    fetch(`<?= url('admin/pos/barcode-lookup') ?>?code=${encodeURIComponent(code)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.product) {
                const p = data.product;
                addToCartDirect(p.id, p.name, p.price, p.stock_quantity);
                document.getElementById('barcode-input').value = '';
            } else {
                alert('Product not found');
            }
        });
}

// Customer search
let customerSearchTimeout;
document.getElementById('customer-search').addEventListener('input', function() {
    clearTimeout(customerSearchTimeout);
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('customer-results').style.display = 'none';
        return;
    }
    customerSearchTimeout = setTimeout(() => {
        fetch(`<?= url('admin/pos/search-customers') ?>?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (data.customers && data.customers.length) {
                    let html = '<div class="list-group list-group-flush">';
                    data.customers.forEach(c => {
                        html += `<a href="#" class="list-group-item list-group-item-action py-1 small" onclick="selectCustomer(${c.id}, '${c.name}', '${c.phone}'); return false;">
                            <strong>${c.name}</strong> - ${c.phone}<br>
                            <small class="text-muted">${c.order_count} orders, ${CURRENCY}${c.total_spent} spent</small>
                        </a>`;
                    });
                    html += '</div>';
                    document.getElementById('customer-results').innerHTML = html;
                    document.getElementById('customer-results').style.display = 'block';
                } else {
                    document.getElementById('customer-results').style.display = 'none';
                }
            });
    }, 300);
});

function selectCustomer(id, name, phone) {
    selectedCustomer = { id, name, phone };
    document.getElementById('customer-search').value = '';
    document.getElementById('customer-results').style.display = 'none';
    document.getElementById('selected-customer').innerHTML = `<i class="fas fa-user-check text-success me-1"></i>${name} (${phone}) <a href="#" onclick="clearCustomer(); return false;"><i class="fas fa-times text-danger"></i></a>`;
    document.getElementById('selected-customer').style.display = 'block';
}

function clearCustomer() {
    selectedCustomer = null;
    document.getElementById('selected-customer').style.display = 'none';
}

// Add to cart
function addToCart(element) {
    const id = parseInt(element.dataset.id);
    const name = element.dataset.name;
    const price = parseFloat(element.dataset.price);
    const stock = parseInt(element.dataset.stock);
    addToCartDirect(id, name, price, stock);
}

function addToCartDirect(id, name, price, stock) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        if (existing.quantity >= stock) {
            alert('Not enough stock!');
            return;
        }
        existing.quantity++;
    } else {
        if (stock < 1) {
            alert('Out of stock!');
            return;
        }
        cart.push({ id, name, price, quantity: 1, stock, discount: 0 });
    }
    renderCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQty = item.quantity + change;
    if (newQty < 1) { removeFromCart(index); return; }
    if (newQty > item.stock) { alert('Not enough stock!'); return; }
    item.quantity = newQty;
    renderCart();
}

function updateItemDiscount(index, discount) {
    cart[index].discount = parseFloat(discount) || 0;
    updateTotals();
}

function renderCart() {
    const body = document.getElementById('cart-body');
    const empty = document.getElementById('empty-cart');

    if (cart.length === 0) {
        empty.style.display = 'block';
        body.innerHTML = '';
        document.getElementById('cart-count').textContent = '0 items';
        updateTotals();
        return;
    }

    empty.style.display = 'none';
    let html = '';
    let totalItems = 0;
    cart.forEach((item, index) => {
        totalItems += item.quantity;
        const itemTotal = (item.price * item.quantity) - item.discount;
        html += `<div class="cart-item px-2">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="small fw-medium">${item.name}</div>
                    <div class="text-muted small">${CURRENCY}${item.price.toFixed(0)} x ${item.quantity}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold small">${CURRENCY}${itemTotal.toFixed(0)}</div>
                    <button class="btn btn-link btn-sm text-danger p-0" onclick="removeFromCart(${index})"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary py-0 px-2" onclick="updateQuantity(${index}, -1)">-</button>
                    <span class="btn btn-outline-secondary py-0 px-2 disabled">${item.quantity}</span>
                    <button class="btn btn-outline-secondary py-0 px-2" onclick="updateQuantity(${index}, 1)">+</button>
                </div>
                <input type="number" class="form-control form-control-sm item-discount-input" value="${item.discount}" min="0" onchange="updateItemDiscount(${index}, this.value)" placeholder="Disc">
            </div>
        </div>`;
    });
    body.innerHTML = html;
    document.getElementById('cart-count').textContent = totalItems + ' items';
    updateTotals();
}

function updateTotals() {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += (item.price * item.quantity) - item.discount;
    });
    const discount = parseFloat(document.getElementById('discount-amount').value) || 0;
    const taxable = Math.max(0, subtotal - discount);
    const tax = taxable * (TAX_RATE / 100);
    const total = taxable + tax;

    document.getElementById('cart-subtotal').textContent = CURRENCY + subtotal.toFixed(0);
    document.getElementById('cart-tax').textContent = CURRENCY + tax.toFixed(0);
    document.getElementById('cart-total').textContent = CURRENCY + total.toFixed(0);

    // Update split remaining
    const splitCash = parseFloat(document.getElementById('split-cash').value) || 0;
    const splitCard = parseFloat(document.getElementById('split-card').value) || 0;
    const splitMobile = parseFloat(document.getElementById('split-mobile').value) || 0;
    const remaining = total - splitCash - splitCard - splitMobile;
    document.getElementById('split-remaining').textContent = CURRENCY + remaining.toFixed(0);
    document.getElementById('split-remaining').className = remaining > 0 ? 'text-danger fw-bold' : 'text-success fw-bold';
}

// Payment type toggle
document.querySelectorAll('input[name="payment-type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('single-payment').style.display = this.value === 'single' ? '' : 'none';
        document.getElementById('split-payment').style.display = this.value === 'split' ? '' : 'none';
    });
});

// Split payment inputs
['split-cash', 'split-card', 'split-mobile'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateTotals);
});

// Quick cash buttons
function setQuickCash(amount) {
    document.getElementById('cash-received').value = amount;
    document.getElementById('cash-received').style.display = 'block';
}

function setExactAmount() {
    const total = parseFloat(document.getElementById('cart-total').textContent.replace(CURRENCY, ''));
    document.getElementById('cash-received').value = total;
    document.getElementById('cash-received').style.display = 'block';
}

// Show cash input when cash is selected
document.getElementById('payment-method').addEventListener('change', function() {
    document.getElementById('quick-cash-section').style.display = this.value === 'cash' ? '' : 'none';
});

function clearCart() {
    if (cart.length === 0) return;
    if (confirm('Clear cart?')) {
        cart.length = 0;
        renderCart();
        document.getElementById('discount-amount').value = 0;
        clearCustomer();
    }
}

function holdOrder() {
    if (cart.length === 0) { alert('Cart is empty!'); return; }

    fetch('<?= url('admin/pos/hold-order') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            csrf_token: CSRF_TOKEN,
            items: cart,
            customer_phone: selectedCustomer?.phone
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Order held: ' + data.hold_id);
            cart.length = 0;
            renderCart();
            clearCustomer();
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function recallOrder(holdId) {
    fetch(`<?= url('admin/pos/recall-order/') ?>${holdId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                cart.length = 0;
                data.items.forEach(item => cart.push(item));
                renderCart();
                if (data.customer_phone) {
                    document.getElementById('customer-search').value = data.customer_phone;
                }
                bootstrap.Modal.getInstance(document.getElementById('recallModal'))?.hide();
                location.reload();
            }
        });
}

// Load held orders in modal
document.getElementById('recallModal').addEventListener('show.bs.modal', function() {
    fetch('<?= url('admin/pos/held-orders') ?>')
        .then(r => r.json())
        .then(data => {
            if (data.orders && data.orders.length) {
                let html = '';
                data.orders.forEach(order => {
                    html += `<div class="held-order" onclick="recallOrder(${order.id})">
                        <div class="d-flex justify-content-between">
                            <strong>${order.hold_number}</strong>
                            <span>${CURRENCY}${order.total.toFixed(0)}</span>
                        </div>
                        <small class="text-muted">${order.items.length} items</small>
                        <button class="btn btn-sm btn-outline-danger float-end" onclick="event.stopPropagation(); deleteHeldOrder(${order.id})"><i class="fas fa-trash"></i></button>
                    </div>`;
                });
                document.getElementById('held-orders-list').innerHTML = html;
            } else {
                document.getElementById('held-orders-list').innerHTML = '<p class="text-muted text-center">No held orders</p>';
            }
        });
});

function deleteHeldOrder(id) {
    if (confirm('Delete this held order?')) {
        fetch(`<?= url('admin/pos/delete-held/') ?>${id}`).then(() => location.reload());
    }
}

function completeSale() {
    if (cart.length === 0) { alert('Cart is empty!'); return; }

    const isSplit = document.querySelector('input[name="payment-type"]:checked').value === 'split';
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity) - item.discount, 0);
    const discount = parseFloat(document.getElementById('discount-amount').value) || 0;
    const taxable = Math.max(0, subtotal - discount);
    const tax = taxable * (TAX_RATE / 100);
    const total = taxable + tax;

    let paymentData = {};
    if (isSplit) {
        const splitCash = parseFloat(document.getElementById('split-cash').value) || 0;
        const splitCard = parseFloat(document.getElementById('split-card').value) || 0;
        const splitMobile = parseFloat(document.getElementById('split-mobile').value) || 0;
        if (splitCash + splitCard + splitMobile < total) {
            alert('Payment amount is less than total!');
            return;
        }
        paymentData = { split: true, cash: splitCash, card: splitCard, mobile: splitMobile };
    } else {
        const method = document.getElementById('payment-method').value;
        paymentData = { split: false, method };
        if (method === 'cash') {
            const received = parseFloat(document.getElementById('cash-received').value) || 0;
            if (received < total) {
                const input = prompt('Enter cash received:', total.toFixed(0));
                if (!input) return;
                paymentData.cash_received = parseFloat(input);
                if (paymentData.cash_received < total) {
                    alert('Insufficient amount!');
                    return;
                }
            } else {
                paymentData.cash_received = received;
            }
        }
    }

    const btn = document.getElementById('btn-complete');
    btn.disabled = true;

    const items = cart.map(item => ({
        product_id: item.id,
        product_name: item.name,
        quantity: item.quantity,
        unit_price: item.price,
        discount: item.discount
    }));

    fetch('<?= url('admin/pos/sale') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            csrf_token: CSRF_TOKEN,
            items: JSON.stringify(items),
            discount_amount: discount,
            tax_amount: tax,
            payment_method: paymentData.split ? 'split' : paymentData.method,
            cash_received: paymentData.cash_received || paymentData.cash || 0,
            card_amount: paymentData.card || 0,
            mobile_amount: paymentData.mobile || 0,
            customer_id: selectedCustomer?.id || '',
            customer_phone: selectedCustomer?.phone || ''
        })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            document.getElementById('receipt-number').textContent = data.transaction_number;
            document.getElementById('sale-total').textContent = CURRENCY + total.toFixed(0);
            document.getElementById('print-receipt-btn').href = '<?= url('admin/pos/receipt/') ?>' + data.transaction_id;

            if (data.change_amount > 0) {
                document.getElementById('change-display').style.display = 'block';
                document.getElementById('change-amount').textContent = CURRENCY + data.change_amount.toFixed(0);
            } else {
                document.getElementById('change-display').style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('saleCompleteModal')).show();
            cart.length = 0;
            renderCart();
            clearCustomer();
            document.getElementById('discount-amount').value = 0;
            document.getElementById('cash-received').value = '';
            document.getElementById('split-cash').value = 0;
            document.getElementById('split-card').value = 0;
            document.getElementById('split-mobile').value = 0;
        } else {
            alert(data.message || 'Error processing sale');
        }
    })
    .catch(err => {
        btn.disabled = false;
        alert('Error: ' + err.message);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

    switch(e.key) {
        case 'F1':
            e.preventDefault();
            document.getElementById('product-search').focus();
            break;
        case 'F2':
            e.preventDefault();
            holdOrder();
            break;
        case 'F3':
            e.preventDefault();
            new bootstrap.Modal(document.getElementById('recallModal')).show();
            break;
        case 'F4':
            e.preventDefault();
            clearCart();
            break;
        case 'F8':
            e.preventDefault();
            document.getElementById('pay-split').click();
            break;
        case 'F12':
            e.preventDefault();
            completeSale();
            break;
    }
});

// Initial render
renderCart();
</script>
<?php endif; ?>
