-- Remove currency settings from database
-- Currency is now configured in config/config.php

-- Delete currency_symbol and currency_code from settings table
DELETE FROM settings WHERE setting_key IN ('currency_symbol', 'currency_code');

-- Optional: Remove currency columns from stores table (commented out for safety)
-- ALTER TABLE stores DROP COLUMN currency_symbol;
-- ALTER TABLE stores DROP COLUMN currency_code;

-- Note: After running this, all currency symbols will come from config/config.php
