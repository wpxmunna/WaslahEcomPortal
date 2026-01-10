<?php
/**
 * POS Transactions View
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-receipt"></i> POS Transactions</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/pos') ?>">POS</a></li>
                <li class="breadcrumb-item active">Transactions</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= $filters['date'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select">
                    <option value="">All Methods</option>
                    <option value="cash" <?= ($filters['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="card" <?= ($filters['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Card</option>
                    <option value="mobile_banking" <?= ($filters['payment_method'] ?? '') === 'mobile_banking' ? 'selected' : '' ?>>Mobile Banking</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Receipt # or Phone">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Search</button>
                <a href="<?= url('admin/pos/transactions') ?>" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="quick-stats mb-4">
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-info text-white">
            <i class="fas fa-receipt"></i>
        </div>
        <div class="stat-content">
            <h4><?= count($transactions) ?></h4>
            <p>Total Transactions</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-success text-white">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
            <h4><?= CURRENCY_SYMBOL ?><?= number_format(array_sum(array_column($transactions, 'total_amount')), 2) ?></h4>
            <p>Total Sales</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-warning text-white">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-content">
            <h4><?= CURRENCY_SYMBOL ?><?= number_format(array_sum(array_map(function($t) { return $t['payment_method'] === 'cash' ? $t['total_amount'] : 0; }, $transactions)), 2) ?></h4>
            <p>Cash Sales</p>
        </div>
    </div>
    <div class="quick-stat-item">
        <div class="stat-icon bg-gradient-primary text-white">
            <i class="fas fa-credit-card"></i>
        </div>
        <div class="stat-content">
            <h4><?= CURRENCY_SYMBOL ?><?= number_format(array_sum(array_map(function($t) { return $t['payment_method'] !== 'cash' ? $t['total_amount'] : 0; }, $transactions)), 2) ?></h4>
            <p>Card/Digital</p>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Transaction List
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Receipt #</th>
                    <th>Date/Time</th>
                    <th>Items</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Cashier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-receipt"></i></div>
                                <h4>No Transactions</h4>
                                <p>No transactions found for the selected filters</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($txn['receipt_number']) ?></strong>
                                <?php if ($txn['status'] === 'refunded'): ?>
                                    <span class="badge bg-danger">Refunded</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('M d, Y', strtotime($txn['created_at'])) ?><br>
                                <small class="text-muted"><?= date('h:i A', strtotime($txn['created_at'])) ?></small>
                            </td>
                            <td><?= $txn['total_items'] ?? '-' ?></td>
                            <td><?= CURRENCY_SYMBOL ?><?= number_format($txn['subtotal'], 2) ?></td>
                            <td>
                                <?php if ($txn['discount_amount'] > 0): ?>
                                    <span class="text-danger">-<?= CURRENCY_SYMBOL ?><?= number_format($txn['discount_amount'], 2) ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= CURRENCY_SYMBOL ?><?= number_format($txn['tax_amount'], 2) ?></td>
                            <td><strong><?= CURRENCY_SYMBOL ?><?= number_format($txn['total_amount'], 2) ?></strong></td>
                            <td>
                                <?php
                                $paymentBadges = [
                                    'cash' => 'success',
                                    'card' => 'primary',
                                    'mobile_banking' => 'info'
                                ];
                                $badge = $paymentBadges[$txn['payment_method']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $badge ?>">
                                    <?= ucfirst(str_replace('_', ' ', $txn['payment_method'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($txn['cashier_name'] ?? 'N/A') ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= url('admin/pos/receipt/' . $txn['id']) ?>" class="btn btn-sm btn-info" target="_blank" title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php if ($txn['status'] !== 'refunded'): ?>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="refundTransaction(<?= $txn['id'] ?>)" title="Refund">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
function refundTransaction(id) {
    if (!confirm('Are you sure you want to refund this transaction? This action cannot be undone.')) {
        return;
    }

    fetch('<?= url('admin/pos/refund/') ?>' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Session::getCsrfToken() ?>'
        },
        body: JSON.stringify({
            csrf_token: '<?= Session::getCsrfToken() ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Transaction refunded successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to refund transaction'));
        }
    })
    .catch(error => {
        alert('Error processing refund');
        console.error(error);
    });
}
</script>
