<?php
/**
 * Fix helpers.php to bypass cached CURRENCY_SYMBOL
 */

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Fix Helpers Currency</title></head><body>";
echo "<h1>Fix helpers.php Currency Functions</h1>";
echo "<hr>";

$helpersFile = __DIR__ . '/includes/helpers.php';

if (!file_exists($helpersFile)) {
    echo "<p style='color:red;'>❌ helpers.php not found!</p>";
    exit;
}

// Read current content
$content = file_get_contents($helpersFile);

echo "<h2>Step 1: Current Functions</h2>";
echo "<pre>" . htmlspecialchars(substr($content, strpos($content, 'function currencySymbol'), 300)) . "</pre>";

// Replace currencySymbol function
$oldCurrencySymbol = 'function currencySymbol(): string
{
    return CURRENCY_SYMBOL;
}';

$newCurrencySymbol = 'function currencySymbol(): string
{
    // FORCE BDT - bypasses any cached CURRENCY_SYMBOL constant
    return \'BDT\';
}';

$content = str_replace($oldCurrencySymbol, $newCurrencySymbol, $content);

// Replace formatPrice function
$oldFormatPrice = 'function formatPrice(float $price): string
{
    return CURRENCY_SYMBOL . \' \' . number_format($price, 2);
}';

$newFormatPrice = 'function formatPrice(float $price): string
{
    // FORCE BDT - bypasses any cached CURRENCY_SYMBOL constant
    return \'BDT \' . number_format($price, 2);
}';

$content = str_replace($oldFormatPrice, $newFormatPrice, $content);

echo "<hr>";
echo "<h2>Step 2: Write Updated File</h2>";

// Backup original
$backupFile = $helpersFile . '.backup_' . date('YmdHis');
if (copy($helpersFile, $backupFile)) {
    echo "<p style='color:green;'>✅ Created backup: $backupFile</p>";
}

// Write new content
if (file_put_contents($helpersFile, $content)) {
    echo "<p style='color:green;'>✅ Updated helpers.php successfully!</p>";

    // Clear opcache for helpers.php
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($helpersFile, true);
        echo "<p style='color:green;'>✅ Cleared opcache for helpers.php</p>";
    }

    echo "<hr>";
    echo "<h2>Step 3: Test Functions</h2>";

    // Reload helpers.php
    require_once $helpersFile;

    echo "<p><strong>currencySymbol():</strong> <span style='font-size:20px; color:blue;'>" . currencySymbol() . "</span></p>";
    echo "<p><strong>formatPrice(100):</strong> <span style='font-size:24px; color:green;'>" . formatPrice(100) . "</span></p>";
    echo "<p><strong>formatPrice(29.99):</strong> <span style='font-size:24px; color:green;'>" . formatPrice(29.99) . "</span></p>";

    echo "<hr>";
    echo "<h2 style='color:green;'>✅ CURRENCY FUNCTIONS FIXED!</h2>";
    echo "<p>All prices should now display as 'BDT' across the entire site.</p>";

    echo "<hr>";
    echo "<h3>Next Step: Delete This Script</h3>";
    echo "<p>This script will delete itself in 3 seconds...</p>";
    echo "<meta http-equiv='refresh' content='3;url=/'>";

    // Delete this script after 3 seconds
    sleep(3);
    unlink(__FILE__);

} else {
    echo "<p style='color:red;'>❌ Failed to update helpers.php</p>";
    echo "<p>You may need to update file permissions or manually edit the file.</p>";
}

echo "</body></html>";
