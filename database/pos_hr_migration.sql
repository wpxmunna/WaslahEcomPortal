-- =============================================
-- POS & HR/Payroll System Migration
-- Run this after the ERP migration
-- =============================================

-- =============================================
-- POS (Point of Sale) Tables
-- =============================================

-- POS Terminals/Registers
CREATE TABLE IF NOT EXISTS pos_terminals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    terminal_name VARCHAR(100) NOT NULL,
    terminal_code VARCHAR(20) NOT NULL,
    location VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_store_code (store_id, terminal_code),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- POS Shifts (for cashier accountability)
CREATE TABLE IF NOT EXISTS pos_shifts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    terminal_id INT NOT NULL,
    user_id INT NOT NULL,
    shift_number VARCHAR(50) NOT NULL UNIQUE,
    opening_time DATETIME NOT NULL,
    closing_time DATETIME,
    opening_cash DECIMAL(12,2) DEFAULT 0.00,
    expected_cash DECIMAL(12,2) DEFAULT 0.00,
    actual_cash DECIMAL(12,2) DEFAULT 0.00,
    cash_difference DECIMAL(12,2) DEFAULT 0.00,
    total_sales DECIMAL(14,2) DEFAULT 0.00,
    total_transactions INT DEFAULT 0,
    total_refunds DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('open','closed') DEFAULT 'open',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (terminal_id) REFERENCES pos_terminals(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- POS Transactions (quick sales without full checkout)
CREATE TABLE IF NOT EXISTS pos_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    shift_id INT,
    terminal_id INT,
    transaction_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT,
    customer_name VARCHAR(255),
    customer_phone VARCHAR(50),
    subtotal DECIMAL(14,2) NOT NULL,
    discount_amount DECIMAL(12,2) DEFAULT 0.00,
    tax_amount DECIMAL(12,2) DEFAULT 0.00,
    total_amount DECIMAL(14,2) NOT NULL,
    payment_method ENUM('cash','card','mobile_banking','mixed') DEFAULT 'cash',
    cash_received DECIMAL(14,2) DEFAULT 0.00,
    change_amount DECIMAL(12,2) DEFAULT 0.00,
    card_amount DECIMAL(14,2) DEFAULT 0.00,
    mobile_amount DECIMAL(14,2) DEFAULT 0.00,
    status ENUM('completed','refunded','void') DEFAULT 'completed',
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES pos_shifts(id) ON DELETE SET NULL,
    FOREIGN KEY (terminal_id) REFERENCES pos_terminals(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- POS Transaction Items
CREATE TABLE IF NOT EXISTS pos_transaction_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100),
    variant_id INT,
    variant_info VARCHAR(255),
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    discount DECIMAL(12,2) DEFAULT 0.00,
    total_price DECIMAL(14,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES pos_transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- POS Cash Drawer Logs
CREATE TABLE IF NOT EXISTS pos_cash_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    shift_id INT NOT NULL,
    log_type ENUM('cash_in','cash_out','adjustment') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    reason VARCHAR(255),
    reference VARCHAR(100),
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES pos_shifts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- HR (Human Resources) Tables
-- =============================================

-- Departments
CREATE TABLE IF NOT EXISTS departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    description TEXT,
    manager_id INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Employees
CREATE TABLE IF NOT EXISTS employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    user_id INT,
    employee_id VARCHAR(20) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),
    date_of_birth DATE,
    gender ENUM('male','female','other'),
    national_id VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    department_id INT,
    designation VARCHAR(100),
    employment_type ENUM('full_time','part_time','contract','intern') DEFAULT 'full_time',
    hire_date DATE NOT NULL,
    termination_date DATE,
    basic_salary DECIMAL(12,2) DEFAULT 0.00,
    bank_name VARCHAR(100),
    bank_account VARCHAR(50),
    mobile_banking VARCHAR(50),
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(50),
    photo VARCHAR(255),
    status ENUM('active','on_leave','terminated','resigned') DEFAULT 'active',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_store_empid (store_id, employee_id),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Attendance
CREATE TABLE IF NOT EXISTS attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    employee_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    check_in TIME,
    check_out TIME,
    status ENUM('present','absent','late','half_day','leave','holiday') DEFAULT 'present',
    work_hours DECIMAL(4,2) DEFAULT 0.00,
    overtime_hours DECIMAL(4,2) DEFAULT 0.00,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_emp_date (employee_id, attendance_date),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Leave Types
CREATE TABLE IF NOT EXISTS leave_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    days_per_year INT DEFAULT 0,
    is_paid TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Leave Requests
CREATE TABLE IF NOT EXISTS leave_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days INT NOT NULL,
    reason TEXT,
    status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    approved_by INT,
    approved_at DATETIME,
    rejection_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Salary Components (allowances, deductions)
CREATE TABLE IF NOT EXISTS salary_components (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('earning','deduction') NOT NULL,
    calculation_type ENUM('fixed','percentage') DEFAULT 'fixed',
    default_amount DECIMAL(12,2) DEFAULT 0.00,
    percentage_of VARCHAR(50),
    is_taxable TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Employee Salary Structure
CREATE TABLE IF NOT EXISTS employee_salary_structure (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    component_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (component_id) REFERENCES salary_components(id) ON DELETE CASCADE
);

-- Payroll Periods
CREATE TABLE IF NOT EXISTS payroll_periods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    period_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    pay_date DATE,
    status ENUM('draft','processing','approved','paid','cancelled') DEFAULT 'draft',
    total_employees INT DEFAULT 0,
    total_gross DECIMAL(14,2) DEFAULT 0.00,
    total_deductions DECIMAL(14,2) DEFAULT 0.00,
    total_net DECIMAL(14,2) DEFAULT 0.00,
    processed_by INT,
    processed_at DATETIME,
    approved_by INT,
    approved_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Payroll Details (per employee per period)
CREATE TABLE IF NOT EXISTS payroll_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payroll_period_id INT NOT NULL,
    employee_id INT NOT NULL,
    basic_salary DECIMAL(12,2) DEFAULT 0.00,
    working_days INT DEFAULT 0,
    present_days INT DEFAULT 0,
    absent_days INT DEFAULT 0,
    leave_days INT DEFAULT 0,
    overtime_hours DECIMAL(6,2) DEFAULT 0.00,
    overtime_amount DECIMAL(12,2) DEFAULT 0.00,
    gross_earnings DECIMAL(14,2) DEFAULT 0.00,
    total_deductions DECIMAL(14,2) DEFAULT 0.00,
    net_salary DECIMAL(14,2) DEFAULT 0.00,
    payment_method ENUM('bank','cash','mobile_banking') DEFAULT 'bank',
    payment_status ENUM('pending','paid') DEFAULT 'pending',
    paid_at DATETIME,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_period_employee (payroll_period_id, employee_id),
    FOREIGN KEY (payroll_period_id) REFERENCES payroll_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Payroll Detail Components (breakdown of earnings/deductions)
CREATE TABLE IF NOT EXISTS payroll_detail_components (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payroll_detail_id INT NOT NULL,
    component_id INT NOT NULL,
    component_name VARCHAR(100) NOT NULL,
    component_type ENUM('earning','deduction') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payroll_detail_id) REFERENCES payroll_details(id) ON DELETE CASCADE,
    FOREIGN KEY (component_id) REFERENCES salary_components(id) ON DELETE CASCADE
);

-- =============================================
-- Default Data
-- =============================================

-- Default POS Terminal
INSERT INTO pos_terminals (store_id, terminal_name, terminal_code, location) VALUES
(1, 'Main Counter', 'POS001', 'Front Store');

-- Default Departments
INSERT INTO departments (store_id, name, code, description) VALUES
(1, 'Management', 'MGMT', 'Executive and management staff'),
(1, 'Sales', 'SALES', 'Sales and customer service'),
(1, 'Operations', 'OPS', 'Store operations and inventory'),
(1, 'Finance', 'FIN', 'Accounting and finance');

-- Default Leave Types
INSERT INTO leave_types (store_id, name, days_per_year, is_paid) VALUES
(1, 'Annual Leave', 14, 1),
(1, 'Sick Leave', 10, 1),
(1, 'Casual Leave', 7, 1),
(1, 'Unpaid Leave', 0, 0),
(1, 'Maternity Leave', 120, 1);

-- Default Salary Components
INSERT INTO salary_components (store_id, name, type, calculation_type, default_amount) VALUES
(1, 'House Rent Allowance', 'earning', 'percentage', 40.00),
(1, 'Medical Allowance', 'earning', 'fixed', 1500.00),
(1, 'Transport Allowance', 'earning', 'fixed', 1000.00),
(1, 'Mobile Allowance', 'earning', 'fixed', 500.00),
(1, 'Performance Bonus', 'earning', 'fixed', 0.00),
(1, 'Provident Fund', 'deduction', 'percentage', 5.00),
(1, 'Tax Deduction', 'deduction', 'percentage', 0.00),
(1, 'Advance Deduction', 'deduction', 'fixed', 0.00);

SELECT 'POS & HR/Payroll migration completed successfully!' as message;
