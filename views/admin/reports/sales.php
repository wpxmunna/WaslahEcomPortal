<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('admin/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active">Sales Report</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Sales Report</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('admin/reports/export?type=sales&start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-primary">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="<?= url('admin/reports/export?type=orders&start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Orders
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Group By</label>
                    <select name="group_by" class="form-select">
                        <option value="day" <?= $groupBy === 'day' ? 'selected' : '' ?>>Day</option>
                        <option value="week" <?= $groupBy === 'week' ? 'selected' : '' ?>>Week</option>
                        <option value="month" <?= $groupBy === 'month' ? 'selected' : '' ?>>Month</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filter
                    </button>
                    <a href="<?= url('admin/reports/sales') ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(7)">7 Days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(30)">30 Days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(90)">90 Days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(365)">1 Year</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Revenue</h6>
                    <h2 class="mb-0"><?= formatPrice($summary['total_revenue'] ?? 0) ?></h2>
                    <small class="text-white-50"><?= number_format($summary['total_orders'] ?? 0) ?> orders</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Average Order Value</h6>
                    <h2 class="mb-0"><?= formatPrice($summary['avg_order_value'] ?? 0) ?></h2>
                    <small class="text-white-50">per order</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Subtotal</h6>
                    <h2 class="mb-0"><?= formatPrice($summary['subtotal'] ?? 0) ?></h2>
                    <small class="text-white-50">before tax/shipping</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Discounts Given</h6>
                    <h2 class="mb-0"><?= formatPrice($summary['total_discounts'] ?? 0) ?></h2>
                    <small class="text-dark-50">total discounts</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Shipping Revenue</h6>
                    <h3 class="text-primary"><?= formatPrice($summary['total_shipping'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Tax Collected</h6>
                    <h3 class="text-success"><?= formatPrice($summary['total_tax'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Order Range</h6>
                    <h3 class="text-info"><?= formatPrice($summary['min_order'] ?? 0) ?> - <?= formatPrice($summary['max_order'] ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Sales Over Time (<?= ucfirst($groupBy) ?>ly)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Payment Methods</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" height="200"></canvas>
                    <div class="mt-3">
                        <?php foreach ($paymentMethods as $pm): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                            <div>
                                <strong><?= ucfirst($pm['method']) ?></strong>
                                <small class="text-muted d-block"><?= $pm['orders'] ?> orders</small>
                            </div>
                            <span class="badge bg-primary"><?= formatPrice($pm['revenue']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($paymentMethods)): ?>
                        <p class="text-muted text-center">No payment data</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hourly Sales -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Sales by Hour of Day</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Data Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sales Breakdown</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Period</th>
                            <th class="text-center">Orders</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Discounts</th>
                            <th class="text-end">Shipping</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($salesData)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No sales data for selected period</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($salesData as $row): ?>
                        <tr>
                            <td><strong><?= sanitize($row['label']) ?></strong></td>
                            <td class="text-center"><?= number_format($row['orders']) ?></td>
                            <td class="text-end"><?= formatPrice($row['subtotal']) ?></td>
                            <td class="text-end text-danger">-<?= formatPrice($row['discounts']) ?></td>
                            <td class="text-end"><?= formatPrice($row['shipping']) ?></td>
                            <td class="text-end"><?= formatPrice($row['tax']) ?></td>
                            <td class="text-end"><strong><?= formatPrice($row['revenue']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($salesData)): ?>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-center"><?= number_format($summary['total_orders'] ?? 0) ?></td>
                            <td class="text-end"><?= formatPrice($summary['subtotal'] ?? 0) ?></td>
                            <td class="text-end text-danger">-<?= formatPrice($summary['total_discounts'] ?? 0) ?></td>
                            <td class="text-end"><?= formatPrice($summary['total_shipping'] ?? 0) ?></td>
                            <td class="text-end"><?= formatPrice($summary['total_tax'] ?? 0) ?></td>
                            <td class="text-end"><?= formatPrice($summary['total_revenue'] ?? 0) ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);

    document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
    document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
}

// Sales Chart
const salesData = <?= json_encode($salesData) ?>;
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: salesData.map(d => d.label),
        datasets: [
            {
                label: 'Revenue',
                data: salesData.map(d => parseFloat(d.revenue)),
                backgroundColor: 'rgba(233, 69, 96, 0.8)',
                borderColor: '#e94560',
                borderWidth: 1,
                yAxisID: 'y'
            },
            {
                label: 'Orders',
                data: salesData.map(d => parseInt(d.orders)),
                type: 'line',
                borderColor: '#1a1a2e',
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.4,
                yAxisID: 'y1'
            }
        ]
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
                type: 'linear',
                display: true,
                position: 'left',
                ticks: {
                    callback: function(value) {
                        return '<?= CURRENCY_SYMBOL ?>' + value.toLocaleString();
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false
                },
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.datasetIndex === 0) {
                            return 'Revenue: <?= CURRENCY_SYMBOL ?>' + context.parsed.y.toLocaleString();
                        }
                        return 'Orders: ' + context.parsed.y;
                    }
                }
            }
        }
    }
});

// Payment Methods Chart
const paymentData = <?= json_encode($paymentMethods) ?>;
new Chart(document.getElementById('paymentChart'), {
    type: 'pie',
    data: {
        labels: paymentData.map(d => d.method.charAt(0).toUpperCase() + d.method.slice(1)),
        datasets: [{
            data: paymentData.map(d => parseFloat(d.revenue)),
            backgroundColor: ['#e94560', '#1a1a2e', '#17a2b8', '#28a745', '#ffc107'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': <?= CURRENCY_SYMBOL ?>' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});

// Hourly Sales Chart
const hourlyData = <?= json_encode($hourlySales) ?>;
const hours = Array.from({length: 24}, (_, i) => i);
const hourlyRevenue = hours.map(h => {
    const found = hourlyData.find(d => parseInt(d.hour) === h);
    return found ? parseFloat(found.revenue) : 0;
});

new Chart(document.getElementById('hourlyChart'), {
    type: 'bar',
    data: {
        labels: hours.map(h => h.toString().padStart(2, '0') + ':00'),
        datasets: [{
            label: 'Revenue',
            data: hourlyRevenue,
            backgroundColor: 'rgba(26, 26, 46, 0.7)',
            borderColor: '#1a1a2e',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '<?= CURRENCY_SYMBOL ?>' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: <?= CURRENCY_SYMBOL ?>' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
