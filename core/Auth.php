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
     * Check if user is logged in (static)
     */
    public static function check(): bool
    {
        return Session::isLoggedIn();
    }

    /**
     * Check if logged in user is admin (static)
     */
    public static function isAdmin(): bool
    {
        $user = Session::getUser();
        return $user && in_array($user['role'] ?? '', ['admin', 'manager']);
    }

    /**
     * Get current user (static)
     */
    public static function user(): ?array
    {
        return Session::getUser();
    }

    /**
     * Get current user ID (static)
     */
    public static function id(): ?int
    {
        $user = Session::getUser();
        return $user ? (int)$user['id'] : null;
    }

    /**
     * Attempt to login user
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
     * Logout user
     */
    public function logout(): void
    {
        Session::destroy();
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
