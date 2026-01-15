# Waslah E-Commerce Deployment Guide

## Production Domain: https://waslah.gt.tc

---

## Step 1: Prepare Database Configuration

Before uploading, you need your InfinityFree MySQL credentials:

1. Login to your InfinityFree control panel
2. Go to **MySQL Databases**
3. Note down these details:
   - MySQL Hostname (e.g., sql###.infinityfree.com)
   - Database Name (e.g., if0_40910353_waslah)
   - Database Username (e.g., if0_40910353)
   - Database Password

4. Update config/database.php with your credentials

---

## Step 2: Upload Files via FTP

1. Download FileZilla: https://filezilla-project.org/
2. Connect to FTP:
   - Host: ftpupload.net
   - Username: if0_40910353 (from control panel)
   - Password: (from control panel)
   - Port: 21

3. Upload ALL files from E:\WaslahEcomPortal\ to /htdocs/ on server
4. Set uploads folder permissions to 755

---

## Step 3: Import Database

Export from localhost phpMyAdmin, then import to InfinityFree phpMyAdmin

---

## Checklist:
- [x] SITE_URL updated to https://waslah.gt.tc
- [ ] Database credentials updated in config/database.php
- [ ] All files uploaded to htdocs
- [ ] Database imported
- [ ] uploads folder permissions set

Your site: https://waslah.gt.tc
