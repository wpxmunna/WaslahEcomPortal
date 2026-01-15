<?php
/**
 * Waslah E-Commerce Portal
 * Main Entry Point
 */

// Start output buffering first to prevent "headers already sent" errors
ob_start();

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disabled for production
ini_set('log_errors', 1);

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/core/',
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/controllers/admin/',
        ROOT_PATH . '/includes/',
        ROOT_PATH . '/includes/mock/',
        ROOT_PATH . '/includes/services/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load helpers
require_once ROOT_PATH . '/includes/helpers.php';

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Initialize router
$router = new Router();

// Define routes
require_once ROOT_PATH . '/config/routes.php';

// Get URL
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

// Dispatch request
$router->dispatch($url);

// Flush output buffer
ob_end_flush();
