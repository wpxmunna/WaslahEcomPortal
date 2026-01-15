<?php
require_once __DIR__ . '/config/config.php';
echo "CURRENCY_SYMBOL value: " . CURRENCY_SYMBOL . "<br>";
echo "CURRENCY_SYMBOL raw: ";
var_dump(CURRENCY_SYMBOL);
echo "<br>Character codes: ";
for ($i = 0; $i < strlen(CURRENCY_SYMBOL); $i++) {
    echo ord(CURRENCY_SYMBOL[$i]) . " ";
}
