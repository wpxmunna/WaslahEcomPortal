<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('admin/reports') ?>">Reports</a></li>
                    <li class="breadcrumb-item active">Product Report</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Product Report</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url('admin/reports/export?type=products&start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-outline-primary">
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
                    <a href="<?= url('admin/reports/products') ?>" class="btn btn-outline-secondary">Reset</a>
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

    <div class="row">
        <!-- Category Performance -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Category Performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="250"></canvas>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categoryPerformance as $cat): ?>
                                <tr>
                                    <td><?= sanitize($cat['name']) ?></td>
                                    <td class="text-center"><?= number_format($cat['quantity_sold'] ?? 0) ?></td>
                                    <td class="text-end"><?= formatPrice($cat['revenue'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($categoryPerformance)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No category data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Low Stock Alert
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Stock</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($lowStock)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-success py-4">
                                        <i class="bi bi-check-circle"></i> All products have sufficient stock
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($lowStock as $product): ?>
                                <tr class="<?= $product['stock_quantity'] <= 0 ? 'table-danger' : ($product['stock_quantity'] <= 5 ? 'table-warning' : '') ?>">
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
                                    <td class="text-center">
                                        <span class="badge <?= $product['stock_quantity'] <= 0 ? 'bg-danger' : 'bg-warning' ?>">
                                            <?= $product['stock_quantity'] ?>
                                        </span>
                                    </td>
                                    <td><?= sanitize($product['category_name'] ?? 'N/A') ?></td>
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

    <!-- Top Selling Products -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Top Selling Products</h5>
            <span class="text-muted"><?= date('M d, Y', strtotime($startDate)) ?> - <?= date('M d, Y', strtotime($endDate)) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Quantity Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Avg. Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topSelling)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No sales data for selected period</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($topSelling as $index => $product): ?>
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
                                    <?php if ($product['image']): ?>
                                    <img src="<?= upload($product['image']) ?>" class="rounded me-2" width="50" height="50" style="object-fit: cover;">
                                    <?php else: ?>
                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-medium"><?= sanitize($product['name']) ?></div>
                                        <a href="<?= url('admin/products/edit/' . $product['id']) ?>" class="small text-primary">Edit</a>
                                    </div>
                                </div>
                            </td>
                            <td><code><?= sanitize($product['sku']) ?></code></td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= number_format($product['quantity_sold']) ?></span>
                            </td>
                            <td class="text-end"><strong><?= formatPrice($product['revenue']) ?></strong></td>
                            <td class="text-end">
                                <?php $avgPrice = $product['quantity_sold'] > 0 ? $product['revenue'] / $product['quantity_sold'] : 0; ?>
                                <?= formatPrice($avgPrice) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Popular Products (by order count) -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Most Popular Products (All Time)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mostViewed)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No product data available</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($mostViewed as $product): ?>
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
                                    <?= sanitize($product['name']) ?>
                                </div>
                            </td>
                            <td><code><?= sanitize($product['sku']) ?></code></td>
                            <td class="text-end"><?= formatPrice($product['price']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= number_format($product['order_count']) ?></span>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function setDateRange(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);

    document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
    document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
}

// Category Performance Chart
const categoryData = <?= json_encode($categoryPerformance) ?>;
const colors = ['#e94560', '#1a1a2e', '#17a2b8', '#28a745', '#ffc107', '#6f42c1', '#fd7e14', '#20c997'];

new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: categoryData.map(d => d.name),
        datasets: [{
            data: categoryData.map(d => parseFloat(d.revenue || 0)),
            backgroundColor: colors,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
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
</script>
