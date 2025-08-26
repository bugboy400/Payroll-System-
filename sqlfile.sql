-- Departments
CREATE TABLE IF NOT EXISTS departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE
);

-- Designations
CREATE TABLE IF NOT EXISTS designations (
    designation_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_id INT NOT NULL,
    designation_name VARCHAR(100) NOT NULL,
    UNIQUE(dept_id, designation_name),
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE
);

-- Personal Details
CREATE TABLE IF NOT EXISTS employees_personal (
    emp_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100),
    fatherName VARCHAR(100),
    dob DATE,
    gender ENUM('male','female','other'),
    nationality VARCHAR(50),
    phone1 VARCHAR(20),
    phone2 VARCHAR(20),
    localaddress TEXT,
    permanentaddress TEXT,
    maritalstatus ENUM('married','unmarried','other'),
    photo VARCHAR(255)
);

-- Company Details
CREATE TABLE IF NOT EXISTS employees_company (
    emp_id VARCHAR(20) PRIMARY KEY,
    dept_id INT NOT NULL,
    designation_id INT NOT NULL,
    dateofjoin DATE,
    dateofleave DATE,
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE,
    FOREIGN KEY (designation_id) REFERENCES designations(designation_id) ON DELETE CASCADE
);

-- Financial Details
CREATE TABLE IF NOT EXISTS employees_financial (
    emp_id VARCHAR(20) PRIMARY KEY,
    basicsal DECIMAL(10,2),
    total_sal DECIMAL(10,2),
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE
);

-- Employee Allowances & Deductions (Master)
CREATE TABLE IF NOT EXISTS employees_allowances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id VARCHAR(20),
    allowance_name VARCHAR(50),
    allowance_amt DECIMAL(10,2),
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS employees_deductions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id VARCHAR(20),
    deduction_name VARCHAR(50),
    deduction_amt DECIMAL(10,2),
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE
);

-- Payslips
CREATE TABLE IF NOT EXISTS payslips (
    payslip_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL,
    dept_id INT NOT NULL,
    year INT NOT NULL,
    month VARCHAR(20) NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    total_allowance DECIMAL(10,2) NOT NULL,
    total_deduction DECIMAL(10,2) NOT NULL,
    net_salary DECIMAL(10,2) NOT NULL,
    status ENUM('Paid','Unpaid') DEFAULT 'Unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE
);

-- Payslip Allowances & Deductions
CREATE TABLE IF NOT EXISTS payslip_allowances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payslip_id INT NOT NULL,
    allowance_name VARCHAR(100),
    allowance_amt DECIMAL(10,2),
    FOREIGN KEY (payslip_id) REFERENCES payslips(payslip_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS payslip_deductions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payslip_id INT NOT NULL,
    deduction_name VARCHAR(100),
    deduction_amt DECIMAL(10,2),
    FOREIGN KEY (payslip_id) REFERENCES payslips(payslip_id) ON DELETE CASCADE
);


-- Add UNIQUE constraint to prevent duplicate payments
ALTER TABLE payslips 
ADD CONSTRAINT unique_payment UNIQUE (employee_id, month, year);

-- Make allowance_name and allowance_amt NOT NULL (optional)
ALTER TABLE payslip_allowances
MODIFY allowance_name VARCHAR(100) NOT NULL,
MODIFY allowance_amt DECIMAL(10,2) NOT NULL;

ALTER TABLE payslip_deductions
MODIFY deduction_name VARCHAR(100) NOT NULL,
MODIFY deduction_amt DECIMAL(10,2) NOT NULL;

-- Add indexes for faster queries (optional)
CREATE INDEX idx_employee ON payslips(employee_id);
CREATE INDEX idx_month_year ON payslips(month, year);


-- Departments table (if not exists)
CREATE TABLE IF NOT EXISTS departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL
);

-- Employees table (if not exists)
CREATE TABLE IF NOT EXISTS employees_personal (
    emp_id VARCHAR(20) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    dept_id INT,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
);

-- Payslips
CREATE TABLE IF NOT EXISTS payslips (
    payslip_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL,
    dept_id INT NOT NULL,
    year INT NOT NULL,
    month VARCHAR(20) NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    total_allowance DECIMAL(10,2) NOT NULL,
    total_deduction DECIMAL(10,2) NOT NULL,
    net_salary DECIMAL(10,2) NOT NULL,
    status ENUM('Paid','Unpaid') DEFAULT 'Unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE
);

-- Payslip Allowances
CREATE TABLE IF NOT EXISTS payslip_allowances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payslip_id INT NOT NULL,
    title VARCHAR(100),
    amount DECIMAL(10,2),
    FOREIGN KEY (payslip_id) REFERENCES payslips(payslip_id) ON DELETE CASCADE
);

-- Payslip Deductions
CREATE TABLE IF NOT EXISTS payslip_deductions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payslip_id INT NOT NULL,
    title VARCHAR(100),
    amount DECIMAL(10,2),
    FOREIGN KEY (payslip_id) REFERENCES payslips(payslip_id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS daily_quotes (
    quote_id INT AUTO_INCREMENT PRIMARY KEY,
    quote_text TEXT NOT NULL,
    quote_author VARCHAR(100) DEFAULT 'Anonymous',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
