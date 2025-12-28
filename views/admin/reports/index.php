<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Reports & Analytics</h1>
            <p class="text-muted mb-0">Overview of your store performance</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('admin/reports/export?type=sales&start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-primary">
                <i class="bi bi-download"></i> Export Sales
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
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
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filter
                    </button>
                    <a href="<?= url('admin/reports') ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(7)">7 Days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(30)">30 Days</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange(90)">90 Days</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Report Links -->
    <div class="row mb-4">
        <div class="col-md-4">
            <a href="<?= url('admin/reports/sales?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-currency-dollar text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Sales Report</h6>
                        <p class="text-muted mb-0 small">Detailed sales analytics</p>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= url('admin/reports/products?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-box-seam text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Product Report</h6>
                        <p class="text-muted mb-0 small">Product performance</p>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= url('admin/reports/customers?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="bi bi-people text-info fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Customer Report</h6>
                        <p class="text-muted mb-0 small">Customer insights</p>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Revenue</h6>
                            <h3 class="mb-0"><?= formatPrice($stats['revenue']) ?></h3>
                        </div>
                        <i class="bi bi-currency-dollar fs-1 opacity-25"></i>
                    </div>
                    <?php if ($stats['revenue_change'] != 0): ?>
                    <small class="<?= $stats['revenue_change'] > 0 ? 'text-success-emphasis' : 'text-danger-emphasis' ?>">
                        <i class="bi bi-<?= $stats['revenue_change'] > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                        <?= abs($stats['revenue_change']) ?>% vs previous
                    </small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Orders</h6>
                            <h3 class="mb-0"><?= number_format($stats['orders']) ?></h3>
                        </div>
                        <i class="bi bi-bag fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Avg Order</h6>
                            <h3 class="mb-0"><?= formatPrice($stats['avg_order']) ?></h3>
                        </div>
                        <i class="bi bi-receipt fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">New Customers</h6>
                            <h3 class="mb-0"><?= number_format($stats['new_customers']) ?></h3>
                        </div>
                        <i class="bi bi-person-plus fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Products Sold</h6>
                            <h3 class="mb-0"><?= number_format($stats['products_sold']) ?></h3>
                        </div>
                        <i class="bi bi-box fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Date Range</h6>
                            <h6 class="mb-0"><?= date('M d', strtotime($startDate)) ?> - <?= date('M d', strtotime($endDate)) ?></h6>
                        </div>
                        <i class="bi bi-calendar fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sales Chart -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daily Sales</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Breakdown -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Orders by Status</h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 200px; width: 100%;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <?php foreach ($ordersByStatus as $status): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge <?= statusBadge($status['status']) ?>"><?= ucfirst($status['status']) ?></span>
                            <span class="text-muted"><?= $status['count'] ?> orders (<?= formatPrice($status['revenue']) ?>)</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Products -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Top Selling Products</h5>
                    <a href="<?= url('admin/reports/products?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Sold</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topProducts)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No sales data available</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach (array_slice($topProducts, 0, 5) as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($product['image']): ?>
                                            <img src="<?= upload($product['image']) ?>" class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                            <?php else: ?>
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-medium"><?= sanitize($product['name']) ?></div>
                                                <small class="text-muted"><?= sanitize($product['sku']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= number_format($product['quantity_sold']) ?></td>
                                    <td class="text-end"><?= formatPrice($product['revenue']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="<?= url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No orders yet</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach (array_slice($recentOrders, 0, 5) as $order): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url('admin/orders/view/' . $order['id']) ?>" class="text-decoration-none">
                                            <?= sanitize($order['order_number']) ?>
                                        </a>
                                        <br><small class="text-muted"><?= formatDateTime($order['created_at']) ?></small>
                                    </td>
                                    <td><?= sanitize($order['customer_name'] ?? 'Guest') ?></td>
                                    <td><span class="badge <?= statusBadge($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td class="text-end"><?= formatPrice($order['total_amount']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Date range helper
function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);

    document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
    document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
}

// Sales Chart
const salesData = <?= json_encode($dailySales) ?>;
const salesLabels = salesData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
const salesValues = salesData.map(d => parseFloat(d.revenue));
const ordersValues = salesData.map(d => parseInt(d.orders));

new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: salesLabels,
        datasets: [
            {
                label: 'Revenue',
                data: salesValues,
                backgroundColor: 'rgba(233, 69, 96, 0.8)',
                borderColor: '#e94560',
                borderWidth: 1,
                borderRadius: 4,
                yAxisID: 'y'
            },
            {
                label: 'Orders',
                data: ordersValues,
                type: 'line',
                borderColor: '#1a1a2e',
                backgroundColor: 'rgba(26, 26, 46, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                pointBackgroundColor: '#1a1a2e',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: false,
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
                beginAtZero: true,
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                },
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
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false
                },
                ticks: {
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 15
                }
            },
            tooltip: {
                backgroundColor: 'rgba(26, 26, 46, 0.9)',
                padding: 12,
                cornerRadius: 8,
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

// Status Chart
const statusData = <?= json_encode($ordersByStatus) ?>;
const statusLabels = statusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1));
const statusValues = statusData.map(d => parseInt(d.count));
const statusColors = {
    'Pending': '#ffc107',
    'Processing': '#17a2b8',
    'Shipped': '#007bff',
    'Delivered': '#28a745',
    'Cancelled': '#dc3545',
    'Refunded': '#6c757d'
};

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: statusLabels.map(l => statusColors[l] || '#6c757d'),
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
