<div class="page-header">
    <h1>Expense Analysis</h1>
    <a href="<?= url('admin/finance-reports') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?= url('admin/finance-reports/export/expenses') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Total Expenses</h6>
                <h3 class="mb-0"><?= formatPrice($report['summary']['total'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Expense Count</h6>
                <h3 class="mb-0"><?= $report['summary']['count'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Average Expense</h6>
                <h3 class="mb-0"><?= formatPrice($report['summary']['average'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-1">Highest Expense</h6>
                <h3 class="mb-0"><?= formatPrice($report['summary']['highest'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Charts -->
    <div class="col-lg-8">
        <!-- Monthly Trend -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Monthly Expense Trend</h5>
            </div>
            <div class="card-body">
                <?php if (empty($report['monthly_trend'])): ?>
                <p class="text-muted text-center mb-0">No expense data available</p>
                <?php else: ?>
                <canvas id="monthlyTrendChart" height="250"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Expenses -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i> Top 10 Expenses</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($report['top_expenses'])): ?>
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No expenses recorded</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['top_expenses'] as $expense): ?>
                            <tr>
                                <td>
                                    <strong><?= sanitize($expense['title']) ?></strong>
                                    <br><small class="text-muted"><?= sanitize($expense['expense_number']) ?></small>
                                </td>
                                <td>
                                    <?php if ($expense['category_name']): ?>
                                    <span class="badge" style="background-color: <?= $expense['color'] ?>">
                                        <i class="fas fa-<?= $expense['icon'] ?>"></i>
                                    </span>
                                    <?= sanitize($expense['category_name']) ?>
                                    <?php else: ?>
                                    <span class="text-muted">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($expense['expense_date'])) ?></td>
                                <td class="text-end"><strong><?= formatPrice($expense['total_amount']) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- By Category -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> By Category</h5>
            </div>
            <div class="card-body">
                <?php if (empty($report['by_category']) || array_sum(array_column($report['by_category'], 'total')) == 0): ?>
                <p class="text-muted text-center mb-0">No expense data available</p>
                <?php else: ?>
                <canvas id="categoryChart" height="200"></canvas>
                <hr>
                <div class="small">
                    <?php foreach ($report['by_category'] as $cat): ?>
                    <?php if ($cat['total'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            <span class="badge me-1" style="background-color: <?= $cat['color'] ?>">
                                <i class="fas fa-<?= $cat['icon'] ?>"></i>
                            </span>
                            <?= sanitize($cat['category']) ?>
                        </span>
                        <strong><?= formatPrice($cat['total']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- By Payment Method -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i> By Payment Method</h5>
            </div>
            <div class="card-body">
                <?php if (empty($report['by_payment_method'])): ?>
                <p class="text-muted text-center mb-0">No data available</p>
                <?php else: ?>
                <?php foreach ($report['by_payment_method'] as $method): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span>
                        <?php
                        $icons = [
                            'cash' => 'money-bill',
                            'bank_transfer' => 'university',
                            'mobile_banking' => 'mobile-alt',
                            'card' => 'credit-card',
                            'other' => 'ellipsis-h'
                        ];
                        ?>
                        <i class="fas fa-<?= $icons[$method['payment_method']] ?? 'ellipsis-h' ?> text-muted me-2"></i>
                        <?= ucfirst(str_replace('_', ' ', $method['payment_method'])) ?>
                        <small class="text-muted">(<?= $method['count'] ?>)</small>
                    </span>
                    <strong><?= formatPrice($method['total']) ?></strong>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if (!empty($report['monthly_trend'])): ?>
const monthlyData = <?= json_encode($report['monthly_trend']) ?>;
new Chart(document.getElementById('monthlyTrendChart'), {
    type: 'bar',
    data: {
        labels: monthlyData.map(d => d.month_label),
        datasets: [{
            label: 'Expenses',
            data: monthlyData.map(d => d.total),
            backgroundColor: 'rgba(220, 53, 69, 0.7)',
            borderColor: 'rgb(220, 53, 69)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
<?php endif; ?>

<?php
$categoryData = array_filter($report['by_category'], fn($c) => $c['total'] > 0);
if (!empty($categoryData)):
?>
const categoryData = <?= json_encode(array_values($categoryData)) ?>;
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: categoryData.map(d => d.category),
        datasets: [{
            data: categoryData.map(d => d.total),
            backgroundColor: categoryData.map(d => d.color)
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
<?php endif; ?>
</script>
