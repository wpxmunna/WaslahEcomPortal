<?php
/**
 * Meta Integration Settings
 */
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-cog"></i> Meta Integration Settings</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= url('admin/meta') ?>">Meta Integration</a></li>
                <li class="breadcrumb-item active">Settings</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- App Configuration -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fab fa-facebook me-2"></i>Meta App Configuration</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Important:</strong> These settings must be configured in your <code>config/config.php</code> file for security.
                </div>

                <div class="mb-3">
                    <label class="form-label">App ID</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($appId) ?>" readonly>
                    <small class="text-muted">Set via <code>META_APP_ID</code> constant in config</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">App Secret</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($appSecret) ?>" readonly>
                    <small class="text-muted">Set via <code>META_APP_SECRET</code> constant in config</small>
                </div>

                <hr>

                <h6 class="mb-3">How to Configure:</h6>
                <ol class="text-muted">
                    <li>Go to <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers</a></li>
                    <li>Create a new app or select an existing one</li>
                    <li>Copy the App ID and App Secret</li>
                    <li>Add the following to your <code>config/config.php</code>:
                        <pre class="bg-dark text-light p-3 rounded mt-2"><code>define('META_APP_ID', 'your_app_id');
define('META_APP_SECRET', 'your_app_secret');
define('META_WEBHOOK_VERIFY_TOKEN', 'your_verify_token');</code></pre>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Webhook Configuration -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-link me-2"></i>Webhook Configuration</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Configure these webhook settings in your Meta App Dashboard:</p>

                <div class="mb-3">
                    <label class="form-label">Callback URL</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="webhookUrl" value="<?= htmlspecialchars($webhookUrl) ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('webhookUrl')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Verify Token</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="verifyToken" value="<?= htmlspecialchars($verifyToken) ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('verifyToken')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <hr>

                <h6 class="mb-3">Required Webhook Subscriptions:</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="fab fa-facebook text-primary me-2"></i>Messenger</h6>
                                <ul class="small mb-0 ps-3">
                                    <li>messages</li>
                                    <li>messaging_postbacks</li>
                                    <li>message_deliveries</li>
                                    <li>message_reads</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="fab fa-instagram text-danger me-2"></i>Instagram</h6>
                                <ul class="small mb-0 ps-3">
                                    <li>messages</li>
                                    <li>messaging_postbacks</li>
                                    <li>comments</li>
                                    <li>mentions</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="fab fa-whatsapp text-success me-2"></i>WhatsApp</h6>
                                <ul class="small mb-0 ps-3">
                                    <li>messages</li>
                                    <li>message_template_status_update</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OAuth Redirect -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>OAuth Redirect URI</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Add this URL to your Meta App's Valid OAuth Redirect URIs:</p>
                <div class="input-group">
                    <input type="text" class="form-control" id="redirectUri"
                           value="<?= SITE_URL ?>/admin/meta/callback" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('redirectUri')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <small class="text-muted">Found in: App Settings > Basic > Valid OAuth Redirect URIs</small>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Configuration Status</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        App ID
                        <?php if (!empty($appId)): ?>
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="fas fa-times"></i></span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        App Secret
                        <?php if ($appSecret === '********'): ?>
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="fas fa-times"></i></span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Verify Token
                        <?php if (!empty($verifyToken)): ?>
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                        <?php else: ?>
                        <span class="badge bg-warning"><i class="fas fa-exclamation"></i></span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Required Permissions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-key me-2"></i>Required Permissions</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Your Meta App needs these permissions:</p>
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fab fa-facebook me-2"></i>Facebook</h6>
                    <ul class="small mb-0">
                        <li>pages_show_list</li>
                        <li>pages_read_engagement</li>
                        <li>pages_messaging</li>
                        <li>pages_manage_metadata</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <h6 class="text-danger"><i class="fab fa-instagram me-2"></i>Instagram</h6>
                    <ul class="small mb-0">
                        <li>instagram_basic</li>
                        <li>instagram_manage_messages</li>
                        <li>instagram_manage_insights</li>
                    </ul>
                </div>
                <div>
                    <h6 class="text-success"><i class="fab fa-whatsapp me-2"></i>WhatsApp</h6>
                    <ul class="small mb-0">
                        <li>whatsapp_business_management</li>
                        <li>whatsapp_business_messaging</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Help Links -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Documentation</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="https://developers.facebook.com/docs/messenger-platform" target="_blank" class="list-group-item list-group-item-action">
                    <i class="fab fa-facebook-messenger me-2"></i>Messenger Platform
                    <i class="fas fa-external-link-alt float-end"></i>
                </a>
                <a href="https://developers.facebook.com/docs/instagram-api" target="_blank" class="list-group-item list-group-item-action">
                    <i class="fab fa-instagram me-2"></i>Instagram API
                    <i class="fas fa-external-link-alt float-end"></i>
                </a>
                <a href="https://developers.facebook.com/docs/whatsapp/cloud-api" target="_blank" class="list-group-item list-group-item-action">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp Cloud API
                    <i class="fas fa-external-link-alt float-end"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');

    // Show feedback
    const btn = element.nextElementSibling;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success');

    setTimeout(() => {
        btn.innerHTML = originalHtml;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>
