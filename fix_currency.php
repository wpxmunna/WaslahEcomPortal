<?php
/**
 * Currency Fix Script
 * This script will:
 * 1. Show current config values
 * 2. Delete old currency from database
 * 3. Show test output
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Currency Fix</title></head><body>";
echo "<h1>Currency Fix Script</h1>";
echo "<hr>";

// Step 1: Show config values
echo "<h2>Step 1: Config File Values</h2>";
echo "CURRENCY_SYMBOL: <strong style='font-size:20px; color:blue;'>" . htmlspecialchars(CURRENCY_SYMBOL) . "</strong><br>";
echo "CURRENCY_CODE: <strong>" . CURRENCY_CODE . "</strong><br>";
echo "Character codes: ";
for ($i = 0; $i < strlen(CURRENCY_SYMBOL); $i++) {
    echo ord(CURRENCY_SYMBOL[$i]) . " ";
}
echo "<br>";
echo "Expected: 66 68 84 (for 'BDT')<br>";

echo "<hr>";

// Step 2: Database cleanup
echo "<h2>Step 2: Database Cleanup</h2>";
try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Delete old currency settings
    $stmt = $conn->prepare("DELETE FROM settings WHERE setting_key IN ('currency_symbol', 'currency_code')");
    $stmt->execute();
    $deleted = $stmt->rowCount();
    
    echo "<p style='color:green;'>✅ Deleted $deleted old currency records from database</p>";
    
    // Check stores table
    $stmt = $conn->prepare("SELECT id, name, currency_symbol FROM stores LIMIT 5");
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($stores)) {
        echo "<p>Stores table currency_symbol values:</p>";
        echo "<ul>";
        foreach ($stores as $store) {
            echo "<li>Store #{$store['id']} ({$store['name']}): " . htmlspecialchars($store['currency_symbol']) . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Step 3: Test formatPrice
echo "<h2>Step 3: Test formatPrice() Function</h2>";
require_once __DIR__ . '/includes/helpers.php';
echo "formatPrice(100): <strong style='font-size:18px;'>" . formatPrice(100) . "</strong><br>";
echo "formatPrice(29.99): <strong style='font-size:18px;'>" . formatPrice(29.99) . "</strong><br>";
echo "formatPrice(1500): <strong style='font-size:18px;'>" . formatPrice(1500) . "</strong><br>";

echo "<hr>";
echo "<h2>Expected Results:</h2>";
echo "<p>✅ CURRENCY_SYMBOL should be: <strong>BDT</strong></p>";
echo "<p>✅ Character codes should be: <strong>66 68 84</strong></p>";
echo "<p>✅ formatPrice(100) should be: <strong>BDT 100.00</strong></p>";

echo "<hr>";
echo "<p><a href='/'>← Back to Home</a> | <a href='/admin/login'>Admin Login →</a></p>";
echo "</body></html>";
