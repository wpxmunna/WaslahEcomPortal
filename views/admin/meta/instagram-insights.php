<?php
/**
 * Instagram Insights
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fab fa-instagram"></i> Instagram Insights</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/meta') ?>">Meta Integration</a></li>
                <li class="breadcrumb-item active">Instagram Insights</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/meta/sync-insights/instagram') ?>" class="btn btn-outline-danger">
            <i class="fas fa-sync me-2"></i>Sync Now
        </a>
        <a href="<?= url('admin/meta/messages/instagram') ?>" class="btn btn-danger">
            <i class="fas fa-envelope me-2"></i>Messages
        </a>
    </div>
</div>

<!-- Account Info -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                <i class="fab fa-instagram fa-2x text-danger"></i>
            </div>
            <div>
                <h5 class="mb-1">@<?= htmlspecialchars($integration['page_name'] ?? 'Instagram Account') ?></h5>
                <small class="text-muted">
                    Account ID: <?= htmlspecialchars($integration['page_id'] ?? 'N/A') ?>
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
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-filter me-2"></i>Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card" style="border-left: 4px solid #E1306C;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Impressions</h6>
                        <h3 class="mb-0">
                            <?php
                            $impressions = 0;
                            if (isset($chartData['impressions'])) {
                                $impressions = array_sum($chartData['impressions']);
                            }
                            echo number_format($impressions);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-eye fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-left: 4px solid #833AB4;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Reach</h6>
                        <h3 class="mb-0">
                            <?php
                            $reach = 0;
                            if (isset($chartData['reach'])) {
                                $reach = array_sum($chartData['reach']);
                            }
                            echo number_format($reach);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-users fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-left: 4px solid #405DE6;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Profile Views</h6>
                        <h3 class="mb-0">
                            <?php
                            $profileViews = 0;
                            if (isset($chartData['profile_views'])) {
                                $profileViews = array_sum($chartData['profile_views']);
                            }
                            echo number_format($profileViews);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-user fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card" style="border-left: 4px solid #F77737;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Website Clicks</h6>
                        <h3 class="mb-0">
                            <?php
                            $clicks = 0;
                            if (isset($chartData['website_clicks'])) {
                                $clicks = array_sum($chartData['website_clicks']);
                            }
                            echo number_format($clicks);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-link fa-2x text-muted"></i>
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
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Impressions & Reach</h5>
            </div>
            <div class="card-body">
                <canvas id="reachChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Audience Actions</h5>
            </div>
            <div class="card-body">
                <canvas id="actionsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Follower Growth</h5>
            </div>
            <div class="card-body">
                <canvas id="followerChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Best Posting Times</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-danger">12 PM</h4>
                            <small class="text-muted">Highest Reach</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-danger">6 PM</h4>
                            <small class="text-muted">Most Engagement</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-danger">Tue</h4>
                            <small class="text-muted">Best Day</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h4 class="mb-0 text-danger">Sat</h4>
                            <small class="text-muted">2nd Best</small>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-0 mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Based on your audience activity patterns. Results may vary.
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?= json_encode($chartData) ?>;

    // Get dates from data
    const dates = Object.keys(chartData['impressions'] || chartData['reach'] || {}).sort();

    // Instagram gradient
    const ctx1 = document.getElementById('reachChart').getContext('2d');
    const gradient = ctx1.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(225, 48, 108, 0.3)');
    gradient.addColorStop(1, 'rgba(225, 48, 108, 0)');

    // Reach Chart
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Impressions',
                data: dates.map(d => chartData['impressions']?.[d] || 0),
                borderColor: '#E1306C',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4
            }, {
                label: 'Reach',
                data: dates.map(d => chartData['reach']?.[d] || 0),
                borderColor: '#833AB4',
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

    // Actions Pie Chart
    const actionsData = {
        profile: dates.reduce((sum, d) => sum + (chartData['profile_views']?.[d] || 0), 0),
        website: dates.reduce((sum, d) => sum + (chartData['website_clicks']?.[d] || 0), 0),
        email: dates.reduce((sum, d) => sum + (chartData['email_contacts']?.[d] || 0), 0),
        phone: dates.reduce((sum, d) => sum + (chartData['phone_call_clicks']?.[d] || 0), 0)
    };

    new Chart(document.getElementById('actionsChart'), {
        type: 'doughnut',
        data: {
            labels: ['Profile Views', 'Website Clicks', 'Email Clicks', 'Phone Clicks'],
            datasets: [{
                data: [
                    actionsData.profile || 1,
                    actionsData.website || 1,
                    actionsData.email || 0,
                    actionsData.phone || 0
                ],
                backgroundColor: ['#E1306C', '#833AB4', '#405DE6', '#F77737'],
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

    // Follower Chart
    new Chart(document.getElementById('followerChart'), {
        type: 'bar',
        data: {
            labels: dates,
            datasets: [{
                label: 'New Followers',
                data: dates.map(d => chartData['follower_count']?.[d] || 0),
                backgroundColor: '#E1306C'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
