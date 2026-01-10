<?php
/**
 * Attendance Model
 */

class Attendance extends Model
{
    protected string $table = 'attendance';

    /**
     * Record check-in
     */
    public function checkIn(int $employeeId, int $storeId): bool
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        // Check if already checked in
        $existing = $this->db->fetch(
            "SELECT * FROM attendance WHERE employee_id = ? AND attendance_date = ?",
            [$employeeId, $today]
        );

        if ($existing) {
            return false;
        }

        // Determine status (late if after 9:30 AM for example)
        $lateTime = '09:30:00';
        $status = ($now > $lateTime) ? 'late' : 'present';

        $this->db->insert($this->table, [
            'store_id' => $storeId,
            'employee_id' => $employeeId,
            'attendance_date' => $today,
            'check_in' => $now,
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Record check-out
     */
    public function checkOut(int $employeeId): bool
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        $attendance = $this->db->fetch(
            "SELECT * FROM attendance WHERE employee_id = ? AND attendance_date = ?",
            [$employeeId, $today]
        );

        if (!$attendance || $attendance['check_out']) {
            return false;
        }

        // Calculate work hours
        $checkIn = new DateTime($attendance['check_in']);
        $checkOut = new DateTime($now);
        $diff = $checkIn->diff($checkOut);
        $workHours = $diff->h + ($diff->i / 60);

        // Overtime if more than 8 hours
        $overtimeHours = max(0, $workHours - 8);

        $this->db->update($this->table, [
            'check_out' => $now,
            'work_hours' => round($workHours, 2),
            'overtime_hours' => round($overtimeHours, 2)
        ], 'id = ?', [$attendance['id']]);

        return true;
    }

    /**
     * Get attendance records
     */
    public function getAttendance(int $storeId, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = 'a.store_id = ?';
        $params = [$storeId];

        if (!empty($filters['employee_id'])) {
            $where .= ' AND a.employee_id = ?';
            $params[] = $filters['employee_id'];
        }

        if (!empty($filters['date'])) {
            $where .= ' AND a.attendance_date = ?';
            $params[] = $filters['date'];
        }

        if (!empty($filters['month'])) {
            $where .= ' AND DATE_FORMAT(a.attendance_date, "%Y-%m") = ?';
            $params[] = $filters['month'];
        }

        if (!empty($filters['status'])) {
            $where .= ' AND a.status = ?';
            $params[] = $filters['status'];
        }

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM attendance a WHERE {$where}",
            $params
        );
        $total = $countResult['total'];
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $records = $this->db->fetchAll(
            "SELECT a.*, e.employee_id as emp_code, e.first_name, e.last_name
             FROM attendance a
             JOIN employees e ON a.employee_id = e.id
             WHERE {$where}
             ORDER BY a.attendance_date DESC, e.first_name
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $records,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get today's attendance summary
     */
    public function getTodaySummary(int $storeId): array
    {
        $today = date('Y-m-d');

        $summary = $this->db->fetch(
            "SELECT
                COUNT(CASE WHEN status = 'present' THEN 1 END) as present,
                COUNT(CASE WHEN status = 'late' THEN 1 END) as late,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
                COUNT(CASE WHEN status = 'leave' THEN 1 END) as on_leave
             FROM attendance
             WHERE store_id = ? AND attendance_date = ?",
            [$storeId, $today]
        );

        $totalEmployees = $this->db->fetch(
            "SELECT COUNT(*) as total FROM employees WHERE store_id = ? AND status = 'active'",
            [$storeId]
        );

        $summary['total_employees'] = $totalEmployees['total'] ?? 0;
        $summary['not_checked_in'] = $summary['total_employees'] - ($summary['present'] ?? 0) - ($summary['late'] ?? 0) - ($summary['on_leave'] ?? 0);

        return $summary;
    }

    /**
     * Get employee attendance for a month
     */
    public function getMonthlyAttendance(int $employeeId, string $month): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM attendance
             WHERE employee_id = ? AND DATE_FORMAT(attendance_date, '%Y-%m') = ?
             ORDER BY attendance_date",
            [$employeeId, $month]
        );
    }

    /**
     * Mark absent for employees who didn't check in
     */
    public function markAbsent(int $storeId, string $date): int
    {
        // Get employees who have no attendance record for the date
        $missingEmployees = $this->db->fetchAll(
            "SELECT id FROM employees
             WHERE store_id = ? AND status = 'active'
             AND id NOT IN (
                SELECT employee_id FROM attendance WHERE attendance_date = ?
             )
             AND id NOT IN (
                SELECT employee_id FROM leave_requests
                WHERE status = 'approved' AND ? BETWEEN start_date AND end_date
             )",
            [$storeId, $date, $date]
        );

        $count = 0;
        foreach ($missingEmployees as $emp) {
            $this->db->insert($this->table, [
                'store_id' => $storeId,
                'employee_id' => $emp['id'],
                'attendance_date' => $date,
                'status' => 'absent',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Get attendance stats for payroll
     */
    public function getPayrollStats(int $employeeId, string $startDate, string $endDate): array
    {
        return $this->db->fetch(
            "SELECT
                COUNT(CASE WHEN status IN ('present', 'late') THEN 1 END) as present_days,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent_days,
                COUNT(CASE WHEN status = 'half_day' THEN 1 END) as half_days,
                COUNT(CASE WHEN status = 'leave' THEN 1 END) as leave_days,
                COALESCE(SUM(overtime_hours), 0) as total_overtime
             FROM attendance
             WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?",
            [$employeeId, $startDate, $endDate]
        ) ?: ['present_days' => 0, 'absent_days' => 0, 'half_days' => 0, 'leave_days' => 0, 'total_overtime' => 0];
    }
}
