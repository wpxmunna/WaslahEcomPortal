<div class="page-header">
    <h1>Profit & Loss Statement</h1>
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
                <a href="<?= url('admin/finance-reports/export/profit-loss') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- P&L Statement -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    Profit & Loss Statement
                    <small class="float-end"><?= date('M d, Y', strtotime($startDate)) ?> - <?= date('M d, Y', strtotime($endDate)) ?></small>
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0">
                    <!-- Revenue Section -->
                    <thead class="bg-success bg-opacity-10">
                        <tr>
                            <th colspan="2" class="text-success">
                                <i class="fas fa-arrow-up me-2"></i> REVENUE
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">Gross Revenue (<?= $report['revenue']['order_count'] ?> orders)</td>
                            <td class="text-end"><?= formatPrice($report['revenue']['gross']) ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4">Less: Discounts</td>
                            <td class="text-end text-danger">-<?= formatPrice($report['revenue']['discounts']) ?></td>
                        </tr>
                        <tr class="bg-light">
                            <td class="ps-4"><strong>Net Revenue</strong></td>
                            <td class="text-end"><strong><?= formatPrice($report['revenue']['net']) ?></strong></td>
                        </tr>
                    </tbody>

                    <!-- COGS Section -->
                    <thead class="bg-warning bg-opacity-10">
                        <tr>
                            <th colspan="2" class="text-warning">
                                <i class="fas fa-boxes me-2"></i> COST OF GOODS SOLD
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">Cost of Products Sold</td>
                            <td class="text-end"><?= formatPrice($report['cogs']) ?></td>
                        </tr>
                        <tr class="bg-light">
                            <td class="ps-4"><strong>Total COGS</strong></td>
                            <td class="text-end"><strong><?= formatPrice($report['cogs']) ?></strong></td>
                        </tr>
                    </tbody>

                    <!-- Gross Profit -->
                    <thead class="bg-primary bg-opacity-10">
                        <tr>
                            <th class="text-primary">GROSS PROFIT</th>
                            <th class="text-end text-primary">
                                <?= formatPrice($report['gross_profit']) ?>
                                <br><small class="text-muted"><?= number_format($report['gross_margin'], 1) ?>% margin</small>
                            </th>
                        </tr>
                    </thead>

                    <!-- Operating Expenses -->
                    <thead class="bg-danger bg-opacity-10">
                        <tr>
                            <th colspan="2" class="text-danger">
                                <i class="fas fa-arrow-down me-2"></i> OPERATING EXPENSES
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report['expenses']['by_category'])): ?>
                        <tr>
                            <td class="ps-4 text-muted" colspan="2">No expenses recorded</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($report['expenses']['by_category'] as $cat): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge me-2" style="background-color: <?= $cat['color'] ?>">
                                    <i class="fas fa-<?= $cat['icon'] ?>"></i>
                                </span>
                                <?= sanitize($cat['category']) ?>
                            </td>
                            <td class="text-end"><?= formatPrice($cat['amount']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <tr class="bg-light">
                            <td class="ps-4"><strong>Total Operating Expenses</strong></td>
                            <td class="text-end"><strong><?= formatPrice($report['expenses']['total']) ?></strong></td>
                        </tr>
                    </tbody>

                    <!-- Net Profit -->
                    <thead class="<?= $report['operating_profit'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                        <tr>
                            <th>NET OPERATING PROFIT</th>
                            <th class="text-end">
                                <?= formatPrice($report['operating_profit']) ?>
                                <br><small><?= number_format($report['net_margin'], 1) ?>% net margin</small>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Summary Cards -->
        <div class="card mb-4 <?= $report['operating_profit'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50">Net Profit/Loss</h6>
                <h2 class="mb-0"><?= formatPrice($report['operating_profit']) ?></h2>
                <p class="mb-0"><?= number_format($report['net_margin'], 1) ?>% margin</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Expense Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (empty($report['expenses']['by_category'])): ?>
                <p class="text-muted text-center mb-0">No expenses recorded</p>
                <?php else: ?>
                <canvas id="expenseChart" height="200"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Key Metrics</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Order Count</td>
                        <td class="text-end"><strong><?= $report['revenue']['order_count'] ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Avg Order Value</td>
                        <td class="text-end">
                            <strong>
                                <?= $report['revenue']['order_count'] > 0
                                    ? formatPrice($report['revenue']['net'] / $report['revenue']['order_count'])
                                    : formatPrice(0) ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gross Margin</td>
                        <td class="text-end"><strong><?= number_format($report['gross_margin'], 1) ?>%</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Net Margin</td>
                        <td class="text-end"><strong><?= number_format($report['net_margin'], 1) ?>%</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Expense Count</td>
                        <td class="text-end"><strong><?= $report['expenses']['count'] ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($report['expenses']['by_category'])): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const expenseData = <?= json_encode($report['expenses']['by_category']) ?>;

new Chart(document.getElementById('expenseChart'), {
    type: 'doughnut',
    data: {
        labels: expenseData.map(d => d.category),
        datasets: [{
            data: expenseData.map(d => d.amount),
            backgroundColor: expenseData.map(d => d.color)
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12
                }
            }
        }
    }
});
</script>
<?php endif; ?>
