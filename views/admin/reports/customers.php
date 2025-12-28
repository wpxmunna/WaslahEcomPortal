<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('admin/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active">Customer Report</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Customer Report</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('admin/reports/export?type=customers&start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-primary">
                <i class="bi bi-download"></i> Export CSV
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
                    <a href="<?= url('admin/reports/customers') ?>" class="btn btn-outline-secondary">Reset</a>
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

    <!-- Customer Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Customers</h6>
                            <h2 class="mb-0"><?= number_format($customerStats['total_customers'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-people fs-1 opacity-25"></i>
                    </div>
                    <small class="text-white-50">All time</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">New Customers</h6>
                            <h2 class="mb-0"><?= number_format($customerStats['new_customers'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-person-plus fs-1 opacity-25"></i>
                    </div>
                    <small class="text-white-50">In selected period</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Active Customers</h6>
                            <h2 class="mb-0"><?= number_format($customerStats['active_customers'] ?? 0) ?></h2>
                        </div>
                        <i class="bi bi-person-check fs-1 opacity-25"></i>
                    </div>
                    <small class="text-white-50">Made orders in period</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-dark-50">Avg Orders/Customer</h6>
                            <h2 class="mb-0"><?= $customerStats['avg_orders_per_customer'] ?? 0 ?></h2>
                        </div>
                        <i class="bi bi-bag fs-1 opacity-25"></i>
                    </div>
                    <small class="text-dark-50">All time average</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- New Customers Over Time -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">New Customer Registrations</h5>
                </div>
                <div class="card-body">
                    <canvas id="newCustomersChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Customer Locations -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Customer Locations</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Location</th>
                                    <th class="text-end">Customers</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($customerLocations)): ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No location data</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($customerLocations as $location): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-geo-alt text-muted me-1"></i>
                                        <?= sanitize($location['city']) ?>, <?= sanitize($location['country']) ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?= number_format($location['customers']) ?></span>
                                    </td>
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

    <!-- Top Customers -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Top Customers</h5>
            <span class="text-muted"><?= date('M d, Y', strtotime($startDate)) ?> - <?= date('M d, Y', strtotime($endDate)) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th class="text-center">Orders</th>
                            <th class="text-end">Total Spent</th>
                            <th>Last Order</th>
                            <th>Member Since</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topCustomers)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No customer data for selected period</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($topCustomers as $index => $customer): ?>
                        <tr>
                            <td>
                                <?php if ($index < 3): ?>
                                <span class="badge <?= $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-danger bg-opacity-75') ?>">
                                    <?= $index + 1 ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted"><?= $index + 1 ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;">
                                        <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?= sanitize($customer['name']) ?></div>
                                        <a href="<?= url('admin/customers/view/' . $customer['id']) ?>" class="small text-primary">View Profile</a>
                                    </div>
                                </div>
                            </td>
                            <td><?= sanitize($customer['email']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= number_format($customer['order_count']) ?></span>
                            </td>
                            <td class="text-end">
                                <strong class="text-success"><?= formatPrice($customer['total_spent']) ?></strong>
                            </td>
                            <td>
                                <?php if ($customer['last_order']): ?>
                                <?= formatDate($customer['last_order']) ?>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDate($customer['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Insights -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up-arrow text-success fs-1"></i>
                    <h3 class="mt-3"><?php
                        $conversionRate = $customerStats['total_customers'] > 0
                            ? round(($customerStats['active_customers'] / $customerStats['total_customers']) * 100, 1)
                            : 0;
                        echo $conversionRate . '%';
                    ?></h3>
                    <p class="text-muted mb-0">Customer Activity Rate</p>
                    <small class="text-muted">Customers who ordered in this period</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar text-primary fs-1"></i>
                    <h3 class="mt-3"><?php
                        $avgValue = $customerStats['active_customers'] > 0 && !empty($topCustomers)
                            ? array_sum(array_column($topCustomers, 'total_spent')) / count($topCustomers)
                            : 0;
                        echo formatPrice($avgValue);
                    ?></h3>
                    <p class="text-muted mb-0">Avg Customer Value</p>
                    <small class="text-muted">Average spend per active customer</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-plus text-info fs-1"></i>
                    <h3 class="mt-3"><?php
                        $daysDiff = max(1, (strtotime($endDate) - strtotime($startDate)) / 86400);
                        $dailyAvg = round(($customerStats['new_customers'] ?? 0) / $daysDiff, 1);
                        echo $dailyAvg;
                    ?></h3>
                    <p class="text-muted mb-0">Avg New Customers/Day</p>
                    <small class="text-muted">Daily customer acquisition</small>
                </div>
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

// New Customers Chart
const newCustomersData = <?= json_encode($newCustomers) ?>;
const labels = newCustomersData.map(d => {
    const date = new Date(d.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
const values = newCustomersData.map(d => parseInt(d.new_customers));

new Chart(document.getElementById('newCustomersChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'New Customers',
            data: values,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#28a745',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
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
                        return context.parsed.y + ' new customer' + (context.parsed.y !== 1 ? 's' : '');
                    }
                }
            }
        }
    }
});
</script>
