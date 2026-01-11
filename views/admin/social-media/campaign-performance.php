<?php
/**
 * Campaign Performance View
 */
$summary = $performance['summary'] ?? [];
$dailyStats = $performance['daily_stats'] ?? [];
$hourlyData = $performance['hourly_distribution'] ?? [];
$platformStats = $performance['platform_stats'] ?? [];
$recentActivity = $performance['recent_activity'] ?? [];
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-bar"></i> Campaign Performance</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media') ?>">Social Media</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/social-media/campaigns') ?>">Campaigns</a></li>
                <li class="breadcrumb-item active">Performance</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/social-media/campaigns/export/' . $campaign['id']) ?>" class="btn btn-outline-success me-2">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
        <a href="<?= url('admin/social-media/campaigns/edit/' . $campaign['id']) ?>" class="btn btn-outline-primary">
            <i class="fas fa-edit me-2"></i>Edit Campaign
        </a>
    </div>
</div>

<!-- Campaign Info -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="mb-1"><?= htmlspecialchars($campaign['title']) ?></h4>
                <div class="d-flex gap-2 flex-wrap">
                    <?php $platform = $platforms[$campaign['platform']] ?? null; ?>
                    <?php if ($platform): ?>
                    <span class="badge" style="background-color: <?= $platform['color'] ?>">
                        <i class="fa-brands <?= $platform['icon'] ?> me-1"></i><?= $platform['name'] ?>
                    </span>
                    <?php endif; ?>
                    <?php $type = $messageTypes[$campaign['message_type']] ?? null; ?>
                    <?php if ($type): ?>
                    <span class="badge" style="background-color: <?= $type['color'] ?>">
                        <i class="fas <?= $type['icon'] ?> me-1"></i><?= $type['name'] ?>
                    </span>
                    <?php endif; ?>
                    <span class="badge bg-<?= $campaign['is_active'] ? 'success' : 'secondary' ?>">
                        <?= $campaign['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <small class="text-muted d-block">Created: <?= date('M d, Y', strtotime($campaign['created_at'])) ?></small>
                <small class="text-muted d-block">Days Active: <?= $summary['days_active'] ?? 0 ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Performance Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Views</h6>
                        <h2 class="mb-0"><?= number_format($summary['total_views'] ?? 0) ?></h2>
                        <small class="text-white-50">
                            <?php if (($summary['views_trend'] ?? 0) != 0): ?>
                            <i class="fas fa-<?= $summary['views_trend'] > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                            <?= abs($summary['views_trend']) ?>% vs last week
                            <?php endif; ?>
                        </small>
                    </div>
                    <i class="fas fa-eye fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Clicks</h6>
                        <h2 class="mb-0"><?= number_format($summary['total_clicks'] ?? 0) ?></h2>
                    </div>
                    <i class="fas fa-mouse-pointer fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Copies</h6>
                        <h2 class="mb-0"><?= number_format($summary['total_copies'] ?? 0) ?></h2>
                    </div>
                    <i class="fas fa-copy fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1 opacity-75">Conversion Rate</h6>
                        <h2 class="mb-0"><?= number_format($summary['conversion_rate'] ?? 0, 2) ?>%</h2>
                    </div>
                    <i class="fas fa-percentage fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Performance Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Daily Performance (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="300"></canvas>
            </div>
        </div>

        <!-- Hourly Distribution -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Hourly Activity Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="hourlyChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Goals -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Goals</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addGoalModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($goals)): ?>
                <p class="text-muted text-center mb-0">No goals set</p>
                <?php else: ?>
                <?php foreach ($goals as $goal): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold"><?= ucfirst($goal['goal_type']) ?></small>
                        <small><?= $goal['current_value'] ?> / <?= $goal['target_value'] ?></small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-<?= $goal['progress'] >= 100 ? 'success' : 'primary' ?>"
                             style="width: <?= min(100, $goal['progress']) ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentActivity)): ?>
                <p class="text-muted text-center py-3 mb-0">No activity yet</p>
                <?php else: ?>
                <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                    <?php foreach ($recentActivity as $activity): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <i class="fas fa-<?= match($activity['event_type']) {
                                'view' => 'eye text-primary',
                                'click' => 'mouse-pointer text-success',
                                'copy' => 'copy text-info',
                                'share' => 'share text-warning',
                                default => 'circle text-muted'
                            } ?> me-2"></i>
                            <span class="text-capitalize"><?= $activity['event_type'] ?></span>
                            <?php if ($activity['platform']): ?>
                            <small class="text-muted ms-1">(<?= $activity['platform'] ?>)</small>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= date('M d H:i', strtotime($activity['created_at'])) ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <?php if (empty($notes)): ?>
                <p class="text-muted text-center py-3 mb-0">No notes yet</p>
                <?php else: ?>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($notes as $note): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-<?= match($note['note_type']) {
                                    'performance' => 'success',
                                    'issue' => 'danger',
                                    'idea' => 'info',
                                    default => 'secondary'
                                } ?> mb-1"><?= ucfirst($note['note_type']) ?></span>
                                <p class="mb-1 small"><?= nl2br(htmlspecialchars($note['note'])) ?></p>
                                <small class="text-muted">
                                    <?= $note['user_name'] ?? 'Unknown' ?> - <?= date('M d', strtotime($note['created_at'])) ?>
                                </small>
                            </div>
                            <a href="<?= url("admin/social-media/campaigns/note/delete/{$campaign['id']}/{$note['id']}?csrf_token=" . Session::getCsrfToken()) ?>"
                               class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this note?')">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Goal Modal -->
<div class="modal fade" id="addGoalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/social-media/campaigns/goal/' . $campaign['id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-bullseye me-2"></i>Add Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Goal Type</label>
                        <select name="goal_type" class="form-select" required>
                            <option value="views">Views</option>
                            <option value="clicks">Clicks</option>
                            <option value="copies">Copies</option>
                            <option value="shares">Shares</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Value</label>
                        <input type="number" name="target_value" class="form-control" min="1" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Goal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('admin/social-media/campaigns/note/' . $campaign['id']) ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-sticky-note me-2"></i>Add Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Note Type</label>
                        <select name="note_type" class="form-select">
                            <option value="general">General</option>
                            <option value="performance">Performance</option>
                            <option value="issue">Issue</option>
                            <option value="idea">Idea</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Performance Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyData = <?= json_encode($dailyStats) ?>;

new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: dailyData.map(d => d.stat_date),
        datasets: [
            {
                label: 'Views',
                data: dailyData.map(d => d.views || 0),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Clicks',
                data: dailyData.map(d => d.clicks || 0),
                borderColor: '#1cc88a',
                fill: false,
                tension: 0.4
            },
            {
                label: 'Copies',
                data: dailyData.map(d => d.copies || 0),
                borderColor: '#36b9cc',
                fill: false,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } }
    }
});

// Hourly Distribution Chart
const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
const hourlyData = <?= json_encode($hourlyData) ?>;

new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: Array.from({length: 24}, (_, i) => i + ':00'),
        datasets: [{
            label: 'Activity',
            data: hourlyData,
            backgroundColor: '#4e73df'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
