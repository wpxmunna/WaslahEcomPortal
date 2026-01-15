<?php
echo "<h2>Configuration Verification</h2>";
echo "<hr>";

echo "<h3>1. Check if config.php exists:</h3>";
if (file_exists(__DIR__ . '/config/config.php')) {
    echo "✅ config.php EXISTS<br>";
    
    // Show file modification time
    $mtime = filemtime(__DIR__ . '/config/config.php');
    echo "Last modified: " . date('Y-m-d H:i:s', $mtime) . "<br>";
    
    // Show file size
    echo "File size: " . filesize(__DIR__ . '/config/config.php') . " bytes<br>";
} else {
    echo "❌ config.php NOT FOUND<br>";
}

echo "<hr>";
echo "<h3>2. Load config and check CURRENCY_SYMBOL:</h3>";
require_once __DIR__ . '/config/config.php';

echo "CURRENCY_SYMBOL = <strong>" . CURRENCY_SYMBOL . "</strong><br>";
echo "CURRENCY_CODE = <strong>" . CURRENCY_CODE . "</strong><br>";

echo "<hr>";
echo "<h3>3. Character Analysis:</h3>";
echo "CURRENCY_SYMBOL length: " . strlen(CURRENCY_SYMBOL) . " characters<br>";
echo "Character codes: ";
for ($i = 0; $i < strlen(CURRENCY_SYMBOL); $i++) {
    $char = CURRENCY_SYMBOL[$i];
    $code = ord($char);
    echo "$code ";
}
echo "<br>";

echo "<hr>";
echo "<h3>4. Expected Values:</h3>";
echo "Should be: <strong>BDT</strong><br>";
echo "Character codes should be: 66 68 84<br>";

echo "<hr>";
echo "<h3>5. Test formatPrice() function:</h3>";
require_once __DIR__ . '/includes/helpers.php';
echo "formatPrice(100): " . formatPrice(100) . "<br>";
echo "formatPrice(1500.50): " . formatPrice(1500.50) . "<br>";

echo "<hr>";
echo "<h3>6. Raw var_dump:</h3>";
echo "<pre>";
var_dump(CURRENCY_SYMBOL);
echo "</pre>";
