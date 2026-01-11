<?php
/**
 * Meta Business Suite Integration - Dashboard
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fab fa-meta"></i> Meta Business Suite</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Meta Integration</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/meta/settings') ?>" class="btn btn-outline-secondary me-2">
            <i class="fas fa-cog me-2"></i>Settings
        </a>
        <?php if ($appConfigured): ?>
        <a href="<?= url('admin/meta/connect') ?>" class="btn btn-primary">
            <i class="fab fa-facebook me-2"></i>Connect Account
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (!$appConfigured): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Configuration Required:</strong> Please configure your Meta App ID and Secret in the
    <a href="<?= url('admin/meta/settings') ?>">settings page</a> before connecting.
</div>
<?php endif; ?>

<!-- Connected Platforms -->
<div class="row g-4 mb-4">
    <!-- Facebook -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fab fa-facebook-f fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Facebook Page</h5>
                        <?php
                        $fb = null;
                        foreach ($integrations as $i) {
                            if ($i['platform'] === 'facebook') { $fb = $i; break; }
                        }
                        ?>
                        <?php if ($fb && $fb['is_active']): ?>
                        <span class="badge bg-success">Connected</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Not Connected</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($fb && $fb['is_active']): ?>
                <p class="text-muted mb-2">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    <?= htmlspecialchars($fb['page_name']) ?>
                </p>
                <div class="btn-group w-100">
                    <a href="<?= url('admin/meta/facebook-insights') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i>Insights
                    </a>
                    <a href="<?= url('admin/meta/messages/facebook') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-envelope me-1"></i>Messages
                    </a>
                    <a href="<?= url('admin/meta/disconnect/facebook?csrf_token=' . Session::getCsrfToken()) ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Disconnect Facebook Page?')">
                        <i class="fas fa-unlink"></i>
                    </a>
                </div>
                <?php else: ?>
                <p class="text-muted small">Connect your Facebook Page to monitor insights and messages.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Instagram -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="fab fa-instagram fa-2x text-danger"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Instagram</h5>
                        <?php
                        $ig = null;
                        foreach ($integrations as $i) {
                            if ($i['platform'] === 'instagram') { $ig = $i; break; }
                        }
                        ?>
                        <?php if ($ig && $ig['is_active']): ?>
                        <span class="badge bg-success">Connected</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Not Connected</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($ig && $ig['is_active']): ?>
                <p class="text-muted mb-2">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    @<?= htmlspecialchars($ig['page_name']) ?>
                </p>
                <div class="btn-group w-100">
                    <a href="<?= url('admin/meta/instagram-insights') ?>" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-chart-line me-1"></i>Insights
                    </a>
                    <a href="<?= url('admin/meta/messages/instagram') ?>" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-envelope me-1"></i>Messages
                    </a>
                    <a href="<?= url('admin/meta/disconnect/instagram?csrf_token=' . Session::getCsrfToken()) ?>"
                       class="btn btn-sm btn-outline-secondary"
                       onclick="return confirm('Disconnect Instagram?')">
                        <i class="fas fa-unlink"></i>
                    </a>
                </div>
                <?php else: ?>
                <p class="text-muted small">Instagram is connected via your Facebook Page.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- WhatsApp -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fab fa-whatsapp fa-2x text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">WhatsApp Business</h5>
                        <?php
                        $wa = null;
                        foreach ($integrations as $i) {
                            if ($i['platform'] === 'whatsapp') { $wa = $i; break; }
                        }
                        ?>
                        <?php if ($wa && $wa['is_active']): ?>
                        <span class="badge bg-success">Connected</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Not Connected</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($wa && $wa['is_active']): ?>
                <p class="text-muted mb-2">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    <?= htmlspecialchars($wa['page_name']) ?>
                </p>
                <div class="btn-group w-100">
                    <a href="<?= url('admin/meta/messages/whatsapp') ?>" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-envelope me-1"></i>Messages
                    </a>
                    <a href="<?= url('admin/meta/disconnect/whatsapp?csrf_token=' . Session::getCsrfToken()) ?>"
                       class="btn btn-sm btn-outline-secondary"
                       onclick="return confirm('Disconnect WhatsApp Business?')">
                        <i class="fas fa-unlink"></i>
                    </a>
                </div>
                <?php else: ?>
                <p class="text-muted small">Connect WhatsApp Business API for messaging.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<?php if (!empty($summary)): ?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Page Followers</h6>
                        <h3 class="mb-0"><?= number_format($summary['followers'] ?? 0) ?></h3>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
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
                        <h3 class="mb-0"><?= number_format($summary['reach'] ?? 0) ?></h3>
                    </div>
                    <i class="fas fa-eye fa-2x opacity-50"></i>
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
                        <h3 class="mb-0"><?= number_format($summary['engagement'] ?? 0) ?></h3>
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
                        <h6 class="text-dark-50 mb-1">Messages</h6>
                        <h3 class="mb-0"><?= number_format(count($recentMessages ?? [])) ?></h3>
                    </div>
                    <i class="fas fa-comments fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Messages -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-inbox me-2"></i>Recent Messages</h5>
        <a href="<?= url('admin/meta/messages') ?>" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentMessages)): ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">No messages yet. Connect your accounts to start receiving messages.</p>
        </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach (array_slice($recentMessages, 0, 10) as $msg): ?>
            <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <?php if ($msg['platform'] === 'facebook'): ?>
                        <i class="fab fa-facebook-messenger text-primary me-3 fa-lg"></i>
                        <?php elseif ($msg['platform'] === 'instagram'): ?>
                        <i class="fab fa-instagram text-danger me-3 fa-lg"></i>
                        <?php else: ?>
                        <i class="fab fa-whatsapp text-success me-3 fa-lg"></i>
                        <?php endif; ?>
                        <div>
                            <h6 class="mb-1"><?= htmlspecialchars($msg['sender_name'] ?? 'Unknown') ?></h6>
                            <p class="mb-0 text-muted small text-truncate" style="max-width: 400px;">
                                <?= htmlspecialchars($msg['content']) ?>
                            </p>
                        </div>
                    </div>
                    <small class="text-muted">
                        <?= date('M j, g:i A', strtotime($msg['created_at'])) ?>
                        <?php if (!$msg['is_read']): ?>
                        <span class="badge bg-primary ms-1">New</span>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
