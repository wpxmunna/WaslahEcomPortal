<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Clean Database Currency</title></head><body>";
echo "<h1>Database Currency Cleanup</h1>";
echo "<hr>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    echo "<h2>Step 1: Check Current Database Values</h2>";
    
    // Check settings table
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('currency_symbol', 'currency_code')");
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($settings)) {
        echo "<p>Found in SETTINGS table:</p><ul>";
        foreach ($settings as $setting) {
            echo "<li><strong>{$setting['setting_key']}:</strong> " . htmlspecialchars($setting['setting_value']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>✅ No currency in SETTINGS table</p>";
    }
    
    // Check stores table
    $stmt = $conn->prepare("SELECT id, name, currency_symbol, currency_code FROM stores");
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($stores)) {
        echo "<p>Found in STORES table:</p><ul>";
        foreach ($stores as $store) {
            $symbol = htmlspecialchars($store['currency_symbol']);
            $code = htmlspecialchars($store['currency_code']);
            echo "<li>Store #{$store['id']} ({$store['name']}): Symbol = <strong>$symbol</strong>, Code = <strong>$code</strong></li>";
        }
        echo "</ul>";
    }
    
    echo "<hr>";
    echo "<h2>Step 2: Clean Database</h2>";
    
    // Delete from settings
    $stmt = $conn->prepare("DELETE FROM settings WHERE setting_key IN ('currency_symbol', 'currency_code')");
    $stmt->execute();
    $deleted1 = $stmt->rowCount();
    echo "<p style='color:green;'>✅ Deleted $deleted1 rows from SETTINGS table</p>";
    
    // Update stores table to BDT
    $stmt = $conn->prepare("UPDATE stores SET currency_symbol = 'BDT', currency_code = 'BDT'");
    $stmt->execute();
    $updated = $stmt->rowCount();
    echo "<p style='color:green;'>✅ Updated $updated stores to use BDT</p>";
    
    echo "<hr>";
    echo "<h2>Step 3: Verify Config Constant</h2>";
    echo "<p><strong>CURRENCY_SYMBOL from config:</strong> <span style='font-size:20px; color:blue;'>" . CURRENCY_SYMBOL . "</span></p>";
    echo "<p><strong>CURRENCY_CODE from config:</strong> <span style='font-size:20px; color:blue;'>" . CURRENCY_CODE . "</span></p>";
    
    echo "<hr>";
    echo "<h2>Step 4: Test formatPrice()</h2>";
    require_once __DIR__ . '/includes/helpers.php';
    echo "<p><strong>formatPrice(100):</strong> <span style='font-size:24px; color:green;'>" . formatPrice(100) . "</span></p>";
    echo "<p><strong>formatPrice(29.99):</strong> <span style='font-size:24px; color:green;'>" . formatPrice(29.99) . "</span></p>";
    
    echo "<hr>";
    echo "<h2 style='color:green;'>✅ DATABASE CLEANED!</h2>";
    echo "<p>Currency now comes from config.php only (BDT)</p>";
    
    echo "<hr>";
    echo "<p><a href='/' style='font-size:18px; padding:10px 20px; background:blue; color:white; text-decoration:none; border-radius:5px;'>Test Website →</a></p>";
    echo "<p><a href='/admin/login' style='font-size:18px; padding:10px 20px; background:green; color:white; text-decoration:none; border-radius:5px;'>Admin Login →</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red; font-size:18px;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
