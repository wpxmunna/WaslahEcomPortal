<?php
/**
 * Admin Attendance Controller
 */

class AdminAttendanceController extends Controller
{
    private Attendance $attendanceModel;
    private Employee $employeeModel;

    public function __construct()
    {
        parent::__construct();

        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        $this->attendanceModel = new Attendance();
        $this->employeeModel = new Employee();
    }

    /**
     * Attendance dashboard/list
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'employee_id' => $this->get('employee'),
            'date' => $this->get('date'),
            'month' => $this->get('month', date('Y-m')),
            'status' => $this->get('status')
        ];

        $result = $this->attendanceModel->getAttendance($storeId, $filters, $page);
        $employees = $this->employeeModel->getActive($storeId);
        $todaySummary = $this->attendanceModel->getTodaySummary($storeId);

        $this->view('admin/attendance/index', [
            'pageTitle' => 'Attendance - HR',
            'records' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'employees' => $employees,
            'todaySummary' => $todaySummary
        ], 'admin');
    }

    /**
     * Today's attendance (quick view and check-in)
     */
    public function today(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $today = date('Y-m-d');

        // Get all active employees with today's attendance
        $employees = $this->db->fetchAll(
            "SELECT e.*, a.id as attendance_id, a.check_in, a.check_out, a.status as attendance_status
             FROM employees e
             LEFT JOIN attendance a ON e.id = a.employee_id AND a.attendance_date = ?
             WHERE e.store_id = ? AND e.status = 'active'
             ORDER BY e.first_name, e.last_name",
            [$today, $storeId]
        );

        $summary = $this->attendanceModel->getTodaySummary($storeId);

        $this->view('admin/attendance/today', [
            'pageTitle' => "Today's Attendance",
            'employees' => $employees,
            'summary' => $summary,
            'today' => $today
        ], 'admin');
    }

    /**
     * Check-in employee
     */
    public function checkIn(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/attendance/today', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $employeeId = (int) $this->post('employee_id');

        if ($this->attendanceModel->checkIn($employeeId, $storeId)) {
            $this->redirect('admin/attendance/today', 'Check-in recorded', 'success');
        } else {
            $this->redirect('admin/attendance/today', 'Already checked in', 'error');
        }
    }

    /**
     * Check-out employee
     */
    public function checkOut(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/attendance/today', 'Invalid request', 'error');
        }

        $employeeId = (int) $this->post('employee_id');

        if ($this->attendanceModel->checkOut($employeeId)) {
            $this->redirect('admin/attendance/today', 'Check-out recorded', 'success');
        } else {
            $this->redirect('admin/attendance/today', 'Check-out failed', 'error');
        }
    }

    /**
     * Bulk check-in
     */
    public function bulkCheckIn(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/attendance/today', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $employeeIds = $this->post('employee_ids', []);
        $count = 0;

        foreach ($employeeIds as $empId) {
            if ($this->attendanceModel->checkIn((int)$empId, $storeId)) {
                $count++;
            }
        }

        $this->redirect('admin/attendance/today', $count . ' employees checked in', 'success');
    }

    /**
     * Mark absent
     */
    public function markAbsent(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/attendance/today', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $date = $this->post('date', date('Y-m-d'));

        $count = $this->attendanceModel->markAbsent($storeId, $date);
        $this->redirect('admin/attendance/today', $count . ' employees marked absent', 'success');
    }

    /**
     * Monthly report
     */
    public function monthlyReport(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $month = $this->get('month', date('Y-m'));

        $employees = $this->employeeModel->getActive($storeId);
        $report = [];

        foreach ($employees as $emp) {
            $attendance = $this->attendanceModel->getMonthlyAttendance($emp['id'], $month);
            $stats = [
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'leave' => 0,
                'total_hours' => 0,
                'overtime' => 0
            ];

            foreach ($attendance as $record) {
                if (in_array($record['status'], ['present', 'late'])) {
                    $stats['present']++;
                    if ($record['status'] === 'late') $stats['late']++;
                } elseif ($record['status'] === 'absent') {
                    $stats['absent']++;
                } elseif ($record['status'] === 'leave') {
                    $stats['leave']++;
                }
                $stats['total_hours'] += $record['work_hours'] ?? 0;
                $stats['overtime'] += $record['overtime_hours'] ?? 0;
            }

            $report[] = [
                'employee' => $emp,
                'stats' => $stats
            ];
        }

        $this->view('admin/attendance/monthly-report', [
            'pageTitle' => 'Monthly Attendance Report',
            'report' => $report,
            'month' => $month
        ], 'admin');
    }
}
