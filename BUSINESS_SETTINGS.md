# Business Settings Management

## Overview
Dynamic business information management system that allows you to manage all your business links, credentials, and contact information from the admin panel without editing code.

## Features
- **Social Media Links**: Facebook, Instagram, YouTube, LinkedIn, Twitter/X, TikTok
- **Contact Information**: WhatsApp, Email, Phone, Business Address
- **Shipping & Logistics**: Pathao, SteadFast courier URLs
- **Domain & Hosting**: NameCheap, WordPress admin URLs
- **Quick Access Links**: Direct links to all configured URLs

## Installation

### 1. Run Database Migration
```bash
mysql -u your_user -p your_database < database/business_info_migration.sql
```

Or import via phpMyAdmin/MySQL client.

### 2. Access Business Settings
Navigate to: **Admin > Settings > Business Info**
URL: `http://localhost:8000/admin/settings/business`

## Usage

### Update Business Information
1. Go to **Admin Panel > Settings**
2. Click on **Business Info** card
3. Update any fields:
   - Social Media URLs
   - Contact information
   - Shipping URLs
   - Domain links
4. Click **Save Business Information**

### Available Settings Groups

#### General Information
- Website URL
- WordPress Admin URL

#### Contact Information
- WhatsApp Number
- WhatsApp Link
- Business Email
- Support Email
- Business Phone
- Business Address

#### Social Media Links
- Facebook Page URL
- Instagram URL
- YouTube Channel
- LinkedIn
- Twitter/X
- TikTok

#### Shipping & Logistics
- Pathao Login URL
- SteadFast URL

#### Domain & Hosting
- NameCheap URL

## Using Settings in Code

### Get All Settings
```php
$businessSettings = new BusinessSetting();
$storeId = Session::get('admin_store_id', 1);
$settings = $businessSettings->getByStore($storeId);

// Access settings
$facebookUrl = $settings['facebook_page_url'] ?? '';
$whatsappNumber = $settings['whatsapp_number'] ?? '';
```

### Get Single Setting
```php
$businessSettings = new BusinessSetting();
$storeId = 1;
$email = $businessSettings->getSetting($storeId, 'business_email', 'default@email.com');
```

### Get Settings by Group
```php
$socialLinks = $businessSettings->getByGroup($storeId, 'social');
$contactInfo = $businessSettings->getByGroup($storeId, 'contact');
```

### Update Setting Programmatically
```php
$businessSettings->setSetting(
    $storeId,
    'facebook_page_url',
    'https://facebook.com/newpage',
    'social',
    'Facebook Page URL'
);
```

## Pre-configured Data

The following settings are pre-configured for Waslah:
- Facebook Page: https://facebook.com/waslahbd
- Website: www.waslahbd.com
- WordPress Admin: https://www.waslahbd.com/wp-admin/
- WhatsApp: 8801755853091
- WhatsApp Link: https://wa.me/8801755853091
- Business Email: waslahbd@gmail.com
- Pathao Login: https://merchant.pathao.com/login
- SteadFast: https://steadfast.com.bd/

## Integration with Social Media Manager

The business settings work alongside the Social Media Manager (`admin/social-media`) to provide:
- Dynamic social links in website footer
- Contact information display
- Quick access to business tools

## Where Business Settings Are Used

The business settings are automatically integrated throughout the application:

### Frontend (Customer-Facing)
- **Header Contact Bar** (`views/layouts/main.php:40-74`): Displays business phone, email, and social icons (Facebook, WhatsApp)
- **Footer Contact Section** (`views/layouts/main.php:240-278`): Shows business email, phone, WhatsApp, and address
- **Footer Social Links** (`views/layouts/main.php:211-226`): Facebook and WhatsApp icons with brand colors

### Admin Panel
- **Dashboard Quick Links** (`views/admin/dashboard.php:167-204`): Direct access buttons to:
  - Facebook Page
  - WhatsApp
  - Pathao Login
  - SteadFast
- **Settings Page** (`admin/settings/business`): Centralized management interface

## Security Notes

- All URLs are validated on input
- Sensitive information should use `is_encrypted` flag (future feature)
- Only full admins can access business settings
- CSRF protection on all updates

## API Endpoints

### View Settings
`GET /admin/settings/business`

### Update Settings
`POST /admin/settings/update-business`
Requires: CSRF token + form data

## Support

For issues or questions, contact the development team.
