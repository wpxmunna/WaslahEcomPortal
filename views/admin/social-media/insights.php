<?php
/**
 * Campaign Insights Dashboard
 */
$overall = $insights['overall'] ?? [];
$today = $insights['today'] ?? [];
$topCampaigns = $insights['top_campaigns'] ?? [];
$platformPerf = $insights['platform_performance'] ?? [];
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-line"></i> Campaign Insights</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media') ?>">Social Media</a></li>
                <li class="breadcrumb-item active">Insights</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/social-media/campaigns') ?>" class="btn btn-outline-primary">
            <i class="fas fa-bullhorn me-2"></i>Campaigns
        </a>
    </div>
</div>

<!-- Overall Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Campaigns</h6>
                        <h2 class="mb-0"><?= number_format($overall['total_campaigns'] ?? 0) ?></h2>
                    </div>
                    <i class="fas fa-bullhorn fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Views</h6>
                        <h2 class="mb-0"><?= number_format($overall['total_views'] ?? 0) ?></h2>
                        <small class="text-white-50">
                            <?php if (($insights['weekly_growth'] ?? 0) != 0): ?>
                            <i class="fas fa-<?= $insights['weekly_growth'] > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                            <?= abs($insights['weekly_growth']) ?>% this week
                            <?php endif; ?>
                        </small>
                    </div>
                    <i class="fas fa-eye fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Clicks</h6>
                        <h2 class="mb-0"><?= number_format($overall['total_clicks'] ?? 0) ?></h2>
                    </div>
                    <i class="fas fa-mouse-pointer fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1 opacity-75">Avg. Conversion</h6>
                        <h2 class="mb-0"><?= number_format($overall['avg_conversion'] ?? 0, 1) ?>%</h2>
                    </div>
                    <i class="fas fa-percentage fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Activity -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Today's Views</h6>
                <h3 class="text-primary mb-0"><?= number_format($today['views'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Today's Clicks</h6>
                <h3 class="text-info mb-0"><?= number_format($today['clicks'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Today's Copies</h6>
                <h3 class="text-success mb-0"><?= number_format($today['copies'] ?? 0) ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Performance Trends Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>30-Day Performance Trends</h5>
            </div>
            <div class="card-body">
                <canvas id="trendsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Performing Campaigns -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Campaigns</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topCampaigns)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <p class="mb-0">No data yet</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($topCampaigns as $index => $campaign): ?>
                    <a href="<?= url('admin/social-media/campaigns/performance/' . $campaign['id']) ?>"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-<?= $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'light text-dark') ?> me-2">
                                    #<?= $index + 1 ?>
                                </span>
                                <strong><?= htmlspecialchars($campaign['title']) ?></strong>
                                <div class="small text-muted mt-1">
                                    <?php $platform = $platforms[$campaign['platform']] ?? null; ?>
                                    <?php if ($platform): ?>
                                    <i class="fa-brands <?= $platform['icon'] ?>" style="color: <?= $platform['color'] ?>"></i>
                                    <?= $platform['name'] ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold"><?= number_format($campaign['metric_value']) ?></div>
                                <small class="text-muted">views</small>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Platform Performance -->
<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Performance by Platform</h5>
            </div>
            <div class="card-body">
                <?php if (empty($platformPerf)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                    <p class="mb-0">No platform data yet</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th class="text-center">Campaigns</th>
                                <th class="text-center">Views</th>
                                <th class="text-center">Clicks</th>
                                <th class="text-center">Conv. Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($platformPerf as $perf): ?>
                            <tr>
                                <td>
                                    <?php $platform = $platforms[$perf['platform']] ?? null; ?>
                                    <?php if ($platform): ?>
                                    <span class="badge" style="background-color: <?= $platform['color'] ?>">
                                        <i class="fa-brands <?= $platform['icon'] ?> me-1"></i>
                                        <?= $platform['name'] ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary"><?= $perf['platform'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $perf['campaign_count'] ?></td>
                                <td class="text-center"><?= number_format($perf['total_views']) ?></td>
                                <td class="text-center"><?= number_format($perf['total_clicks']) ?></td>
                                <td class="text-center"><?= number_format($perf['avg_conversion'], 1) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Platform Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="platformChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Trends Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
const trendsData = <?= json_encode($trends) ?>;

new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: trendsData.map(d => d.stat_date),
        datasets: [
            {
                label: 'Views',
                data: trendsData.map(d => d.total_views || 0),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Clicks',
                data: trendsData.map(d => d.total_clicks || 0),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Copies',
                data: trendsData.map(d => d.total_copies || 0),
                borderColor: '#36b9cc',
                backgroundColor: 'rgba(54, 185, 204, 0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Platform Chart
const platformCtx = document.getElementById('platformChart').getContext('2d');
const platformData = <?= json_encode($platformPerf) ?>;
const platformColors = {
    'all': '#34495e',
    'facebook': '#1877F2',
    'instagram': '#E4405F',
    'whatsapp': '#25D366',
    'telegram': '#26A5E4',
    'twitter': '#000000'
};

new Chart(platformCtx, {
    type: 'doughnut',
    data: {
        labels: platformData.map(p => p.platform),
        datasets: [{
            data: platformData.map(p => p.total_views || 0),
            backgroundColor: platformData.map(p => platformColors[p.platform] || '#6c757d')
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
</script>
