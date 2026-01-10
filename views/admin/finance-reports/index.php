<div class="page-header">
    <h1>Financial Reports</h1>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">This Month Revenue</h6>
                        <h3 class="mb-0"><?= formatPrice($summary['this_month']['revenue']) ?></h3>
                        <?php if ($summary['revenue_growth'] != 0): ?>
                        <small class="<?= $summary['revenue_growth'] > 0 ? 'text-white' : 'text-warning' ?>">
                            <i class="fas fa-<?= $summary['revenue_growth'] > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                            <?= number_format(abs($summary['revenue_growth']), 1) ?>% vs last month
                        </small>
                        <?php endif; ?>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">This Month Expenses</h6>
                        <h3 class="mb-0"><?= formatPrice($summary['this_month']['expenses']) ?></h3>
                    </div>
                    <i class="fas fa-receipt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">This Month Profit</h6>
                        <h3 class="mb-0"><?= formatPrice($summary['this_month']['profit']) ?></h3>
                    </div>
                    <i class="fas fa-coins fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark-50 mb-1">Outstanding Payables</h6>
                        <h3 class="mb-0"><?= formatPrice($summary['outstanding_payables']) ?></h3>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Report Links -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Reports</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="<?= url('admin/finance-reports/profit-loss') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-chart-pie text-primary me-2"></i>
                            <strong>Profit & Loss</strong>
                            <br><small class="text-muted">Revenue, costs, and profitability</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="<?= url('admin/finance-reports/cash-flow') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-money-bill-wave text-success me-2"></i>
                            <strong>Cash Flow</strong>
                            <br><small class="text-muted">Money in and money out</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="<?= url('admin/finance-reports/expenses') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-receipt text-danger me-2"></i>
                            <strong>Expense Analysis</strong>
                            <br><small class="text-muted">Expense breakdown by category</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Quick Stats</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">This Month Purchases</td>
                        <td class="text-end"><strong><?= formatPrice($summary['this_month']['purchases']) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Last Month Revenue</td>
                        <td class="text-end"><strong><?= formatPrice($summary['last_month_revenue']) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i> Revenue Trend (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueTrendChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueTrendData = <?= json_encode($revenueTrend) ?>;

new Chart(document.getElementById('revenueTrendChart'), {
    type: 'line',
    data: {
        labels: revenueTrendData.map(d => d.month_label),
        datasets: [{
            label: 'Revenue',
            data: revenueTrendData.map(d => d.revenue),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Orders',
            data: revenueTrendData.map(d => d.order_count),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'transparent',
            yAxisID: 'y1',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Revenue (৳)'
                }
            },
            y1: {
                beginAtZero: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Orders'
                },
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});
</script>
