<?php
/**
 * Facebook Page Insights
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fab fa-facebook"></i> Facebook Page Insights</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/meta') ?>">Meta Integration</a></li>
                <li class="breadcrumb-item active">Facebook Insights</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/meta/sync-insights/facebook') ?>" class="btn btn-outline-primary">
            <i class="fas fa-sync me-2"></i>Sync Now
        </a>
        <a href="<?= url('admin/meta/messages/facebook') ?>" class="btn btn-primary">
            <i class="fas fa-envelope me-2"></i>Messages
        </a>
    </div>
</div>

<!-- Page Info -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                <i class="fab fa-facebook-f fa-2x text-primary"></i>
            </div>
            <div>
                <h5 class="mb-1"><?= htmlspecialchars($integration['page_name'] ?? 'Facebook Page') ?></h5>
                <small class="text-muted">
                    Page ID: <?= htmlspecialchars($integration['page_id'] ?? 'N/A') ?>
                    <?php if ($integration['last_sync_at']): ?>
                    | Last synced: <?= date('M j, Y g:i A', strtotime($integration['last_sync_at'])) ?>
                    <?php endif; ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start" class="form-control" value="<?= $startDate ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end" class="form-control" value="<?= $endDate ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Page Views</h6>
                        <h3 class="mb-0">
                            <?php
                            $pageViews = 0;
                            if (isset($chartData['page_views_total'])) {
                                $pageViews = array_sum($chartData['page_views_total']);
                            }
                            echo number_format($pageViews);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-eye fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Page Reach</h6>
                        <h3 class="mb-0">
                            <?php
                            $reach = 0;
                            if (isset($chartData['page_impressions'])) {
                                $reach = array_sum($chartData['page_impressions']);
                            }
                            echo number_format($reach);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Engagements</h6>
                        <h3 class="mb-0">
                            <?php
                            $engagement = 0;
                            if (isset($chartData['page_post_engagements'])) {
                                $engagement = array_sum($chartData['page_post_engagements']);
                            }
                            echo number_format($engagement);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-heart fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark-50 mb-1">New Followers</h6>
                        <h3 class="mb-0">
                            <?php
                            $followers = 0;
                            if (isset($chartData['page_fan_adds'])) {
                                $followers = array_sum($chartData['page_fan_adds']);
                            }
                            echo number_format($followers);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-user-plus fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Reach & Impressions</h5>
            </div>
            <div class="card-body">
                <canvas id="reachChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Engagement Breakdown</h5>
            </div>
            <div class="card-body">
                <canvas id="engagementChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Daily Performance</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?= json_encode($chartData) ?>;

    // Get dates from data
    const dates = Object.keys(chartData['page_impressions'] || {}).sort();

    // Reach Chart
    new Chart(document.getElementById('reachChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Impressions',
                data: dates.map(d => chartData['page_impressions']?.[d] || 0),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Page Views',
                data: dates.map(d => chartData['page_views_total']?.[d] || 0),
                borderColor: '#198754',
                backgroundColor: 'transparent',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Engagement Pie Chart
    const engagementData = {
        likes: dates.reduce((sum, d) => sum + (chartData['page_actions_post_reactions_like_total']?.[d] || 0), 0),
        comments: dates.reduce((sum, d) => sum + (chartData['page_actions_post_comments']?.[d] || 0), 0),
        shares: dates.reduce((sum, d) => sum + (chartData['page_actions_post_shares']?.[d] || 0), 0)
    };

    new Chart(document.getElementById('engagementChart'), {
        type: 'doughnut',
        data: {
            labels: ['Likes', 'Comments', 'Shares'],
            datasets: [{
                data: [engagementData.likes || 1, engagementData.comments || 1, engagementData.shares || 1],
                backgroundColor: ['#0d6efd', '#198754', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Daily Chart
    new Chart(document.getElementById('dailyChart'), {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'Engagements',
                data: dates.map(d => chartData['page_post_engagements']?.[d] || 0),
                backgroundColor: '#0d6efd'
            }, {
                label: 'New Followers',
                data: dates.map(d => chartData['page_fan_adds']?.[d] || 0),
                backgroundColor: '#198754'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
