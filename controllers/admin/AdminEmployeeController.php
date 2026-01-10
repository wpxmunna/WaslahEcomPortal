<?php
/**
 * Admin Employee Controller
 */

class AdminEmployeeController extends Controller
{
    private Employee $employeeModel;

    public function __construct()
    {
        parent::__construct();

        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        $this->employeeModel = new Employee();
    }

    /**
     * List employees
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $page = (int) $this->get('page', 1);

        $filters = [
            'status' => $this->get('status'),
            'department_id' => $this->get('department'),
            'search' => $this->get('search')
        ];

        $result = $this->employeeModel->getAdminEmployees($storeId, $filters, $page);
        $departments = $this->employeeModel->getDepartments($storeId);
        $stats = $this->employeeModel->getStats($storeId);

        $this->view('admin/employees/index', [
            'pageTitle' => 'Employees - HR',
            'employees' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'departments' => $departments,
            'stats' => $stats
        ], 'admin');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $departments = $this->employeeModel->getDepartments($storeId);

        $this->view('admin/employees/create', [
            'pageTitle' => 'Add Employee - HR',
            'departments' => $departments
        ], 'admin');
    }

    /**
     * Store new employee
     */
    public function store(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/employees', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'first_name' => trim($this->post('first_name')),
            'last_name' => trim($this->post('last_name')),
            'email' => trim($this->post('email')),
            'phone' => trim($this->post('phone')),
            'date_of_birth' => $this->post('date_of_birth') ?: null,
            'gender' => $this->post('gender'),
            'national_id' => trim($this->post('national_id')),
            'address' => trim($this->post('address')),
            'city' => trim($this->post('city')),
            'department_id' => $this->post('department_id') ?: null,
            'designation' => trim($this->post('designation')),
            'employment_type' => $this->post('employment_type', 'full_time'),
            'hire_date' => $this->post('hire_date'),
            'basic_salary' => (float) $this->post('basic_salary', 0),
            'bank_name' => trim($this->post('bank_name')),
            'bank_account' => trim($this->post('bank_account')),
            'mobile_banking' => trim($this->post('mobile_banking')),
            'emergency_contact_name' => trim($this->post('emergency_contact_name')),
            'emergency_contact_phone' => trim($this->post('emergency_contact_phone')),
            'notes' => trim($this->post('notes')),
            'status' => 'active'
        ];

        if (empty($data['first_name']) || empty($data['hire_date'])) {
            $this->redirect('admin/employees/create', 'First name and hire date are required', 'error');
        }

        $employeeId = $this->employeeModel->createEmployee($data);

        if ($employeeId) {
            $this->redirect('admin/employees/view/' . $employeeId, 'Employee added successfully', 'success');
        } else {
            $this->redirect('admin/employees/create', 'Failed to add employee', 'error');
        }
    }

    /**
     * View employee details
     */
    public function show(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $employee = $this->employeeModel->getWithDetails($id);

        if (!$employee || $employee['store_id'] != $storeId) {
            $this->redirect('admin/employees', 'Employee not found', 'error');
        }

        // Get attendance for current month
        $attendance = new Attendance();
        $monthlyAttendance = $attendance->getMonthlyAttendance($id, date('Y-m'));

        // Get salary structure
        $payroll = new Payroll();
        $salaryStructure = $payroll->getEmployeeSalaryStructure($id);

        $this->view('admin/employees/show', [
            'pageTitle' => $employee['first_name'] . ' ' . $employee['last_name'] . ' - Employee',
            'employee' => $employee,
            'monthlyAttendance' => $monthlyAttendance,
            'salaryStructure' => $salaryStructure
        ], 'admin');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $employee = $this->employeeModel->find($id);

        if (!$employee || $employee['store_id'] != $storeId) {
            $this->redirect('admin/employees', 'Employee not found', 'error');
        }

        $departments = $this->employeeModel->getDepartments($storeId);

        $this->view('admin/employees/edit', [
            'pageTitle' => 'Edit Employee - HR',
            'employee' => $employee,
            'departments' => $departments
        ], 'admin');
    }

    /**
     * Update employee
     */
    public function update(int $id): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/employees', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);
        $employee = $this->employeeModel->find($id);

        if (!$employee || $employee['store_id'] != $storeId) {
            $this->redirect('admin/employees', 'Employee not found', 'error');
        }

        $data = [
            'first_name' => trim($this->post('first_name')),
            'last_name' => trim($this->post('last_name')),
            'email' => trim($this->post('email')),
            'phone' => trim($this->post('phone')),
            'date_of_birth' => $this->post('date_of_birth') ?: null,
            'gender' => $this->post('gender'),
            'national_id' => trim($this->post('national_id')),
            'address' => trim($this->post('address')),
            'city' => trim($this->post('city')),
            'department_id' => $this->post('department_id') ?: null,
            'designation' => trim($this->post('designation')),
            'employment_type' => $this->post('employment_type'),
            'hire_date' => $this->post('hire_date'),
            'termination_date' => $this->post('termination_date') ?: null,
            'basic_salary' => (float) $this->post('basic_salary', 0),
            'bank_name' => trim($this->post('bank_name')),
            'bank_account' => trim($this->post('bank_account')),
            'mobile_banking' => trim($this->post('mobile_banking')),
            'emergency_contact_name' => trim($this->post('emergency_contact_name')),
            'emergency_contact_phone' => trim($this->post('emergency_contact_phone')),
            'notes' => trim($this->post('notes')),
            'status' => $this->post('status', 'active')
        ];

        $this->employeeModel->updateEmployee($id, $data);
        $this->redirect('admin/employees/view/' . $id, 'Employee updated successfully', 'success');
    }

    /**
     * Departments management
     */
    public function departments(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $departments = $this->employeeModel->getDepartments($storeId);

        $this->view('admin/employees/departments', [
            'pageTitle' => 'Departments - HR',
            'departments' => $departments
        ], 'admin');
    }

    /**
     * Store department
     */
    public function storeDepartment(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/employees/departments', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'name' => trim($this->post('name')),
            'code' => trim($this->post('code')),
            'description' => trim($this->post('description'))
        ];

        $this->employeeModel->createDepartment($data);
        $this->redirect('admin/employees/departments', 'Department added successfully', 'success');
    }
}
