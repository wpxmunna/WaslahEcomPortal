<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Diagnose</title></head><body>";
echo "<h1>System Diagnosis</h1>";
echo "<hr>";

// Test 1: Config file
echo "<h2>Test 1: Load Config</h2>";
try {
    require_once __DIR__ . '/config/config.php';
    echo "<p style='color:green;'>✅ Config loaded</p>";
    echo "<p>CURRENCY_SYMBOL = <strong>" . CURRENCY_SYMBOL . "</strong></p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Config error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 2: Database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "<p style='color:green;'>✅ Database config loaded</p>";
    echo "<p>DB_HOST = " . DB_HOST . "</p>";
    echo "<p>DB_NAME = " . DB_NAME . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database config error: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Test 3: Database class
echo "<h2>Test 3: Database Class</h2>";
try {
    require_once __DIR__ . '/core/Database.php';
    echo "<p style='color:green;'>✅ Database class loaded</p>";
    
    $db = new Database();
    echo "<p style='color:green;'>✅ Database object created</p>";
    
    $conn = $db->getConnection();
    echo "<p style='color:green;'>✅ Database connected!</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";

// Test 4: Query database
if (isset($conn)) {
    echo "<h2>Test 4: Query Database</h2>";
    try {
        // Check settings table
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM settings");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p style='color:green;'>✅ Settings table has {$result['cnt']} rows</p>";
        
        // Check stores table  
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM stores");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p style='color:green;'>✅ Stores table has {$result['cnt']} rows</p>";
        
        // Check currency in stores
        $stmt = $conn->prepare("SELECT id, name, currency_symbol FROM stores LIMIT 1");
        $stmt->execute();
        $store = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($store) {
            echo "<p>Store #1: <strong>" . htmlspecialchars($store['name']) . "</strong></p>";
            echo "<p>Currency Symbol in DB: <strong style='font-size:20px;'>" . htmlspecialchars($store['currency_symbol']) . "</strong></p>";
            
            // Show character codes
            echo "<p>Character codes: ";
            for ($i = 0; $i < strlen($store['currency_symbol']); $i++) {
                echo ord($store['currency_symbol'][$i]) . " ";
            }
            echo "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Query error: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If all tests pass, run the cleanup script next.</p>";
echo "<p><a href='/clean_database_currency.php'>→ Run Database Cleanup</a></p>";

echo "</body></html>";
