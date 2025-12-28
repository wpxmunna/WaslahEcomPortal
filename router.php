<?php
/**
 * Router for PHP Built-in Development Server
 *
 * Usage: php -S localhost:8000 router.php
 *
 * This file handles URL routing when using PHP's built-in server,
 * which doesn't support .htaccess files.
 */

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files from /public path (CSS, JS, images)
if (preg_match('/^\/public\//', $uri)) {
    $publicPath = __DIR__ . $uri;
    if (file_exists($publicPath) && is_file($publicPath)) {
        return false;
    }
}

// Serve uploaded files
if (preg_match('/^\/uploads\//', $uri)) {
    $uploadPath = __DIR__ . $uri;
    if (file_exists($uploadPath) && is_file($uploadPath)) {
        return false;
    }
}

// For all other requests, route through index.php
$_GET['url'] = ltrim($uri, '/');
require __DIR__ . '/index.php';
