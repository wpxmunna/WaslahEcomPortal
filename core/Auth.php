<?php
/**
 * Authentication Class
 */

class Auth
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Check if user is logged in (static) - for frontend customer
     */
    public static function check(): bool
    {
        return Session::isLoggedIn();
    }

    /**
     * Check if admin is logged in (static)
     */
    public static function isAdmin(): bool
    {
        return Session::isAdmin();
    }

    /**
     * Get current user (static) - frontend customer
     */
    public static function user(): ?array
    {
        return Session::getUser();
    }

    /**
     * Get current user ID (static) - frontend customer
     */
    public static function id(): ?int
    {
        $user = Session::getUser();
        return $user ? (int)$user['id'] : null;
    }

    /**
     * Get current admin user (static)
     */
    public static function admin(): ?array
    {
        return Session::getAdmin();
    }

    /**
     * Get current admin ID (static)
     */
    public static function adminId(): ?int
    {
        $admin = Session::getAdmin();
        return $admin ? (int)$admin['id'] : null;
    }

    /**
     * Attempt to login user (frontend customer)
     */
    public function attempt(string $email, string $password): bool
    {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 1",
            [$email]
        );

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Update last login
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        // Remove password from session data
        unset($user['password']);

        Session::setUser($user);
        Session::regenerate();

        return true;
    }

    /**
     * Attempt to login admin
     */
    public function attemptAdmin(string $email, string $password): bool
    {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 1 AND role IN ('admin', 'manager')",
            [$email]
        );

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Update last login
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        // Remove password from session data
        unset($user['password']);

        Session::setAdmin($user);
        Session::regenerate();

        return true;
    }

    /**
     * Register new user
     */
    public function register(array $data): int|false
    {
        // Check if email exists
        $existing = $this->db->fetch(
            "SELECT id FROM users WHERE email = ?",
            [$data['email']]
        );

        if ($existing) {
            return false;
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        $data['role'] = 'customer';
        $data['status'] = 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('users', $data);
    }

    /**
     * Logout user (frontend customer)
     */
    public function logout(): void
    {
        Session::logoutUser();
    }

    /**
     * Logout admin
     */
    public function logoutAdmin(): void
    {
        Session::logoutAdmin();
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email): bool
    {
        $result = $this->db->fetch(
            "SELECT id FROM users WHERE email = ?",
            [$email]
        );
        return $result !== null;
    }

    /**
     * Update password
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        return $this->db->update('users', ['password' => $hash], 'id = ?', [$userId]) > 0;
    }

    /**
     * Verify current password
     */
    public function verifyPassword(int $userId, string $password): bool
    {
        $user = $this->db->fetch(
            "SELECT password FROM users WHERE id = ?",
            [$userId]
        );

        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    /**
     * Create admin user
     */
    public function createAdmin(string $name, string $email, string $password): int
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT, ['cost' => HASH_COST]),
            'role' => 'admin',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('users', $data);
    }
}
