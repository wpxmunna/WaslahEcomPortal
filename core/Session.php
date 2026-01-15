<?php
/**
 * Session Management Class
 */

class Session
{
    /**
     * Set session value
     */
    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     */
    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy session
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Set flash message
     */
    public static function setFlash(string $message, string $type = 'success'): void
    {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * Get and clear flash message
     */
    public static function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Check if user is logged in (frontend customer)
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if admin is logged in
     */
    public static function isAdmin(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    /**
     * Get current user ID (frontend customer)
     */
    public static function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user data (frontend customer)
     */
    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Set user session (frontend customer)
     */
    public static function setUser(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
    }

    /**
     * Get current admin ID
     */
    public static function getAdminId(): ?int
    {
        return $_SESSION['admin_id'] ?? null;
    }

    /**
     * Get current admin data
     */
    public static function getAdmin(): ?array
    {
        return $_SESSION['admin_user'] ?? null;
    }

    /**
     * Set admin session
     */
    public static function setAdmin(array $user): void
    {
        // Only allow admin and manager roles
        if (!in_array($user['role'] ?? '', ['admin', 'manager'])) {
            return;
        }

        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_user'] = $user;
        $_SESSION['admin_role'] = $user['role'];
        $_SESSION['admin_store_id'] = $user['store_id'] ?? 1;
    }

    /**
     * Logout user (frontend customer)
     */
    public static function logoutUser(): void
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);
    }

    /**
     * Logout admin
     */
    public static function logoutAdmin(): void
    {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_user']);
        unset($_SESSION['admin_role']);
        unset($_SESSION['admin_store_id']);
    }

    /**
     * Generate CSRF token
     */
    public static function getCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrf(?string $token): bool
    {
        if (!$token || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Regenerate session ID
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}
