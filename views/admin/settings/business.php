<?php
/**
 * Business Information Settings
 */

$groupLabels = [
    'general' => 'General Information',
    'contact' => 'Contact Information',
    'social' => 'Social Media Links',
    'shipping' => 'Shipping & Logistics',
    'domain' => 'Domain & Hosting'
];

$fieldLabels = [
    'facebook_page_url' => 'Facebook Page URL',
    'instagram_url' => 'Instagram URL',
    'youtube_url' => 'YouTube Channel',
    'linkedin_url' => 'LinkedIn',
    'twitter_url' => 'Twitter/X',
    'tiktok_url' => 'TikTok',
    'website_url' => 'Website URL',
    'wordpress_admin_url' => 'WordPress Admin URL',
    'whatsapp_number' => 'WhatsApp Number',
    'whatsapp_link' => 'WhatsApp Link',
    'business_email' => 'Business Email',
    'support_email' => 'Support Email',
    'business_phone' => 'Business Phone',
    'business_address' => 'Business Address',
    'pathao_login_url' => 'Pathao Login URL',
    'steadfast_url' => 'SteadFast URL',
    'namecheap_url' => 'NameCheap URL'
];
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-building me-2"></i>Business Information</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/settings') ?>">Settings</a></li>
                <li class="breadcrumb-item active">Business Info</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= url('admin/settings') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Settings
        </a>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Manage your business information:</strong> Update your social media links, contact details, and important business URLs. These settings are used throughout your portal.
</div>

<form method="POST" action="<?= url('admin/settings/update-business') ?>">
    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">

    <?php foreach ($groupLabels as $group => $groupLabel): ?>
        <?php if (!empty($businessSettings[$group])): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <?php if ($group === 'social'): ?>
                    <i class="fas fa-share-alt me-2"></i>
                    <?php elseif ($group === 'contact'): ?>
                    <i class="fas fa-address-card me-2"></i>
                    <?php elseif ($group === 'shipping'): ?>
                    <i class="fas fa-shipping-fast me-2"></i>
                    <?php elseif ($group === 'domain'): ?>
                    <i class="fas fa-globe me-2"></i>
                    <?php else: ?>
                    <i class="fas fa-info-circle me-2"></i>
                    <?php endif; ?>
                    <?= $groupLabel ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($businessSettings[$group] as $setting): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <?= $fieldLabels[$setting['setting_key']] ?? ucwords(str_replace('_', ' ', $setting['setting_key'])) ?>
                        </label>
                        <?php if ($setting['setting_key'] === 'business_address'): ?>
                        <textarea
                            name="<?= htmlspecialchars($setting['setting_key']) ?>"
                            class="form-control"
                            rows="3"
                            placeholder="Enter <?= $fieldLabels[$setting['setting_key']] ?? $setting['description'] ?>"
                        ><?= htmlspecialchars($setting['setting_value'] ?? '') ?></textarea>
                        <?php else: ?>
                        <input
                            type="<?= strpos($setting['setting_key'], 'email') !== false ? 'email' : (strpos($setting['setting_key'], 'url') !== false ? 'url' : 'text') ?>"
                            name="<?= htmlspecialchars($setting['setting_key']) ?>"
                            class="form-control"
                            value="<?= htmlspecialchars($setting['setting_value'] ?? '') ?>"
                            placeholder="Enter <?= $fieldLabels[$setting['setting_key']] ?? $setting['description'] ?>"
                        >
                        <?php endif; ?>
                        <?php if ($setting['description']): ?>
                        <small class="text-muted"><?= htmlspecialchars($setting['description']) ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Save Changes</h6>
                    <small class="text-muted">Update your business information</small>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Save Business Information
                </button>
            </div>
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Quick Access Links</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <?php
            $quickLinks = [];
            foreach ($businessSettings as $group => $settings) {
                foreach ($settings as $setting) {
                    if (strpos($setting['setting_key'], '_url') !== false || strpos($setting['setting_key'], '_link') !== false) {
                        if (!empty($setting['setting_value'])) {
                            $quickLinks[] = [
                                'label' => $fieldLabels[$setting['setting_key']] ?? ucwords(str_replace('_', ' ', $setting['setting_key'])),
                                'url' => $setting['setting_value']
                            ];
                        }
                    }
                }
            }
            ?>
            <?php if (!empty($quickLinks)): ?>
                <?php foreach ($quickLinks as $link): ?>
                <div class="col-md-3 mb-2">
                    <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-external-link-alt me-1"></i><?= $link['label'] ?>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-muted mb-0">No links configured yet. Add your business URLs above to see quick access links here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
