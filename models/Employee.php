<?php
/**
 * Employee Model
 */

class Employee extends Model
{
    protected string $table = 'employees';
    protected array $fillable = [
        'store_id', 'user_id', 'employee_id', 'first_name', 'last_name',
        'email', 'phone', 'date_of_birth', 'gender', 'national_id',
        'address', 'city', 'department_id', 'designation', 'employment_type',
        'hire_date', 'termination_date', 'basic_salary', 'bank_name',
        'bank_account', 'mobile_banking', 'emergency_contact_name',
        'emergency_contact_phone', 'photo', 'status', 'notes'
    ];

    /**
     * Generate unique employee ID
     */
    public function generateEmployeeId(int $storeId): string
    {
        $count = $this->db->fetch(
            "SELECT COUNT(*) as count FROM employees WHERE store_id = ?",
            [$storeId]
        );
        $num = ($count['count'] ?? 0) + 1;
        return 'EMP' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get employees for admin listing
     */
    public function getAdminEmployees(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 'e.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['status'])) {
            $where .= ' AND e.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['department_id'])) {
            $where .= ' AND e.department_id = ?';
            $params[] = $filters['department_id'];
        }

        if (!empty($filters['search'])) {
            $where .= ' AND (e.first_name LIKE ? OR e.last_name LIKE ? OR e.employee_id LIKE ? OR e.phone LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM employees e WHERE {$where}",
            $params
        );
        $total = $countResult['total'];
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $employees = $this->db->fetchAll(
            "SELECT e.*, d.name as department_name
             FROM employees e
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE {$where}
             ORDER BY e.first_name, e.last_name
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $employees,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get active employees for dropdown
     */
    public function getActive(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT id, employee_id, first_name, last_name, designation
             FROM employees
             WHERE store_id = ? AND status = 'active'
             ORDER BY first_name, last_name",
            [$storeId]
        );
    }

    /**
     * Get employee with full details
     */
    public function getWithDetails(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT e.*, d.name as department_name, u.name as user_name
             FROM employees e
             LEFT JOIN departments d ON e.department_id = d.id
             LEFT JOIN users u ON e.user_id = u.id
             WHERE e.id = ?",
            [$id]
        );
    }

    /**
     * Create employee
     */
    public function createEmployee(array $data): int
    {
        if (empty($data['employee_id'])) {
            $data['employee_id'] = $this->generateEmployeeId($data['store_id']);
        }
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update employee
     */
    public function updateEmployee(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update($this->table, $data, 'id = ?', [$id]);
        return true;
    }

    /**
     * Get departments
     */
    public function getDepartments(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT d.*, COUNT(e.id) as employee_count
             FROM departments d
             LEFT JOIN employees e ON d.id = e.department_id AND e.status = 'active'
             WHERE d.store_id = ?
             GROUP BY d.id
             ORDER BY d.name",
            [$storeId]
        );
    }

    /**
     * Create department
     */
    public function createDepartment(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('departments', $data);
    }

    /**
     * Get leave types
     */
    public function getLeaveTypes(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM leave_types WHERE store_id = ? AND is_active = 1",
            [$storeId]
        );
    }

    /**
     * Get employee stats
     */
    public function getStats(int $storeId): array
    {
        return $this->db->fetch(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as on_leave,
                SUM(CASE WHEN employment_type = 'full_time' THEN 1 ELSE 0 END) as full_time,
                SUM(CASE WHEN employment_type = 'part_time' THEN 1 ELSE 0 END) as part_time
             FROM employees WHERE store_id = ?",
            [$storeId]
        ) ?: ['total' => 0, 'active' => 0, 'on_leave' => 0, 'full_time' => 0, 'part_time' => 0];
    }
}
