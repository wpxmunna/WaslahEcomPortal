<?php
/**
 * Helper Functions
 */

/**
 * Generate URL
 */
function url(string $path = ''): string
{
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Asset URL
 */
function asset(string $path): string
{
    return SITE_URL . '/public/' . ltrim($path, '/');
}

/**
 * Upload URL
 */
function upload(string $path): string
{
    return SITE_URL . '/uploads/' . ltrim($path, '/');
}

/**
 * Format price
 */
function formatPrice(float $price): string
{
    return CURRENCY_SYMBOL . number_format($price, 2);
}

/**
 * Generate slug
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Sanitize input
 */
function sanitize(?string $input): string
{
    if ($input === null) {
        return '';
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text
 */
function truncate(?string $text, int $length = 100, string $suffix = '...'): string
{
    if ($text === null || strlen($text) <= $length) {
        return $text ?? '';
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Format date
 */
function formatDate(string $date, string $format = 'M d, Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime(string $datetime, string $format = 'M d, Y h:i A'): string
{
    return date($format, strtotime($datetime));
}

/**
 * CSRF field
 */
function csrfField(): string
{
    $token = Session::getCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Old input value
 */
function old(string $key, string $default = ''): string
{
    return $_POST[$key] ?? $default;
}

/**
 * Check if current URL matches
 */
function isActiveUrl(string $path): bool
{
    $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $path = trim($path, '/');
    return $current === $path || strpos($current, $path) === 0;
}

/**
 * Active class helper
 */
function activeClass(string $path, string $class = 'active'): string
{
    $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Special handling for POS section - highlight all POS menu items when in any POS page
    if (strpos($current, 'admin/pos') === 0) {
        $posPages = ['admin/pos', 'admin/pos/terminal', 'admin/pos/transactions', 'admin/pos/shifts'];
        if (in_array($path, $posPages)) {
            return $class;
        }
    }

    return isActiveUrl($path) ? $class : '';
}

/**
 * Generate random string
 */
function randomString(int $length = 16): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate order number
 */
function generateOrderNumber(): string
{
    return 'WAS-' . date('Ymd') . '-' . strtoupper(randomString(6));
}

/**
 * Get status badge class
 */
function statusBadge(string $status): string
{
    $classes = [
        'pending' => 'bg-warning',
        'processing' => 'bg-info',
        'shipped' => 'bg-primary',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
        'refunded' => 'bg-secondary',
        'paid' => 'bg-success',
        'unpaid' => 'bg-danger',
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
    ];
    return $classes[$status] ?? 'bg-secondary';
}

/**
 * Pagination HTML
 */
function pagination(array $paginate, string $baseUrl): string
{
    if ($paginate['total_pages'] <= 1) {
        return '';
    }

    $html = '<nav><ul class="pagination justify-content-center">';

    // Previous
    if ($paginate['current_page'] > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($paginate['current_page'] - 1) . '">&laquo;</a></li>';
    }

    // Pages
    for ($i = 1; $i <= $paginate['total_pages']; $i++) {
        $active = $i === $paginate['current_page'] ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
    }

    // Next
    if ($paginate['current_page'] < $paginate['total_pages']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($paginate['current_page'] + 1) . '">&raquo;</a></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Calculate discount percentage
 */
function discountPercent(float $original, float $sale): int
{
    if ($original <= 0) return 0;
    return round((($original - $sale) / $original) * 100);
}

/**
 * Get file extension
 */
function getExtension(string $filename): string
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Is valid image
 */
function isValidImage(array $file): bool
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if ($file['size'] > MAX_IMAGE_SIZE) {
        return false;
    }
    return in_array($file['type'], ALLOWED_IMAGE_TYPES);
}

/**
 * Debug dump
 */
function dd(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
}

/**
 * Log message
 */
function logMessage(string $message, string $level = 'info'): void
{
    $logFile = ROOT_PATH . '/logs/' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Check if current user is full admin (not manager)
 */
function isFullAdmin(): bool
{
    $user = Session::getUser();
    return ($user['role'] ?? '') === 'admin';
}

/**
 * Require full admin access, redirect managers
 */
function requireFullAdmin(): void
{
    if (!isFullAdmin()) {
        Session::setFlash('Access denied. Administrator privileges required.', 'error');
        redirect('admin');
    }
}
