-- Mini ERP/Accounting System Migration
-- Run this SQL to create all ERP-related tables

-- =============================================
-- EXPENSE TRACKING TABLES
-- =============================================

-- Expense Categories
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#6c757d',
    icon VARCHAR(50) DEFAULT 'tag',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_id (store_id),
    INDEX idx_slug (slug),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Expenses
CREATE TABLE IF NOT EXISTS expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    category_id INT,
    expense_number VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    amount DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL,
    expense_date DATE NOT NULL,
    payment_method ENUM('cash','bank_transfer','mobile_banking','card','other') DEFAULT 'cash',
    payment_status ENUM('pending','paid','partial') DEFAULT 'pending',
    reference_number VARCHAR(100),
    vendor_name VARCHAR(255),
    receipt_path VARCHAR(255),
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_date (store_id, expense_date),
    INDEX idx_category (category_id),
    INDEX idx_payment_status (payment_status),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- SUPPLIER & PURCHASE ORDER TABLES
-- =============================================

-- Suppliers
CREATE TABLE IF NOT EXISTS suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Bangladesh',
    payment_terms INT DEFAULT 30,
    notes TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    total_purchases DECIMAL(14,2) DEFAULT 0.00,
    total_paid DECIMAL(14,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_status (store_id, status),
    INDEX idx_name (name),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Purchase Orders
CREATE TABLE IF NOT EXISTS purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    supplier_id INT NOT NULL,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('draft','pending','approved','ordered','partial','received','cancelled') DEFAULT 'draft',
    order_date DATE NOT NULL,
    expected_date DATE,
    received_date DATE,
    subtotal DECIMAL(14,2) DEFAULT 0.00,
    tax_amount DECIMAL(12,2) DEFAULT 0.00,
    shipping_amount DECIMAL(12,2) DEFAULT 0.00,
    discount_amount DECIMAL(12,2) DEFAULT 0.00,
    total_amount DECIMAL(14,2) DEFAULT 0.00,
    payment_status ENUM('pending','partial','paid') DEFAULT 'pending',
    paid_amount DECIMAL(14,2) DEFAULT 0.00,
    notes TEXT,
    created_by INT,
    approved_by INT,
    approved_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_status (store_id, status),
    INDEX idx_supplier (supplier_id),
    INDEX idx_order_date (order_date),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Purchase Order Items
CREATE TABLE IF NOT EXISTS purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100),
    variant_id INT,
    variant_info VARCHAR(255),
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_cost DECIMAL(12,2) NOT NULL,
    total_cost DECIMAL(14,2) NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_po (purchase_order_id),
    INDEX idx_product (product_id),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Supplier Payments
