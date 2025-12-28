@echo off
title Waslah E-Commerce - Local Setup
color 0A

echo ==========================================
echo   WASLAH E-COMMERCE LOCAL SETUP
echo ==========================================
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] Running without admin privileges
    echo.
)

:: Display setup instructions
echo This script will help you set up Waslah E-Commerce locally.
echo.
echo REQUIREMENTS:
echo  - XAMPP, WAMP, or Laragon installed
echo  - Apache and MySQL running
echo.
pause

echo.
echo ==========================================
echo   STEP 1: CHECKING FOLDERS
echo ==========================================

:: Create required directories if they don't exist
if not exist "uploads\products" mkdir "uploads\products"
if not exist "uploads\stores" mkdir "uploads\stores"
if not exist "uploads\banners" mkdir "uploads\banners"
if not exist "logs" mkdir "logs"

echo [OK] Upload directories created/verified
echo [OK] Logs directory created/verified
echo.

echo ==========================================
echo   STEP 2: CONFIGURATION
echo ==========================================
echo.
echo Current configuration:
echo  - Database Host: localhost
echo  - Database Name: waslah_ecom
echo  - Database User: root
echo  - Database Pass: (empty)
echo.
echo If you need to change these, edit:
echo  config/database.php
echo.

echo ==========================================
echo   STEP 3: DATABASE SETUP
echo ==========================================
echo.
echo Please complete these steps manually:
echo.
echo  1. Open phpMyAdmin: http://localhost/phpmyadmin
echo.
echo  2. Create database named: waslah_ecom
echo     - Click "New" on left sidebar
echo     - Enter name: waslah_ecom
echo     - Select: utf8mb4_unicode_ci
echo     - Click "Create"
echo.
echo  3. Import schema:
echo     - Select waslah_ecom database
echo     - Click "Import" tab
echo     - Choose file: database\schema.sql
echo     - Click "Go"
echo.
echo  4. Import sample data (optional):
echo     - Click "Import" tab again
echo     - Choose file: database\sample_data.sql
echo     - Click "Go"
echo.
pause

echo.
echo ==========================================
echo   STEP 4: ACCESS INFORMATION
echo ==========================================
echo.
echo After database setup, access your store at:
echo.
echo  FRONTEND:
echo  http://localhost/WaslahEcomPortal
echo.
echo  ADMIN PANEL:
echo  http://localhost/WaslahEcomPortal/admin
echo.
echo  LOGIN CREDENTIALS:
echo  Email: admin@waslah.com
echo  Password: admin123
echo.
echo ==========================================
echo   SETUP COMPLETE!
echo ==========================================
echo.
echo Press any key to open the store in your browser...
pause >nul

start http://localhost/WaslahEcomPortal

echo.
echo Thank you for using Waslah E-Commerce!
echo.
pause
