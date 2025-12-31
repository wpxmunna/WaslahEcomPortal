<?php
/**
 * User Model
 */

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'store_id', 'name', 'email', 'password', 'phone',
        'avatar', 'role', 'status'
    ];

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    /**
     * Get user with addresses
     */
    public function getWithAddresses(int $userId): ?array
    {
        $user = $this->find($userId);
        if ($user) {
            $user['addresses'] = $this->db->fetchAll(
                "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC",
                [$userId]
            );
        }
        return $user;
    }

    /**
     * Get user orders
     */
    public function getOrders(int $userId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    /**
     * Add address
     */
    public function addAddress(int $userId, array $data): int
    {
        $data['user_id'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // If default, unset other defaults
        if (!empty($data['is_default'])) {
            $this->db->update('user_addresses', ['is_default' => 0], 'user_id = ?', [$userId]);
        }

        return $this->db->insert('user_addresses', $data);
    }

    /**
     * Get customers (non-admin users)
     */
    public function getCustomers(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "role = 'customer'", [], 'created_at DESC');
    }

    /**
     * Get customer stats
     */
    public function getCustomerStats(int $userId): array
    {
        $stats = $this->db->fetch(
            "SELECT
                COUNT(*) as total_orders,
                SUM(total_amount) as total_spent,
                AVG(total_amount) as avg_order_value
             FROM orders WHERE user_id = ?",
            [$userId]
        );
        return $stats ?: ['total_orders' => 0, 'total_spent' => 0, 'avg_order_value' => 0];
    }

    /**
     * Get admin/manager users
     */
    public function getAdminUsers(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "role IN ('admin', 'manager')", [], 'created_at DESC');
    }

    /**
     * Create admin user
     */
    public function createAdmin(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update admin user
     */
    public function updateAdmin(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    /**
     * Check if email exists (excluding a user)
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        return (bool) $this->db->fetch($sql, $params);
    }
}
