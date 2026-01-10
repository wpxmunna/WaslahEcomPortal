<?php
/**
 * Payroll Model
 */

class Payroll extends Model
{
    protected string $table = 'payroll_periods';

    /**
     * Get salary components
     */
    public function getSalaryComponents(int $storeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM salary_components WHERE store_id = ? AND is_active = 1 ORDER BY type, name",
            [$storeId]
        );
    }

    /**
     * Get employee salary structure
     */
    public function getEmployeeSalaryStructure(int $employeeId): array
    {
        return $this->db->fetchAll(
            "SELECT ess.*, sc.name, sc.type, sc.calculation_type
             FROM employee_salary_structure ess
             JOIN salary_components sc ON ess.component_id = sc.id
             WHERE ess.employee_id = ?
             AND (ess.effective_to IS NULL OR ess.effective_to >= CURDATE())
             AND ess.effective_from <= CURDATE()",
            [$employeeId]
        );
    }

    /**
     * Set employee salary component
     */
    public function setEmployeeSalaryComponent(int $employeeId, int $componentId, float $amount, string $effectiveFrom): int
    {
        // Close previous record
        $this->db->update('employee_salary_structure',
            ['effective_to' => date('Y-m-d', strtotime($effectiveFrom . ' -1 day'))],
            'employee_id = ? AND component_id = ? AND effective_to IS NULL',
            [$employeeId, $componentId]
        );

        return $this->db->insert('employee_salary_structure', [
            'employee_id' => $employeeId,
            'component_id' => $componentId,
            'amount' => $amount,
            'effective_from' => $effectiveFrom,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get payroll periods
     */
    public function getPayrollPeriods(int $storeId, int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;

        $periods = $this->db->fetchAll(
            "SELECT pp.*, u1.name as processed_by_name, u2.name as approved_by_name
             FROM payroll_periods pp
             LEFT JOIN users u1 ON pp.processed_by = u1.id
             LEFT JOIN users u2 ON pp.approved_by = u2.id
             WHERE pp.store_id = ?
             ORDER BY pp.start_date DESC
             LIMIT {$perPage} OFFSET {$offset}",
            [$storeId]
        );

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM payroll_periods WHERE store_id = ?",
            [$storeId]
        );

        return [
            'data' => $periods,
            'total' => $countResult['total'] ?? 0
        ];
    }

    /**
     * Create payroll period
     */
    public function createPayrollPeriod(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Process payroll for a period
     */
    public function processPayroll(int $periodId, int $storeId, int $processedBy): bool
    {
        $period = $this->find($periodId);
        if (!$period || $period['status'] !== 'draft') {
            return false;
        }

        // Get all active employees
        $employees = $this->db->fetchAll(
            "SELECT * FROM employees WHERE store_id = ? AND status = 'active'",
            [$storeId]
        );

        $attendance = new Attendance();
        $workingDays = $this->getWorkingDays($period['start_date'], $period['end_date']);

        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;
        $employeeCount = 0;

        foreach ($employees as $employee) {
            // Get attendance stats
            $attendanceStats = $attendance->getPayrollStats(
                $employee['id'],
                $period['start_date'],
                $period['end_date']
            );

            // Calculate salary
            $salaryData = $this->calculateEmployeeSalary($employee, $attendanceStats, $workingDays);

            // Insert payroll detail
            $detailId = $this->db->insert('payroll_details', [
                'payroll_period_id' => $periodId,
                'employee_id' => $employee['id'],
                'basic_salary' => $employee['basic_salary'],
                'working_days' => $workingDays,
                'present_days' => $attendanceStats['present_days'],
                'absent_days' => $attendanceStats['absent_days'],
                'leave_days' => $attendanceStats['leave_days'],
                'overtime_hours' => $attendanceStats['total_overtime'],
                'overtime_amount' => $salaryData['overtime_amount'],
                'gross_earnings' => $salaryData['gross_earnings'],
                'total_deductions' => $salaryData['total_deductions'],
                'net_salary' => $salaryData['net_salary'],
                'payment_method' => $employee['bank_account'] ? 'bank' : ($employee['mobile_banking'] ? 'mobile_banking' : 'cash'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Insert component breakdown
            foreach ($salaryData['components'] as $component) {
                $this->db->insert('payroll_detail_components', [
                    'payroll_detail_id' => $detailId,
                    'component_id' => $component['id'],
                    'component_name' => $component['name'],
                    'component_type' => $component['type'],
                    'amount' => $component['amount'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $totalGross += $salaryData['gross_earnings'];
            $totalDeductions += $salaryData['total_deductions'];
            $totalNet += $salaryData['net_salary'];
            $employeeCount++;
        }

        // Update period
        $this->db->update($this->table, [
            'status' => 'processing',
            'total_employees' => $employeeCount,
            'total_gross' => $totalGross,
            'total_deductions' => $totalDeductions,
            'total_net' => $totalNet,
            'processed_by' => $processedBy,
            'processed_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$periodId]);

        return true;
    }

    /**
     * Calculate employee salary
     */
    private function calculateEmployeeSalary(array $employee, array $attendanceStats, int $workingDays): array
    {
        $basicSalary = $employee['basic_salary'];
        $perDaySalary = $basicSalary / $workingDays;

        // Adjust for absences (unpaid leave)
        $paidDays = $workingDays - $attendanceStats['absent_days'];
        $adjustedBasic = $perDaySalary * $paidDays;

        // Get salary components
        $components = $this->getEmployeeSalaryStructure($employee['id']);

        $earnings = $adjustedBasic;
        $deductions = 0;
        $componentList = [];

        foreach ($components as $comp) {
            $amount = $comp['calculation_type'] === 'percentage'
                ? ($basicSalary * $comp['amount'] / 100)
                : $comp['amount'];

            $componentList[] = [
                'id' => $comp['component_id'],
                'name' => $comp['name'],
                'type' => $comp['type'],
                'amount' => $amount
            ];

            if ($comp['type'] === 'earning') {
                $earnings += $amount;
            } else {
                $deductions += $amount;
            }
        }

        // Overtime calculation (1.5x hourly rate)
        $hourlyRate = $basicSalary / ($workingDays * 8);
        $overtimeAmount = $attendanceStats['total_overtime'] * $hourlyRate * 1.5;
        $earnings += $overtimeAmount;

        return [
            'gross_earnings' => round($earnings, 2),
            'total_deductions' => round($deductions, 2),
            'net_salary' => round($earnings - $deductions, 2),
            'overtime_amount' => round($overtimeAmount, 2),
            'components' => $componentList
        ];
    }

    /**
     * Get working days between dates
     */
    private function getWorkingDays(string $startDate, string $endDate): int
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $workingDays = 0;

        while ($start <= $end) {
            $dayOfWeek = $start->format('N');
            // Exclude Friday (5) for Bangladesh, or Saturday/Sunday for Western
            if ($dayOfWeek != 5) { // Adjust based on your weekend
                $workingDays++;
            }
            $start->modify('+1 day');
        }

        return $workingDays;
    }

    /**
     * Approve payroll
     */
    public function approvePayroll(int $periodId, int $approvedBy): bool
    {
        $period = $this->find($periodId);
        if (!$period || $period['status'] !== 'processing') {
            return false;
        }

        $this->db->update($this->table, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$periodId]);

        return true;
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(int $periodId): bool
    {
        $period = $this->find($periodId);
        if (!$period || $period['status'] !== 'approved') {
            return false;
        }

        // Update all details to paid
        $this->db->update('payroll_details', [
            'payment_status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s')
        ], 'payroll_period_id = ?', [$periodId]);

        $this->db->update($this->table, ['status' => 'paid'], 'id = ?', [$periodId]);

        return true;
    }

    /**
     * Get payroll details for a period
     */
    public function getPayrollDetails(int $periodId): array
    {
        return $this->db->fetchAll(
            "SELECT pd.*, e.employee_id as emp_code, e.first_name, e.last_name, e.designation,
                    d.name as department_name
             FROM payroll_details pd
             JOIN employees e ON pd.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE pd.payroll_period_id = ?
             ORDER BY e.first_name, e.last_name",
            [$periodId]
        );
    }

    /**
     * Get employee payslip
     */
    public function getPayslip(int $detailId): ?array
    {
        $detail = $this->db->fetch(
            "SELECT pd.*, pp.period_name, pp.start_date, pp.end_date, pp.pay_date,
                    e.employee_id as emp_code, e.first_name, e.last_name, e.designation,
                    e.bank_name, e.bank_account, e.mobile_banking,
                    d.name as department_name
             FROM payroll_details pd
             JOIN payroll_periods pp ON pd.payroll_period_id = pp.id
             JOIN employees e ON pd.employee_id = e.id
             LEFT JOIN departments d ON e.department_id = d.id
             WHERE pd.id = ?",
            [$detailId]
        );

        if ($detail) {
            $detail['components'] = $this->db->fetchAll(
                "SELECT * FROM payroll_detail_components WHERE payroll_detail_id = ?",
                [$detailId]
            );
        }

        return $detail;
    }
}
