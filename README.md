# Waslah E-Commerce Platform

A complete, production-ready e-commerce solution for fashion retail (Men, Women, Children) built with PHP and MySQL. Designed to work on any shared hosting provider.

---

## Table of Contents

1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Usage Guide](#usage-guide)
6. [Admin Panel](#admin-panel)
7. [File Structure](#file-structure)
8. [Database Schema](#database-schema)
9. [API Reference](#api-reference)
10. [Customization](#customization)
11. [Troubleshooting](#troubleshooting)
12. [Security](#security)

---

## Features

### Customer Features
- **Product Browsing**: Browse by category, search, filter by price/size/color
- **Product Details**: Image gallery, size/color variants, related products
- **Shopping Cart**: Add, update, remove items with real-time updates
- **User Accounts**: Registration, login, profile management
- **Checkout**: Multi-step checkout with address and payment selection
- **Order Tracking**: Real-time shipment tracking with status updates
- **Wishlist**: Save favorite products for later

### Admin Features
- **Dashboard**: Sales stats, revenue charts, low stock alerts
- **Product Management**: Full CRUD with images, variants, SEO settings
- **Order Management**: View, update status, generate invoices
- **Category Management**: Hierarchical categories with images
- **Customer Management**: View customers and their order history
- **Multi-Store**: Create and manage multiple stores
- **Courier Management**: Configure shipping methods and rates
- **Payment Settings**: Configure payment gateways
- **Reports**: Sales and product reports

### Technical Features
- **MVC Architecture**: Clean separation of concerns
- **Routing System**: SEO-friendly URLs
- **Session Management**: Secure session handling
- **CSRF Protection**: Form security tokens
- **Password Hashing**: Bcrypt with configurable cost
- **Image Upload**: Secure file handling with validation
- **Responsive Design**: Mobile-first with Bootstrap 5

---

## Requirements

- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher
- **Apache**: With mod_rewrite enabled
- **Extensions**: PDO, PDO_MySQL, GD (for images)

### Recommended Local Development
- XAMPP (Windows) - https://www.apachefriends.org/
- MAMP (Mac) - https://www.mamp.info/
- WAMP (Windows) - https://www.wampserver.com/
- Laragon (Windows) - https://laragon.org/

---

## Installation

### Step 1: Download/Clone Files

Place the `WaslahEcomPortal` folder in your web server directory:

```
XAMPP:  C:\xampp\htdocs\WaslahEcomPortal\
WAMP:   C:\wamp64\www\WaslahEcomPortal\
MAMP:   /Applications/MAMP/htdocs/WaslahEcomPortal/
Laragon: C:\laragon\www\WaslahEcomPortal\
```

### Step 2: Create Database

1. Start your local server (Apache + MySQL)
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Click "New" to create a database
4. Name it: `waslah_ecom`
5. Collation: `utf8mb4_unicode_ci`
6. Click "Create"

### Step 3: Import Database Schema

1. Select the `waslah_ecom` database
2. Click "Import" tab
3. Choose file: `database/schema.sql`
4. Click "Go"

### Step 4: Import Sample Data (Optional)

1. Stay in the `waslah_ecom` database
2. Click "Import" tab
3. Choose file: `database/sample_data.sql`
4. Click "Go"

### Step 5: Configure Application

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'waslah_ecom');
define('DB_USER', 'root');
define('DB_PASS', '');  // Empty for XAMPP, 'root' for MAMP
```

Edit `config/config.php`:
```php
define('SITE_URL', 'http://localhost/WaslahEcomPortal');
```

### Step 6: Set Permissions

Ensure these folders are writable:
- `uploads/`
- `uploads/products/`
- `uploads/stores/`
- `uploads/banners/`
- `logs/`

### Step 7: Access the Site

- **Frontend**: http://localhost/WaslahEcomPortal
- **Admin Panel**: http://localhost/WaslahEcomPortal/admin

### Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@waslah.com | admin123 |

---

## Configuration

### config/database.php
```php
// Database connection settings
define('DB_HOST', 'localhost');     // Database host
define('DB_NAME', 'waslah_ecom');   // Database name
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
define('DB_CHARSET', 'utf8mb4');    // Character set
```

### config/config.php
```php
// Site Settings
define('SITE_NAME', 'Waslah');                              // Store name
define('SITE_TAGLINE', 'Fashion for Everyone');             // Tagline
define('SITE_URL', 'http://localhost/WaslahEcomPortal');    // Full URL
define('SITE_EMAIL', 'info@waslah.com');                    // Contact email

// Currency
define('CURRENCY_SYMBOL', '$');     // Currency symbol
define('CURRENCY_CODE', 'USD');     // Currency code

// Pagination
define('PRODUCTS_PER_PAGE', 12);    // Products per page
define('ORDERS_PER_PAGE', 20);      // Orders per page

// Shipping
define('FREE_SHIPPING_THRESHOLD', 100.00);  // Free shipping minimum
define('DEFAULT_SHIPPING_COST', 10.00);     // Default shipping cost

// Tax
define('TAX_RATE', 0.00);           // Tax rate (0 = no tax)

// Image Settings
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024);  // 5MB max
define('THUMB_WIDTH', 300);                  // Thumbnail width
define('THUMB_HEIGHT', 400);                 // Thumbnail height

// Security
define('HASH_COST', 12);            // Password hash cost
define('SESSION_LIFETIME', 7200);   // Session timeout (2 hours)
```

---

## Usage Guide

### Customer Journey

1. **Browse Products**
   - Visit homepage to see featured products
   - Use navigation to browse by category (Men/Women/Children)
   - Use filters to narrow down by price, size, color
   - Search for specific products

2. **Add to Cart**
   - Click product to view details
   - Select size/color if available
   - Choose quantity
   - Click "Add to Cart"

3. **Checkout**
   - View cart and adjust quantities
   - Click "Proceed to Checkout"
   - Enter shipping address
   - Select shipping method
   - Choose payment method
   - Complete order

4. **Track Order**
   - View order confirmation
   - Check order status in "My Orders"
   - Track shipment with tracking number

### Mock Payment Testing

| Gateway | Test Data | Result |
|---------|-----------|--------|
| Credit Card | 4242 4242 4242 4242 | Success |
| Credit Card | 4000 0000 0000 0002 | Declined |
| PayPal | any@email.com | Success |
| COD | N/A | Always Success |

---

## Admin Panel

### Accessing Admin
URL: `http://localhost/WaslahEcomPortal/admin`

### Dashboard
- View total orders, revenue, products, customers
- See today's and monthly statistics
- Monitor pending orders
- Check low stock alerts

### Products Management
**Path**: Admin > Products

- **List Products**: View all with search/filter
- **Add Product**: Create new with images, variants
- **Edit Product**: Update details, pricing, stock
- **Delete Product**: Remove from catalog

**Product Fields**:
- Basic: Name, slug, descriptions
- Pricing: Regular price, sale price, cost
- Inventory: SKU, stock quantity, low stock threshold
- Variants: Sizes (XS-XXL), Colors
- SEO: Meta title, meta description
- Images: Primary + additional images

### Orders Management
**Path**: Admin > Orders

- View all orders with status filters
- Update order status (Pending → Processing → Shipped → Delivered)
- View order details and items
- Generate printable invoices
- Track shipment status

**Order Statuses**:
| Status | Description |
|--------|-------------|
| Pending | New order, awaiting processing |
| Processing | Order being prepared |
| Shipped | Order dispatched |
| Delivered | Order received by customer |
| Cancelled | Order cancelled |
| Refunded | Payment refunded |

### Categories Management
**Path**: Admin > Categories

- Create parent categories (Men, Women, Children)
- Create subcategories (T-Shirts, Dresses, etc.)
- Assign images and icons
- Set display order

### Multi-Store Management
**Path**: Admin > Stores

- Create multiple stores
- Each store has separate:
  - Products
  - Orders
  - Categories
  - Settings
- Switch between stores using sidebar selector
- Clone categories when creating new store

### Courier Management
**Path**: Admin > Couriers

- Configure shipping methods
- Set base rates and per-kg rates
- Define estimated delivery times
- Enable/disable methods

---

## File Structure

```
WaslahEcomPortal/
│
├── config/                     # Configuration files
│   ├── config.php             # Application settings
│   ├── database.php           # Database credentials
│   └── routes.php             # URL routing definitions
│
├── controllers/               # Request handlers
│   ├── HomeController.php     # Homepage
│   ├── ProductController.php  # Product pages
│   ├── CartController.php     # Shopping cart
│   ├── CheckoutController.php # Checkout process
│   ├── UserController.php     # User account
│   └── admin/                 # Admin controllers
│       ├── AdminDashboardController.php
│       ├── AdminProductController.php
│       ├── AdminOrderController.php
│       └── AdminStoreController.php
│
├── core/                      # Framework core
│   ├── Auth.php              # Authentication
│   ├── Controller.php        # Base controller
│   ├── Database.php          # PDO wrapper
│   ├── Model.php             # Base model
│   ├── Router.php            # URL routing
│   └── Session.php           # Session management
│
├── database/                  # Database files
│   ├── schema.sql            # Table definitions
│   └── sample_data.sql       # Demo products
│
├── includes/                  # Utilities
│   ├── helpers.php           # Helper functions
│   └── mock/                 # Mock services
│       ├── PaymentGateway.php
│       └── CourierService.php
│
├── models/                    # Data models
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Cart.php
│   ├── Order.php
│   ├── Store.php
│   ├── Courier.php
│   └── Payment.php
│
├── public/                    # Static assets
│   ├── css/
│   │   ├── style.css         # Frontend styles
│   │   └── admin.css         # Admin styles
│   ├── js/
│   │   ├── main.js           # Frontend scripts
│   │   └── admin.js          # Admin scripts
│   └── images/               # Static images
│
├── uploads/                   # User uploads
│   ├── products/             # Product images
│   ├── stores/               # Store logos
│   └── banners/              # Banner images
│
├── views/                     # Templates
│   ├── layouts/
│   │   ├── main.php          # Frontend layout
│   │   └── admin.php         # Admin layout
│   ├── home/                 # Homepage views
│   ├── products/             # Product views
│   ├── cart/                 # Cart views
│   ├── checkout/             # Checkout views
│   ├── user/                 # User account views
│   ├── admin/                # Admin views
│   ├── errors/               # Error pages
│   └── partials/             # Reusable components
│
├── logs/                      # Application logs
├── .htaccess                  # Apache config
├── index.php                  # Entry point
├── README.md                  # This file
└── DEPLOYMENT.md             # Hosting guide
```

---

## Database Schema

### Tables Overview

| Table | Description |
|-------|-------------|
| stores | Multi-store configuration |
| users | Customer and admin accounts |
| user_addresses | Saved shipping addresses |
| categories | Product categories |
| products | Product catalog |
| product_images | Product image gallery |
| product_variants | Size/color variants |
| cart | Shopping carts |
| cart_items | Cart line items |
| orders | Order records |
| order_items | Order line items |
| payments | Payment transactions |
| couriers | Shipping methods |
| shipments | Shipment records |
| shipment_tracking | Tracking history |
| wishlist | User wishlists |
| reviews | Product reviews |
| coupons | Discount codes |
| settings | Store settings |
| banners | Homepage banners |

### Entity Relationships

```
stores
  └── products (one-to-many)
  └── categories (one-to-many)
  └── orders (one-to-many)
  └── users (one-to-many)

products
  └── product_images (one-to-many)
  └── product_variants (one-to-many)
  └── cart_items (one-to-many)

orders
  └── order_items (one-to-many)
  └── payments (one-to-many)
  └── shipments (one-to-one)

users
  └── user_addresses (one-to-many)
  └── orders (one-to-many)
  └── wishlist (one-to-many)
```

---

## API Reference

### Cart API (AJAX)

**Add to Cart**
```
POST /cart/add
Body: product_id, quantity, variant_id (optional)
Response: { success: true, cartCount: 5 }
```

**Update Cart**
```
POST /cart/update
Body: item_id, quantity
Response: { success: true }
```

**Remove from Cart**
```
POST /cart/remove
Body: item_id
Response: { success: true }
```

**Get Cart Count**
```
GET /api/cart/count
Response: { count: 5 }
```

### Wishlist API (AJAX)

**Toggle Wishlist**
```
POST /wishlist/add
Body: product_id
Response: { success: true, action: "added"|"removed" }
```

### Admin API (AJAX)

**Update Order Status**
```
POST /admin/orders/status/{id}
Body: status
Response: { success: true }
```

---

## Customization

### Changing Colors

Edit `public/css/style.css`:
```css
:root {
    --primary-color: #1a1a2e;    /* Dark navy */
    --secondary-color: #16213e;   /* Darker navy */
    --accent-color: #e94560;      /* Coral red */
    --text-color: #333;
    --bg-light: #f8f9fa;
}
```

### Adding New Category

1. Admin > Categories > Add Category
2. Or via SQL:
```sql
INSERT INTO categories (store_id, name, slug, status)
VALUES (1, 'Accessories', 'accessories', 1);
```

### Adding Payment Gateway

1. Create new class in `includes/mock/`:
```php
// includes/mock/RazorpayGateway.php
class RazorpayGateway extends PaymentGateway {
    // Implementation
}
```

2. Add to `PaymentGateway::getAvailableMethods()`

### Adding Courier Service

Admin > Couriers > Add Courier

Or via SQL:
```sql
INSERT INTO couriers (store_id, name, code, base_rate, estimated_days, status)
VALUES (1, 'DHL Express', 'dhl', 25.00, '1-2 days', 1);
```

---

## Troubleshooting

### Common Issues

**1. 500 Internal Server Error**
```
Solution:
- Check Apache error log
- Verify mod_rewrite is enabled
- Check .htaccess syntax
- Ensure PHP 8.0+
```

**2. Database Connection Failed**
```
Solution:
- Verify credentials in config/database.php
- Check MySQL is running
- Confirm database exists
```

**3. 404 on All Pages**
```
Solution:
- Enable mod_rewrite: a2enmod rewrite
- Allow .htaccess: AllowOverride All
- Restart Apache
```

**4. Images Not Uploading**
```
Solution:
- Check uploads/ permissions (755 or 777)
- Verify PHP upload_max_filesize
- Check MAX_IMAGE_SIZE in config
```

**5. CSS/JS Not Loading**
```
Solution:
- Check SITE_URL in config
- Verify public/ folder exists
- Check browser console for errors
```

**6. Admin Login Not Working**
```
Solution:
- Reset password via SQL:
UPDATE users SET password = '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4.PXHxMZNGjOxkWy'
WHERE email = 'admin@waslah.com';
(Password becomes: admin123)
```

### Enable Debug Mode

Edit `index.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check Logs

View `logs/YYYY-MM-DD.log` for application errors.

---

## Security

### Implemented Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Password Hashing**: Bcrypt with cost factor 12
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Prevention**: Output sanitization with htmlspecialchars
- **Session Security**: Regeneration on login
- **File Upload Validation**: Type and size checking
- **Admin Authentication**: Role-based access control

### Production Recommendations

1. **Use HTTPS**: Enable SSL certificate
2. **Disable Errors**: Set display_errors = 0
3. **Secure .htaccess**: Block sensitive files
4. **Update Regularly**: Keep PHP/MySQL updated
5. **Strong Passwords**: Enforce for admin accounts
6. **Backup Database**: Regular automated backups
7. **Monitor Logs**: Check for suspicious activity

---

## License

This project is provided for educational and commercial use.

---

## Support

For issues or customization requests, review the troubleshooting section above.

---

**Built with PHP 8.0+ | MySQL 5.7+ | Bootstrap 5 | Font Awesome 6**
