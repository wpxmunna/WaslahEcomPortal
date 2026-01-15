<?php
/**
 * FORCE FIX CONFIG.PHP
 * This will overwrite config.php with correct values
 */

$configContent = <<<'CONFIGPHP'
<?php
/**
 * Application Configuration
 */

// Site Settings
define('SITE_NAME', 'Waslah');
define('SITE_TAGLINE', 'Authenticity in Every Stitch');
define('SITE_LOGO', 'images/logo.png');
define('SITE_URL', 'https://waslah.gt.tc');
define('SITE_EMAIL', 'info@waslah.com');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('VIEW_PATH', ROOT_PATH . '/views');

// Currency - BDT TEXT ONLY
define('CURRENCY_SYMBOL', 'BDT');
define('CURRENCY_CODE', 'BDT');

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);

// Session
define('SESSION_NAME', 'waslah_session');
define('SESSION_LIFETIME', 7200);

// Security
define('HASH_COST', 12);

// Image Settings
define('MAX_IMAGE_SIZE', 20 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('THUMB_WIDTH', 300);
define('THUMB_HEIGHT', 400);

// Tax
define('TAX_RATE', 0.00);

// Shipping (in BDT)
define('FREE_SHIPPING_THRESHOLD', 5000.00);
define('DEFAULT_SHIPPING_COST', 80.00);

// Meta Business Suite Integration
define('META_APP_ID', '');
define('META_APP_SECRET', '');
define('META_WEBHOOK_VERIFY_TOKEN', 'waslah_verify_token');
CONFIGPHP;

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Force Fix Config</title></head><body>";
echo "<h1>Force Fix Config.php</h1>";
echo "<hr>";

$configPath = __DIR__ . '/config/config.php';
$backupPath = __DIR__ . '/config/config.php.backup.' . date('YmdHis');

echo "<h2>Step 1: Backup Old Config</h2>";
if (file_exists($configPath)) {
    if (copy($configPath, $backupPath)) {
        echo "<p style='color:green;'>✅ Backed up to: " . basename($backupPath) . "</p>";
    } else {
        echo "<p style='color:red;'>❌ Could not backup old file</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠ No existing config.php found</p>";
}

echo "<h2>Step 2: Write New Config</h2>";
$result = file_put_contents($configPath, $configContent);

if ($result !== false) {
    echo "<p style='color:green;'>✅ Successfully wrote new config.php ($result bytes)</p>";
    
    // Verify it worked
    echo "<h2>Step 3: Verify New Config</h2>";
    
    // Clear any opcode cache
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($configPath, true);
        echo "<p>✅ Cleared opcode cache</p>";
    }
    
    // Load and test
    require_once $configPath;
    
    echo "<p><strong>CURRENCY_SYMBOL:</strong> <span style='font-size:20px; color:blue;'>" . CURRENCY_SYMBOL . "</span></p>";
    echo "<p><strong>Character codes:</strong> ";
    for ($i = 0; $i < strlen(CURRENCY_SYMBOL); $i++) {
        echo ord(CURRENCY_SYMBOL[$i]) . " ";
    }
    echo "</p>";
    
    if (CURRENCY_SYMBOL === 'BDT') {
        echo "<h2 style='color:green;'>✅✅✅ SUCCESS! Config is now correct!</h2>";
        echo "<p><a href='/fix_currency.php' style='font-size:18px;'>→ Run Full Test Again</a></p>";
        echo "<p><a href='/' style='font-size:18px;'>→ Go to Homepage</a></p>";
    } else {
        echo "<h2 style='color:red;'>❌ Still incorrect!</h2>";
        echo "<p>CURRENCY_SYMBOL = " . var_export(CURRENCY_SYMBOL, true) . "</p>";
    }
    
} else {
    echo "<p style='color:red;'>❌ Failed to write config.php</p>";
    echo "<p>Check file permissions on /config/ folder (should be 755)</p>";
}

echo "<hr>";
echo "<p><small>This script overwrites config/config.php with clean BDT settings</small></p>";
echo "</body></html>";
