<div class="container-fluid py-4">
    <!-- Debug Info -->
    <?php if (isset($debug_store_id)): ?>
    <div class="alert alert-warning small mb-3">
        <strong>Debug:</strong> Store ID: <?= $debug_store_id ?> |
        Pathao Enabled (from settings array): <strong><?= $settings['pathao_enabled'] ?? 'not set' ?></strong> |
        <br>
        <strong>Direct DB Check:</strong>
        <?php if (isset($debug_db_check) && $debug_db_check): ?>
            Found! ID: <?= $debug_db_check['id'] ?>, Value: <strong><?= $debug_db_check['setting_value'] ?></strong>
        <?php else: ?>
            <span class="text-danger">NOT FOUND in database!</span>
        <?php endif; ?>
        <button type="button" class="btn btn-sm btn-success ms-3" onclick="forceEnable()">
            <i class="fas fa-bolt"></i> Force Enable Pathao
        </button>
        <details class="mt-2">
            <summary>All Settings Array</summary>
            <pre style="font-size: 11px; max-height: 200px; overflow: auto;"><?= print_r($settings, true) ?></pre>
        </details>
    </div>
    <script>
    function forceEnable() {
        fetch('<?= url('admin/pathao/force-enable') ?>')
            .then(r => r.json())
            .then(data => {
                alert(JSON.stringify(data));
                if (data.success) location.reload();
            })
            .catch(e => alert('Error: ' + e.message));
    }
    </script>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pathao Courier Settings</h1>
            <p class="text-muted mb-0">Configure Pathao Merchant API for automatic order pickup</p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                <i class="bi bi-plug"></i> Test Connection
            </button>
        </div>
    </div>

    <!-- Connection Status -->
    <div id="connectionStatus" class="alert d-none mb-4"></div>

    <form method="POST" action="<?= url('admin/pathao/update') ?>">
        <?= csrfField() ?>

        <div class="row">
            <!-- Enable/Disable -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="pathao_enabled" id="pathao_enabled" value="1" <?= ($settings['pathao_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="pathao_enabled">
                                <strong>Enable Pathao Integration</strong>
                                <span class="text-muted d-block">Automatically create pickup requests when orders are confirmed</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environment Selection -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Environment</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="pathao_environment" id="env_sandbox" value="sandbox" <?= ($settings['pathao_environment'] ?? 'sandbox') === 'sandbox' ? 'checked' : '' ?> onchange="toggleEnvironment()">
                            <label class="form-check-label" for="env_sandbox">
                                <strong>Sandbox (Testing)</strong>
                                <span class="text-muted d-block small">Use test credentials for development</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pathao_environment" id="env_production" value="production" <?= ($settings['pathao_environment'] ?? 'sandbox') === 'production' ? 'checked' : '' ?> onchange="toggleEnvironment()">
                            <label class="form-check-label" for="env_production">
                                <strong>Production (Live)</strong>
                                <span class="text-muted d-block small">Use real credentials for live orders</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Selection -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pathao Store</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadStores()">
                            <i class="bi bi-arrow-clockwise"></i> Load Stores
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Store ID</label>
                            <input type="text" name="pathao_store_id" id="pathao_store_id" class="form-control" value="<?= sanitize($settings['pathao_store_id'] ?? '') ?>" placeholder="Enter Pathao Store ID">
                            <small class="text-muted">Your Pathao merchant store ID for pickup location</small>
                        </div>
                        <div id="storesList" class="d-none">
                            <label class="form-label">Or select from your stores:</label>
                            <select class="form-select" id="storesDropdown" onchange="selectStore(this.value)">
                                <option value="">-- Select Store --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sandbox Credentials -->
            <div class="col-md-6 mb-4" id="sandbox_credentials">
                <div class="card h-100">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h5 class="mb-0"><i class="bi bi-cone-striped"></i> Sandbox Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" name="pathao_sandbox_client_id" class="form-control" value="<?= sanitize($settings['pathao_sandbox_client_id'] ?? '7N1aMJQbWm') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Client Secret</label>
                            <input type="text" name="pathao_sandbox_client_secret" class="form-control" value="<?= sanitize($settings['pathao_sandbox_client_secret'] ?? 'wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username (Email)</label>
                            <input type="email" name="pathao_sandbox_username" class="form-control" value="<?= sanitize($settings['pathao_sandbox_username'] ?? 'test@pathao.com') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="pathao_sandbox_password" class="form-control" value="<?= sanitize($settings['pathao_sandbox_password'] ?? 'lovePathao') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Production Credentials -->
            <div class="col-md-6 mb-4" id="production_credentials">
                <div class="card h-100">
                    <div class="card-header bg-success bg-opacity-10">
                        <h5 class="mb-0"><i class="bi bi-shield-check"></i> Production Credentials</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" name="pathao_client_id" class="form-control" value="<?= sanitize($settings['pathao_client_id'] ?? '') ?>" placeholder="Your production client ID">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Client Secret</label>
                            <input type="password" name="pathao_client_secret" class="form-control" value="<?= sanitize($settings['pathao_client_secret'] ?? '') ?>" placeholder="Your production client secret">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username (Email)</label>
                            <input type="email" name="pathao_username" class="form-control" value="<?= sanitize($settings['pathao_username'] ?? '') ?>" placeholder="Your Pathao merchant email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="pathao_password" class="form-control" value="<?= sanitize($settings['pathao_password'] ?? '') ?>" placeholder="Your Pathao merchant password">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Settings -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="pathao_auto_create" id="pathao_auto_create" value="1" <?= ($settings['pathao_auto_create'] ?? '1') === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="pathao_auto_create">
                                        <strong>Auto-create Pathao Order</strong>
                                        <span class="text-muted d-block small">Automatically create pickup request when order status is changed to "Processing"</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Default Parcel Weight (KG)</label>
                                <select name="pathao_default_weight" class="form-select">
                                    <option value="0.5" <?= ($settings['pathao_default_weight'] ?? '0.5') === '0.5' ? 'selected' : '' ?>>0.5 KG</option>
                                    <option value="1" <?= ($settings['pathao_default_weight'] ?? '0.5') === '1' ? 'selected' : '' ?>>1 KG</option>
                                    <option value="2" <?= ($settings['pathao_default_weight'] ?? '0.5') === '2' ? 'selected' : '' ?>>2 KG</option>
                                    <option value="3" <?= ($settings['pathao_default_weight'] ?? '0.5') === '3' ? 'selected' : '' ?>>3 KG</option>
                                    <option value="5" <?= ($settings['pathao_default_weight'] ?? '0.5') === '5' ? 'selected' : '' ?>>5 KG</option>
                                </select>
                                <small class="text-muted">Default weight for parcels (0.5 - 10 KG)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= url('admin/settings') ?>" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Settings
            </button>
        </div>
    </form>

    <!-- API Documentation -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">API Documentation</h5>
        </div>
        <div class="card-body">
            <p>To get your Pathao Merchant API credentials:</p>
            <ol>
                <li>Log in to your <a href="https://merchant.pathao.com" target="_blank">Pathao Merchant Panel</a></li>
                <li>Go to Settings &rarr; API Settings</li>
                <li>Copy your Client ID and Client Secret</li>
                <li>Use your Pathao login email and password</li>
            </ol>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Note:</strong> Start with Sandbox mode for testing. Switch to Production only when you're ready for live orders.
            </div>
        </div>
    </div>
</div>

<script>
function toggleEnvironment() {
    const isSandbox = document.getElementById('env_sandbox').checked;
    document.getElementById('sandbox_credentials').style.display = isSandbox ? 'block' : 'none';
    document.getElementById('production_credentials').style.display = isSandbox ? 'none' : 'block';
}

function testConnection() {
    const statusDiv = document.getElementById('connectionStatus');
    statusDiv.className = 'alert alert-info';
    statusDiv.classList.remove('d-none');
    statusDiv.innerHTML = '<i class="bi bi-hourglass-split"></i> Testing connection...';

    fetch('<?= url('admin/pathao/test') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusDiv.className = 'alert alert-success';
                statusDiv.innerHTML = '<i class="bi bi-check-circle"></i> ' + data.message + ' (Environment: ' + data.environment + ')';
            } else {
                statusDiv.className = 'alert alert-danger';
                statusDiv.innerHTML = '<i class="bi bi-x-circle"></i> ' + data.message;
            }
        })
        .catch(error => {
            statusDiv.className = 'alert alert-danger';
            statusDiv.innerHTML = '<i class="bi bi-x-circle"></i> Connection failed: ' + error.message;
        });
}

function loadStores() {
    const storesList = document.getElementById('storesList');
    const dropdown = document.getElementById('storesDropdown');

    dropdown.innerHTML = '<option value="">Loading...</option>';
    storesList.classList.remove('d-none');

    fetch('<?= url('admin/pathao/stores') ?>')
        .then(response => response.json())
        .then(data => {
            dropdown.innerHTML = '<option value="">-- Select Store --</option>';
            if (data.stores && data.stores.length > 0) {
                data.stores.forEach(store => {
                    const option = document.createElement('option');
                    option.value = store.store_id;
                    option.textContent = store.store_name + ' (' + store.store_address + ')';
                    dropdown.appendChild(option);
                });
            } else {
                dropdown.innerHTML = '<option value="">No stores found</option>';
            }
        })
        .catch(error => {
            dropdown.innerHTML = '<option value="">Error loading stores</option>';
        });
}

function selectStore(storeId) {
    if (storeId) {
        document.getElementById('pathao_store_id').value = storeId;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleEnvironment();
});
</script>
