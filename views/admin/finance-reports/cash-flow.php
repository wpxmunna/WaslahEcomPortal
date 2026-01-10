<div class="page-header">
    <h1>Cash Flow Statement</h1>
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
                <a href="<?= url('admin/finance-reports/export/cash-flow') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-outline-success w-100">
                    <i class="fas fa-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Cash Flow Statement -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    Cash Flow Statement
                    <small class="float-end"><?= date('M d, Y', strtotime($startDate)) ?> - <?= date('M d, Y', strtotime($endDate)) ?></small>
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0">
                    <!-- Cash In Section -->
                    <thead class="bg-success bg-opacity-10">
                        <tr>
                            <th colspan="2" class="text-success">
                                <i class="fas fa-arrow-down me-2"></i> CASH IN (Receipts)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <i class="fas fa-shopping-cart text-muted me-2"></i>
                                Order Revenue (Paid Orders)
                            </td>
                            <td class="text-end text-success"><?= formatPrice($report['cash_in']['orders']) ?></td>
                        </tr>
                        <tr class="bg-success bg-opacity-10">
                            <td class="ps-4"><strong>Total Cash In</strong></td>
                            <td class="text-end"><strong class="text-success"><?= formatPrice($report['cash_in']['total']) ?></strong></td>
                        </tr>
                    </tbody>

                    <!-- Cash Out Section -->
                    <thead class="bg-danger bg-opacity-10">
                        <tr>
                            <th colspan="2" class="text-danger">
                                <i class="fas fa-arrow-up me-2"></i> CASH OUT (Payments)
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">
                                <i class="fas fa-receipt text-muted me-2"></i>
                                Expenses Paid
                            </td>
                            <td class="text-end text-danger"><?= formatPrice($report['cash_out']['expenses']) ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <i class="fas fa-truck-loading text-muted me-2"></i>
                                Supplier Payments
                            </td>
                            <td class="text-end text-danger"><?= formatPrice($report['cash_out']['supplier_payments']) ?></td>
                        </tr>
                        <tr>
                            <td class="ps-4">
                                <i class="fas fa-undo text-muted me-2"></i>
                                Customer Refunds
                            </td>
                            <td class="text-end text-danger"><?= formatPrice($report['cash_out']['refunds']) ?></td>
                        </tr>
                        <tr class="bg-danger bg-opacity-10">
                            <td class="ps-4"><strong>Total Cash Out</strong></td>
                            <td class="text-end"><strong class="text-danger"><?= formatPrice($report['cash_out']['total']) ?></strong></td>
                        </tr>
                    </tbody>

                    <!-- Net Cash Flow -->
                    <thead class="<?= $report['net_cash_flow'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                        <tr>
                            <th>
                                <i class="fas fa-balance-scale me-2"></i>
                                NET CASH FLOW
                            </th>
                            <th class="text-end h4 mb-0">
                                <?= formatPrice($report['net_cash_flow']) ?>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Net Cash Flow Card -->
        <div class="card mb-4 <?= $report['net_cash_flow'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50">Net Cash Flow</h6>
                <h2 class="mb-0"><?= formatPrice($report['net_cash_flow']) ?></h2>
                <p class="mb-0">
                    <?= $report['net_cash_flow'] >= 0 ? 'Positive' : 'Negative' ?> Cash Flow
                </p>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Cash Flow Breakdown</h5>
            </div>
            <div class="card-body">
                <canvas id="cashFlowChart" height="200"></canvas>
            </div>
        </div>

        <!-- Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Cash In</span>
                    <span class="text-success"><?= formatPrice($report['cash_in']['total']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Cash Out</span>
                    <span class="text-danger">-<?= formatPrice($report['cash_out']['total']) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Net</strong>
                    <strong class="<?= $report['net_cash_flow'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= formatPrice($report['net_cash_flow']) ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('cashFlowChart'), {
    type: 'bar',
    data: {
        labels: ['Cash In', 'Cash Out', 'Net'],
        datasets: [{
            data: [
                <?= $report['cash_in']['total'] ?>,
                -<?= $report['cash_out']['total'] ?>,
                <?= $report['net_cash_flow'] ?>
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)',
                <?= $report['net_cash_flow'] >= 0 ? "'rgba(40, 167, 69, 0.8)'" : "'rgba(220, 53, 69, 0.8)'" ?>
            ],
            borderWidth: 0
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
</script>
