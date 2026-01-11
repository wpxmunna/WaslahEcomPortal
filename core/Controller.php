<?php
/**
 * Base Controller Class
 */

class Controller
{
    protected Database $db;
    protected array $data = [];

    public function __construct()
    {
        $this->db = new Database();

        // Common data for all views
        $this->data['siteName'] = SITE_NAME;
        $this->data['siteUrl'] = SITE_URL;
        $this->data['currentUrl'] = Router::currentUrl();
        $this->data['isLoggedIn'] = Session::isLoggedIn();
        $this->data['user'] = Session::getUser();
        $this->data['cartCount'] = $this->getCartCount();
        $this->data['categories'] = $this->getCategories();
        $this->data['currentStore'] = $this->getCurrentStore();
        $this->data['flash'] = Session::getFlash();
        $this->data['stores'] = $this->getStores();
        $this->data['socialLinksHeader'] = $this->getSocialLinks('header');
        $this->data['socialLinksFooter'] = $this->getSocialLinks('footer');
    }

    /**
     * Render a view
     */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        $data = array_merge($this->data, $data);
        extract($data);

        $viewFile = VIEW_PATH . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $view) . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: {$view} (looked in: {$viewFile})");
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = VIEW_PATH . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                die("Layout not found: {$layout} (looked in: {$layoutFile})");
            }
        } else {
            echo $content;
        }
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect with flash message
     */
    protected function redirect(string $url, string $message = '', string $type = 'success'): void
    {
        if ($message) {
            Session::setFlash($message, $type);
        }
        Router::redirect($url);
    }

    /**
     * Get POST data
     */
    protected function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): bool
    {
        $token = $this->post('csrf_token');
        return Session::validateCsrf($token);
    }

    /**
     * Require user to be logged in
     */
    protected function requireLogin(): void
    {
        if (!Session::isLoggedIn()) {
            Session::set('redirect_after_login', Router::currentUrl());
            $this->redirect('login', 'Please login to continue', 'warning');
        }
    }

    /**
     * Require admin access
     */
    protected function requireAdmin(): void
    {
        if (!Session::isAdmin()) {
            $this->redirect('admin/login', 'Admin access required', 'error');
        }
    }

    /**
     * Get cart item count
     */
    private function getCartCount(): int
    {
        if (Session::isLoggedIn()) {
            $userId = Session::getUserId();
            $result = $this->db->fetch(
                "SELECT SUM(quantity) as count FROM cart_items ci
                 JOIN cart c ON ci.cart_id = c.id
                 WHERE c.user_id = ?",
                [$userId]
            );
            return (int) ($result['count'] ?? 0);
        } else {
            $cart = Session::get('cart', []);
            return array_sum(array_column($cart, 'quantity'));
        }
    }

    /**
     * Get main categories
     */
    private function getCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id IS NULL AND status = 1 ORDER BY sort_order"
        );
    }

    /**
     * Get current store
     */
    private function getCurrentStore(): ?array
    {
        $storeId = Session::get('current_store_id', 1);
        return $this->db->fetch("SELECT * FROM stores WHERE id = ?", [$storeId]);
    }

    /**
     * Get all active stores (for admin)
     */
    private function getStores(): array
    {
        return $this->db->fetchAll("SELECT * FROM stores WHERE status = 1 ORDER BY name");
    }

    /**
     * Get social media links for header or footer
     */
    private function getSocialLinks(string $location = 'footer'): array
    {
        $storeId = Session::get('current_store_id', 1);
        $column = $location === 'header' ? 'show_in_header' : 'show_in_footer';

        return $this->db->fetchAll(
            "SELECT * FROM social_media
             WHERE store_id = ? AND is_active = 1 AND {$column} = 1
             ORDER BY sort_order",
            [$storeId]
        );
    }

    /**
     * Upload file
     */
    protected function uploadFile(array $file, string $directory = 'products'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > MAX_IMAGE_SIZE) {
            return null;
        }

        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            return null;
        }

        $uploadDir = UPLOAD_PATH . '/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $directory . '/' . $filename;
        }

        return null;
    }
}
