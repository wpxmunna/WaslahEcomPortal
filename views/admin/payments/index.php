<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Payment Methods</h1>
        <a href="<?= url('admin/payments/settings') ?>" class="btn btn-primary">
            <i class="fas fa-cog me-2"></i>Settings
        </a>
    </div>

    <!-- Payment Methods -->
    <div class="row g-4 mb-4">
        <?php foreach ($paymentMethods as $method): ?>
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 <?= $method['enabled'] ? 'border-success' : 'border-secondary' ?>">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="<?= $method['icon'] ?> fa-3x <?= $method['enabled'] ? 'text-success' : 'text-muted' ?>"></i>
                    </div>
                    <h5 class="card-title"><?= $method['name'] ?></h5>
                    <p class="card-text text-muted small"><?= $method['description'] ?></p>
                    <span class="badge <?= $method['enabled'] ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $method['enabled'] ? 'Enabled' : 'Disabled' ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Transactions</h5>
        </div>
        <div class="card-body">
            <?php if (empty($transactions)): ?>
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No transactions yet</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td><code><?= $txn['transaction_id'] ?? 'N/A' ?></code></td>
                            <td>
                                <a href="<?= url('admin/orders/view/' . $txn['order_id']) ?>">
                                    <?= $txn['order_number'] ?>
                                </a>
                            </td>
                            <td><?= sanitize($txn['customer_name'] ?? 'Guest') ?></td>
                            <td class="fw-bold"><?= formatPrice($txn['amount']) ?></td>
                            <td><?= ucfirst($txn['payment_method'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $statusClass = match($txn['status'] ?? '') {
                                    'completed', 'paid' => 'bg-success',
                                    'pending' => 'bg-warning',
                                    'failed' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst($txn['status'] ?? 'Unknown') ?>
                                </span>
                            </td>
                            <td><?= formatDate($txn['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
