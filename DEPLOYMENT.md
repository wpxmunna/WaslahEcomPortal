# Waslah E-Commerce Portal - Deployment Guide

## Table of Contents
1. [Server Requirements](#server-requirements)
2. [Local Development Setup](#local-development-setup)
3. [Shared Hosting Deployment](#shared-hosting-deployment)
4. [Configuration](#configuration)
5. [Security Hardening](#security-hardening)
6. [Performance Optimization](#performance-optimization)
7. [Backup & Maintenance](#backup--maintenance)
8. [Troubleshooting](#troubleshooting)

---

## Server Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP Version | 8.0+ | 8.1+ |
| MySQL | 5.7+ | 8.0+ |
| Web Server | Apache 2.4+ | Apache 2.4+ with mod_rewrite |
| Memory | 128MB | 256MB+ |
| Disk Space | 100MB | 500MB+ (for uploads) |

### Required PHP Extensions
```
- PDO & PDO_MySQL
- mbstring
- json
- fileinfo
- gd (for image processing)
- openssl
```

### Check PHP Extensions
```php
<?php
$required = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'fileinfo', 'gd', 'openssl'];
foreach ($required as $ext) {
    echo $ext . ': ' . (extension_loaded($ext) ? 'OK' : 'MISSING') . "\n";
}
```

---

## Local Development Setup

### XAMPP/WAMP/MAMP Setup

1. **Place files in web directory**
   ```
   # XAMPP (Windows)
   C:\xampp\htdocs\WaslahEcomPortal\

   # WAMP (Windows)
   C:\wamp64\www\WaslahEcomPortal\

   # MAMP (Mac)
   /Applications/MAMP/htdocs/WaslahEcomPortal/

   # Linux
   /var/www/html/WaslahEcomPortal/
   ```

2. **Create the database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database: `waslah_ecom`
   - Import: `database/schema.sql`

3. **Configure database** (`config/database.php`)
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'waslah_ecom');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Your MySQL password
   ```

4. **Configure site URL** (`config/config.php`)
   ```php
   define('SITE_URL', 'http://localhost/WaslahEcomPortal');
   ```

5. **Access the site**
   - Frontend: `http://localhost/WaslahEcomPortal`
   - Admin: `http://localhost/WaslahEcomPortal/admin`

### Default Admin Credentials
| Field | Value |
|-------|-------|
| Email | admin@waslah.com |
| Password | admin123 |

---

## Shared Hosting Deployment

### Compatible Hosting Providers
- Hostinger
- Namecheap
- GoDaddy
- Bluehost
- SiteGround
- HostSeba
- Any cPanel-based host with PHP 8.0+

### Step-by-Step Deployment

#### Step 1: Prepare Files
```bash
# Create deployment package (exclude dev files)
zip -r waslah-deploy.zip . -x "*.git*" -x "*.md" -x "logs/*"
```

#### Step 2: Upload Files

**Option A: File Manager**
1. Login to cPanel
2. Open File Manager
3. Navigate to `public_html` (or subdirectory)
4. Upload and extract `waslah-deploy.zip`

**Option B: FTP**
1. Use FileZilla or similar FTP client
2. Connect with FTP credentials
3. Upload all files to `public_html`

#### Step 3: Create MySQL Database

1. **cPanel → MySQL Databases**
2. Create new database: `username_waslah`
3. Create new user: `username_waslahuser`
4. Add user to database with **All Privileges**
5. **Import schema:**
   - Go to phpMyAdmin
   - Select your database
   - Import → Choose `database/schema.sql`
   - Click "Go"

#### Step 4: Configure Application

**Edit `config/database.php`:**
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'username_waslah');      // Your full database name
define('DB_USER', 'username_waslahuser');  // Your full username
define('DB_PASS', 'YourSecurePassword');   // Database password
define('DB_CHARSET', 'utf8mb4');
```

**Edit `config/config.php`:**
```php
<?php
// Site Settings
define('SITE_NAME', 'Waslah');
define('SITE_TAGLINE', 'Authenticity in Every Stitch');
define('SITE_URL', 'https://yourdomain.com');  // NO trailing slash!
define('SITE_EMAIL', 'orders@yourdomain.com');

// Currency
define('CURRENCY_SYMBOL', '৳');
define('CURRENCY_CODE', 'BDT');
```

#### Step 5: Configure .htaccess

**For Root Domain** (`https://yourdomain.com`):
```apache
RewriteBase /
```

**For Subdirectory** (`https://yourdomain.com/shop`):
```apache
RewriteBase /shop/
```

**For Subdomain** (`https://shop.yourdomain.com`):
```apache
RewriteBase /
```

#### Step 6: Set File Permissions

| Path | Permission | Purpose |
|------|------------|---------|
| `uploads/` | 755 | Product images |
| `uploads/products/` | 755 | Product uploads |
| `logs/` | 755 | Error logs |
| All `.php` files | 644 | PHP scripts |
| All directories | 755 | Folders |

**Via cPanel File Manager:**
1. Right-click folder → Change Permissions
2. Set numeric value (755 or 644)

**Via SSH:**
```bash
find /path/to/waslah -type d -exec chmod 755 {} \;
find /path/to/waslah -type f -exec chmod 644 {} \;
chmod 755 uploads/ logs/
```

#### Step 7: Create Required Directories

```bash
mkdir -p uploads/products
mkdir -p logs
chmod 755 uploads uploads/products logs
```

#### Step 8: Enable HTTPS

Uncomment in `.htaccess`:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Configuration

### Site Configuration (`config/config.php`)

```php
<?php
// ===================
// SITE SETTINGS
// ===================
define('SITE_NAME', 'Waslah');
define('SITE_TAGLINE', 'Authenticity in Every Stitch');
define('SITE_URL', 'https://yourdomain.com');
define('SITE_EMAIL', 'info@yourdomain.com');

// ===================
// PATHS
// ===================
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('VIEW_PATH', ROOT_PATH . '/views');

// ===================
// CURRENCY
// ===================
// Bangladesh Taka
define('CURRENCY_SYMBOL', '৳');
define('CURRENCY_CODE', 'BDT');

// US Dollar
// define('CURRENCY_SYMBOL', '$');
// define('CURRENCY_CODE', 'USD');

// ===================
// PAGINATION
// ===================
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);

// ===================
// UPLOADS
// ===================
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ===================
// SHIPPING
// ===================
define('FREE_SHIPPING_THRESHOLD', 5000.00);
define('DEFAULT_SHIPPING_COST', 80.00);
```

### Database Configuration (`config/database.php`)

```php
<?php
// ===================
// DATABASE SETTINGS
// ===================
define('DB_HOST', 'localhost');
define('DB_NAME', 'waslah_ecom');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// For some shared hosts, you might need:
// define('DB_HOST', '127.0.0.1');
// or
// define('DB_HOST', 'mysql.yourdomain.com');
```

---

## Security Hardening

### 1. Disable Error Display (Production)

**Edit `index.php`:**
```php
// Development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/php-errors.log');
```

### 2. Protect Sensitive Files

**Add to `.htaccess`:**
```apache
# Deny access to sensitive files
<FilesMatch "\.(sql|md|json|lock|log|ini|sh)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Protect config directory
<IfModule mod_rewrite.c>
    RewriteRule ^config/ - [F,L]
    RewriteRule ^core/ - [F,L]
    RewriteRule ^models/ - [F,L]
    RewriteRule ^includes/ - [F,L]
</IfModule>
```

### 3. Secure Uploads Directory

**Create `uploads/.htaccess`:**
```apache
# Disable PHP execution in uploads
php_flag engine off

<FilesMatch "\.php$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Only allow image files
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Order Deny,Allow
    Allow from all
</FilesMatch>
```

### 4. Change Admin Password

**Immediately after deployment:**
```sql
-- Generate new password hash
-- Use PHP: echo password_hash('YourNewPassword', PASSWORD_DEFAULT);

UPDATE users
SET password = '$2y$10$YourGeneratedHashHere'
WHERE email = 'admin@waslah.com';
```

### 5. Update Admin Email

```sql
UPDATE users
SET email = 'youremail@domain.com'
WHERE role = 'admin';
```

### 6. Database Security

Create dedicated database user with limited privileges:
```sql
CREATE USER 'waslah_app'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON waslah_ecom.* TO 'waslah_app'@'localhost';
FLUSH PRIVILEGES;
```

---

## Performance Optimization

### 1. Enable Gzip Compression

**Add to `.htaccess`:**
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css
    AddOutputFilterByType DEFLATE application/javascript application/json
    AddOutputFilterByType DEFLATE text/xml application/xml
</IfModule>
```

### 2. Enable Browser Caching

**Add to `.htaccess`:**
```apache
<IfModule mod_expires.c>
    ExpiresActive On

    # Images
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"

    # CSS & JavaScript
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"

    # Fonts
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
</IfModule>
```

### 3. Optimize Images

Before uploading product images:
- Resize to max 1200px width
- Compress using TinyPNG or similar
- Use WebP format when possible

---

## Backup & Maintenance

### Database Backup

**Manual Backup (phpMyAdmin):**
1. Open phpMyAdmin
2. Select database
3. Export → Quick → Go
4. Save the .sql file

**Command Line:**
```bash
mysqldump -u username -p waslah_ecom > backup_$(date +%Y%m%d).sql
```

**Automated Backup (Cron Job):**
```bash
# Add to crontab (runs daily at 2 AM)
0 2 * * * mysqldump -u username -p'password' waslah_ecom > /backups/waslah_$(date +\%Y\%m\%d).sql
```

### Files Backup

```bash
# Backup uploads only
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/

# Full site backup
tar -czf waslah_full_$(date +%Y%m%d).tar.gz \
    --exclude='logs' \
    --exclude='.git' \
    --exclude='*.log' \
    /path/to/waslah/
```

### Maintenance Checklist

**Weekly:**
- [ ] Check error logs
- [ ] Review failed orders
- [ ] Backup database

**Monthly:**
- [ ] Full site backup
- [ ] Check disk usage
- [ ] Review security logs
- [ ] Update admin password

---

## Troubleshooting

### 500 Internal Server Error

**Causes & Solutions:**

1. **Bad .htaccess syntax**
   ```bash
   # Temporarily rename to test
   mv .htaccess .htaccess.bak
   ```

2. **mod_rewrite not enabled**
   ```bash
   sudo a2enmod rewrite
   sudo service apache2 restart
   ```

3. **Wrong PHP version**
   - Set PHP 8.0+ in cPanel → MultiPHP Manager

4. **File permissions**
   ```bash
   chmod 644 index.php
   chmod 755 uploads/
   ```

### Database Connection Failed

1. Verify credentials in `config/database.php`
2. Check database exists: `SHOW DATABASES;`
3. Test connection:
   ```php
   <?php
   $conn = new PDO('mysql:host=localhost;dbname=waslah_ecom', 'user', 'pass');
   echo "Connected!";
   ```

### 404 on All Pages

1. **Enable mod_rewrite:**
   ```bash
   sudo a2enmod rewrite
   ```

2. **Allow .htaccess override:**
   ```apache
   # In Apache config or vhost
   <Directory /var/www/html>
       AllowOverride All
   </Directory>
   ```

3. **Check RewriteBase** in `.htaccess`

### Images Not Loading

1. Check SITE_URL (no trailing slash)
2. Verify uploads folder exists
3. Check file permissions (755)
4. Verify paths in database

### Admin Login Not Working

**Reset password via SQL:**
```sql
-- Reset to 'admin123'
UPDATE users
SET password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4.PXHxMZNGjOxkWy'
WHERE email = 'admin@waslah.com';
```

### Session Issues

1. Check session save path permissions
2. Ensure no output before `session_start()`
3. Check for BOM in PHP files:
   ```bash
   file *.php | grep BOM
   ```

---

## Quick Reference

### Important URLs

| Page | URL |
|------|-----|
| Homepage | `/` |
| Shop | `/shop` |
| Cart | `/cart` |
| Checkout | `/checkout` |
| Login | `/login` |
| Register | `/register` |
| My Account | `/account` |
| Admin Login | `/admin/login` |
| Admin Dashboard | `/admin` |

### Configuration Files

| File | Purpose |
|------|---------|
| `config/config.php` | Site settings, URLs, currency |
| `config/database.php` | Database connection |
| `config/routes.php` | URL routing |
| `.htaccess` | Apache rewrite rules |

### File Structure

```
WaslahEcomPortal/
├── config/              # Configuration
├── controllers/         # Page controllers
│   └── admin/          # Admin controllers
├── core/               # Framework classes
├── database/           # SQL schema
├── includes/           # Helpers & services
│   ├── mock/          # Mock payment/courier
│   └── services/      # External services
├── models/             # Data models
├── public/             # Static assets
│   ├── css/
│   ├── js/
│   └── images/
├── uploads/            # User uploads
├── views/              # Templates
│   ├── admin/
│   ├── layouts/
│   └── ...
├── logs/               # Error logs
├── .htaccess
└── index.php
```

---

## Support

1. Check logs: `logs/` and PHP error logs
2. Verify all configuration settings
3. Test database connection
4. Check file permissions

---

**Built with:** PHP 8.0+ | MySQL 5.7+ | Bootstrap 5 | Vanilla JavaScript
