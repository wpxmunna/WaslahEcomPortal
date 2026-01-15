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

// Currency - CHANGED TO BDT TEXT ONLY
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
