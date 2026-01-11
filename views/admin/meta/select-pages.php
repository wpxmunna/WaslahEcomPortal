<?php
/**
 * Meta Business Suite - Select Pages to Connect
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fab fa-meta"></i> Select Pages to Connect</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/meta') ?>">Meta Integration</a></li>
                <li class="breadcrumb-item active">Select Pages</li>
            </ol>
        </nav>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Connected Successfully!</strong> Now select which pages and accounts you want to connect to your store.
</div>

<form method="POST" action="<?= url('admin/meta/save-pages') ?>">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <div class="row">
        <!-- Facebook Pages -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fab fa-facebook me-2"></i>Facebook Pages</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($pages)): ?>
                    <div class="text-center py-4">
                        <i class="fab fa-facebook-f fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No Facebook Pages found. Make sure you have admin access to at least one page.</p>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-3">Select the pages you want to connect:</p>
                    <?php foreach ($pages as $page): ?>
                    <div class="form-check mb-3 p-3 border rounded">
                        <input class="form-check-input" type="checkbox" name="pages[]"
                               value="<?= htmlspecialchars($page['id']) ?>"
                               id="page_<?= htmlspecialchars($page['id']) ?>">
                        <label class="form-check-label d-flex align-items-center" for="page_<?= htmlspecialchars($page['id']) ?>">
                            <?php if (!empty($page['picture']['data']['url'])): ?>
                            <img src="<?= htmlspecialchars($page['picture']['data']['url']) ?>"
                                 class="rounded-circle me-3" width="40" height="40" alt="">
                            <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="fab fa-facebook-f"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <strong><?= htmlspecialchars($page['name']) ?></strong>
                                <?php if (!empty($page['category'])): ?>
                                <small class="text-muted d-block"><?= htmlspecialchars($page['category']) ?></small>
                                <?php endif; ?>
                                <?php if (!empty($page['fan_count'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i><?= number_format($page['fan_count']) ?> followers
                                </small>
                                <?php endif; ?>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- WhatsApp Business -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fab fa-whatsapp me-2"></i>WhatsApp Business</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($waAccounts)): ?>
                    <div class="text-center py-4">
                        <i class="fab fa-whatsapp fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No WhatsApp Business accounts found. Make sure you have WhatsApp Business API access.</p>
                        <a href="https://business.facebook.com/settings/whatsapp-business-accounts" target="_blank" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-external-link-alt me-1"></i>Set up WhatsApp Business
                        </a>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-3">Select a WhatsApp number to connect:</p>
                    <?php foreach ($waAccounts as $account): ?>
                    <div class="mb-3">
                        <h6 class="text-muted"><?= htmlspecialchars($account['name'] ?? 'WhatsApp Business Account') ?></h6>
                        <?php if (!empty($account['phone_numbers'])): ?>
                        <?php foreach ($account['phone_numbers'] as $phone): ?>
                        <div class="form-check mb-2 p-3 border rounded">
                            <input class="form-check-input" type="radio" name="whatsapp"
                                   value="<?= htmlspecialchars($phone['id']) ?>"
                                   id="wa_<?= htmlspecialchars($phone['id']) ?>">
                            <label class="form-check-label" for="wa_<?= htmlspecialchars($phone['id']) ?>">
                                <i class="fab fa-whatsapp text-success me-2"></i>
                                <strong><?= htmlspecialchars($phone['display_phone_number'] ?? $phone['id']) ?></strong>
                                <?php if (!empty($phone['verified_name'])): ?>
                                <small class="text-muted d-block">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    <?= htmlspecialchars($phone['verified_name']) ?>
                                </small>
                                <?php endif; ?>
                                <?php if (!empty($phone['quality_rating'])): ?>
                                <small class="text-muted">
                                    Quality: <?= htmlspecialchars($phone['quality_rating']) ?>
                                </small>
                                <?php endif; ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-muted small">No phone numbers found for this account.</p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="card mb-4">
        <div class="card-body">
            <h6><i class="fab fa-instagram text-danger me-2"></i>Instagram Note</h6>
            <p class="text-muted mb-0">
                Instagram accounts linked to your selected Facebook Pages will be automatically connected.
                Make sure your Instagram Business/Creator account is properly linked to your Facebook Page.
            </p>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="<?= url('admin/meta') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-times me-2"></i>Cancel
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check me-2"></i>Connect Selected Accounts
        </button>
    </div>
</form>
