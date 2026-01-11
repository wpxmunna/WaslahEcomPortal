<?php
/**
 * Application Configuration
 */

// Site Settings
define('SITE_NAME', 'Waslah');
define('SITE_TAGLINE', 'Authenticity in Every Stitch');
define('SITE_URL', 'http://localhost:8000');
define('SITE_EMAIL', 'info@waslah.com');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('VIEW_PATH', ROOT_PATH . '/views');

// Currency (Bangladeshi Taka)
define('CURRENCY_SYMBOL', '৳');
define('CURRENCY_CODE', 'BDT');

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);

// Session
define('SESSION_NAME', 'waslah_session');
define('SESSION_LIFETIME', 7200); // 2 hours

// Security
define('HASH_COST', 12);

// Image Settings
define('MAX_IMAGE_SIZE', 20 * 1024 * 1024); // 20MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('THUMB_WIDTH', 300);
define('THUMB_HEIGHT', 400);

// Tax
define('TAX_RATE', 0.00); // 0%

// Shipping (in BDT)
define('FREE_SHIPPING_THRESHOLD', 5000.00);  // Free shipping over ৳5000
define('DEFAULT_SHIPPING_COST', 80.00);       // ৳80 shipping cost

// Meta Business Suite Integration (Facebook, Instagram, WhatsApp)
// Get these from https://developers.facebook.com/apps
define('META_APP_ID', '');           // Your Meta App ID
define('META_APP_SECRET', '');       // Your Meta App Secret
define('META_WEBHOOK_VERIFY_TOKEN', 'waslah_verify_token');  // Custom token for webhook verification
