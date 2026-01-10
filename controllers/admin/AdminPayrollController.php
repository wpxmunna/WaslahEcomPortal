<?php
/**
 * Admin Payroll Controller
 */

class AdminPayrollController extends Controller
{
    private Payroll $payrollModel;
    private Employee $employeeModel;

    public function __construct()
    {
        parent::__construct();

        if (!Session::isAdmin()) {
            $this->redirect('admin/login');
        }

        // Only full admin for payroll
        if (Session::get('admin_role') !== 'admin') {
            $this->redirect('admin', 'Access denied', 'error');
        }

        $this->payrollModel = new Payroll();
        $this->employeeModel = new Employee();
    }

    /**
     * Payroll periods list
     */
    public function index(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $result = $this->payrollModel->getPayrollPeriods($storeId);

        $this->view('admin/payroll/index', [
            'pageTitle' => 'Payroll - HR',
            'periods' => $result['data']
        ], 'admin');
    }

    /**
     * Create new payroll period
     */
    public function create(): void
    {
        $storeId = Session::get('admin_store_id', 1);

        // Suggest next month period
        $suggestedStart = date('Y-m-01');
        $suggestedEnd = date('Y-m-t');
        $suggestedName = date('F Y');

        $this->view('admin/payroll/create', [
            'pageTitle' => 'Create Payroll Period',
            'suggestedStart' => $suggestedStart,
            'suggestedEnd' => $suggestedEnd,
            'suggestedName' => $suggestedName
        ], 'admin');
    }

    /**
     * Store payroll period
     */
    public function store(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/payroll', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'period_name' => trim($this->post('period_name')),
            'start_date' => $this->post('start_date'),
            'end_date' => $this->post('end_date'),
            'pay_date' => $this->post('pay_date') ?: null,
            'status' => 'draft'
        ];

        $periodId = $this->payrollModel->createPayrollPeriod($data);
        $this->redirect('admin/payroll/view/' . $periodId, 'Payroll period created', 'success');
    }

    /**
     * View payroll period details
     */
    public function show(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $period = $this->payrollModel->find($id);

        if (!$period || $period['store_id'] != $storeId) {
            $this->redirect('admin/payroll', 'Period not found', 'error');
        }

        $details = $this->payrollModel->getPayrollDetails($id);

        $this->view('admin/payroll/show', [
            'pageTitle' => $period['period_name'] . ' - Payroll',
            'period' => $period,
            'details' => $details
        ], 'admin');
    }

    /**
     * Process payroll
     */
    public function process(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $userId = Session::get('admin_user_id');

        $period = $this->payrollModel->find($id);
        if (!$period || $period['store_id'] != $storeId) {
            $this->redirect('admin/payroll', 'Period not found', 'error');
        }

        if ($this->payrollModel->processPayroll($id, $storeId, $userId)) {
            $this->redirect('admin/payroll/view/' . $id, 'Payroll processed successfully', 'success');
        } else {
            $this->redirect('admin/payroll/view/' . $id, 'Failed to process payroll', 'error');
        }
    }

    /**
     * Approve payroll
     */
    public function approve(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $userId = Session::get('admin_user_id');

        $period = $this->payrollModel->find($id);
        if (!$period || $period['store_id'] != $storeId) {
            $this->redirect('admin/payroll', 'Period not found', 'error');
        }

        if ($this->payrollModel->approvePayroll($id, $userId)) {
            $this->redirect('admin/payroll/view/' . $id, 'Payroll approved', 'success');
        } else {
            $this->redirect('admin/payroll/view/' . $id, 'Failed to approve', 'error');
        }
    }

    /**
     * Mark as paid
     */
    public function markPaid(int $id): void
    {
        $storeId = Session::get('admin_store_id', 1);

        $period = $this->payrollModel->find($id);
        if (!$period || $period['store_id'] != $storeId) {
            $this->redirect('admin/payroll', 'Period not found', 'error');
        }

        if ($this->payrollModel->markAsPaid($id)) {
            $this->redirect('admin/payroll/view/' . $id, 'Payroll marked as paid', 'success');
        } else {
            $this->redirect('admin/payroll/view/' . $id, 'Failed to mark as paid', 'error');
        }
    }

    /**
     * View payslip
     */
    public function payslip(int $detailId): void
    {
        $payslip = $this->payrollModel->getPayslip($detailId);

        if (!$payslip) {
            $this->redirect('admin/payroll', 'Payslip not found', 'error');
        }

        $this->view('admin/payroll/payslip', [
            'pageTitle' => 'Payslip - ' . $payslip['first_name'] . ' ' . $payslip['last_name'],
            'payslip' => $payslip
        ], 'admin');
    }

    /**
     * Salary components management
     */
    public function components(): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $components = $this->payrollModel->getSalaryComponents($storeId);

        $this->view('admin/payroll/components', [
            'pageTitle' => 'Salary Components',
            'components' => $components
        ], 'admin');
    }

    /**
     * Store salary component
     */
    public function storeComponent(): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/payroll/components', 'Invalid request', 'error');
        }

        $storeId = Session::get('admin_store_id', 1);

        $data = [
            'store_id' => $storeId,
            'name' => trim($this->post('name')),
            'type' => $this->post('type'),
            'calculation_type' => $this->post('calculation_type', 'fixed'),
            'default_amount' => (float) $this->post('default_amount', 0),
            'is_taxable' => $this->post('is_taxable') ? 1 : 0,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('salary_components', $data);
        $this->redirect('admin/payroll/components', 'Component added', 'success');
    }

    /**
     * Employee salary structure
     */
    public function employeeSalary(int $employeeId): void
    {
        $storeId = Session::get('admin_store_id', 1);
        $employee = $this->employeeModel->find($employeeId);

        if (!$employee || $employee['store_id'] != $storeId) {
            $this->redirect('admin/employees', 'Employee not found', 'error');
        }

        $components = $this->payrollModel->getSalaryComponents($storeId);
        $structure = $this->payrollModel->getEmployeeSalaryStructure($employeeId);

        $this->view('admin/payroll/employee-salary', [
            'pageTitle' => 'Salary Structure - ' . $employee['first_name'],
            'employee' => $employee,
            'components' => $components,
            'structure' => $structure
        ], 'admin');
    }

    /**
     * Update employee salary component
     */
    public function updateEmployeeSalary(int $employeeId): void
    {
        if (!Session::validateCsrf($this->post('csrf_token'))) {
            $this->redirect('admin/payroll/employee-salary/' . $employeeId, 'Invalid request', 'error');
        }

        $componentId = (int) $this->post('component_id');
        $amount = (float) $this->post('amount');
        $effectiveFrom = $this->post('effective_from', date('Y-m-d'));

        $this->payrollModel->setEmployeeSalaryComponent($employeeId, $componentId, $amount, $effectiveFrom);
        $this->redirect('admin/payroll/employee-salary/' . $employeeId, 'Salary updated', 'success');
    }
}