CREATE TABLE IF NOT EXISTS supplier_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    supplier_id INT NOT NULL,
    purchase_order_id INT,
    payment_number VARCHAR(50) NOT NULL UNIQUE,
    amount DECIMAL(14,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash','bank_transfer','check','mobile_banking','other') DEFAULT 'bank_transfer',
    reference_number VARCHAR(100),
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_supplier (supplier_id),
    INDEX idx_po (purchase_order_id),
    INDEX idx_payment_date (payment_date),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- ACCOUNTING TABLES
-- =============================================

-- Chart of Accounts
CREATE TABLE IF NOT EXISTS chart_of_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    account_code VARCHAR(20) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    account_type ENUM('asset','liability','equity','revenue','expense','cogs') NOT NULL,
    parent_id INT,
    description TEXT,
    is_system TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    normal_balance ENUM('debit','credit') NOT NULL,
    current_balance DECIMAL(14,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_store_code (store_id, account_code),
    INDEX idx_type (account_type),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL
);

-- Journal Entries
CREATE TABLE IF NOT EXISTS journal_entries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    entry_number VARCHAR(50) NOT NULL UNIQUE,
    entry_date DATE NOT NULL,
    description TEXT NOT NULL,
    reference_type ENUM('manual','order','expense','purchase','return','payment','adjustment') DEFAULT 'manual',
    reference_id INT,
    total_debit DECIMAL(14,2) NOT NULL,
    total_credit DECIMAL(14,2) NOT NULL,
    status ENUM('draft','posted','reversed') DEFAULT 'draft',
    posted_at DATETIME,
    posted_by INT,
    reversed_by_id INT,
    notes TEXT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_store_date (store_id, entry_date),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_status (status),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Journal Entry Lines
CREATE TABLE IF NOT EXISTS journal_entry_lines (
    id INT PRIMARY KEY AUTO_INCREMENT,
    journal_entry_id INT NOT NULL,
    account_id INT NOT NULL,
    description VARCHAR(255),
    debit_amount DECIMAL(14,2) DEFAULT 0.00,
    credit_amount DECIMAL(14,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entry (journal_entry_id),
    INDEX idx_account (account_id),
    FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
);

-- =============================================
-- SEED DEFAULT DATA
-- =============================================

-- Default Expense Categories
INSERT INTO expense_categories (store_id, name, slug, description, color, icon, is_active) VALUES
(1, 'Rent & Utilities', 'rent-utilities', 'Office rent, electricity, water, internet', '#3498db', 'building', 1),
(1, 'Salaries & Wages', 'salaries-wages', 'Employee salaries, wages, bonuses', '#9b59b6', 'users', 1),
(1, 'Marketing & Advertising', 'marketing-advertising', 'Ads, promotions, social media', '#e74c3c', 'bullhorn', 1),
(1, 'Office Supplies', 'office-supplies', 'Stationery, equipment, furniture', '#f39c12', 'paperclip', 1),
(1, 'Shipping & Logistics', 'shipping-logistics', 'Courier fees, packaging materials', '#1abc9c', 'truck', 1),
(1, 'Bank Fees & Charges', 'bank-fees', 'Transaction fees, bank charges', '#34495e', 'university', 1),
(1, 'Software & Subscriptions', 'software-subscriptions', 'Software licenses, SaaS subscriptions', '#2ecc71', 'laptop', 1),
(1, 'Travel & Transportation', 'travel-transportation', 'Business travel, fuel, transport', '#e67e22', 'car', 1),
(1, 'Maintenance & Repairs', 'maintenance-repairs', 'Equipment repair, maintenance', '#95a5a6', 'wrench', 1),
(1, 'Miscellaneous', 'miscellaneous', 'Other business expenses', '#7f8c8d', 'ellipsis-h', 1);

-- Default Chart of Accounts
INSERT INTO chart_of_accounts (store_id, account_code, account_name, account_type, normal_balance, is_system, description) VALUES
-- Assets
(1, '1000', 'Cash', 'asset', 'debit', 1, 'Cash on hand'),
(1, '1010', 'Bank Account', 'asset', 'debit', 1, 'Business bank accounts'),
(1, '1100', 'Accounts Receivable', 'asset', 'debit', 1, 'Money owed by customers'),
(1, '1200', 'Inventory', 'asset', 'debit', 1, 'Product inventory value'),
(1, '1300', 'Prepaid Expenses', 'asset', 'debit', 0, 'Advance payments'),

-- Liabilities
(1, '2000', 'Accounts Payable', 'liability', 'credit', 1, 'Money owed to suppliers'),
(1, '2100', 'Tax Payable', 'liability', 'credit', 1, 'Taxes collected/owed'),
(1, '2200', 'Customer Deposits', 'liability', 'credit', 0, 'Advance payments from customers'),

-- Equity
(1, '3000', 'Owner Equity', 'equity', 'credit', 1, 'Owner investment'),
(1, '3100', 'Retained Earnings', 'equity', 'credit', 1, 'Accumulated profits'),

-- Revenue
(1, '4000', 'Sales Revenue', 'revenue', 'credit', 1, 'Product sales income'),
(1, '4010', 'Shipping Revenue', 'revenue', 'credit', 1, 'Shipping fees collected'),
(1, '4100', 'Other Income', 'revenue', 'credit', 0, 'Miscellaneous income'),
(1, '4200', 'Discounts Given', 'revenue', 'debit', 1, 'Sales discounts'),

-- Cost of Goods Sold
(1, '5000', 'Cost of Goods Sold', 'cogs', 'debit', 1, 'Direct cost of products sold'),
(1, '5100', 'Shipping Costs', 'cogs', 'debit', 1, 'Outbound shipping costs'),

-- Expenses
(1, '6000', 'Rent Expense', 'expense', 'debit', 0, 'Office/warehouse rent'),
(1, '6010', 'Utilities Expense', 'expense', 'debit', 0, 'Electricity, water, internet'),
(1, '6020', 'Salary Expense', 'expense', 'debit', 0, 'Employee salaries'),
(1, '6030', 'Marketing Expense', 'expense', 'debit', 0, 'Advertising and marketing'),
(1, '6040', 'Office Supplies', 'expense', 'debit', 0, 'Office supplies and equipment'),
(1, '6050', 'Bank Fees', 'expense', 'debit', 0, 'Bank charges and fees'),
(1, '6060', 'Insurance Expense', 'expense', 'debit', 0, 'Business insurance'),
(1, '6100', 'Miscellaneous Expense', 'expense', 'debit', 0, 'Other expenses');
