-- SQL file to create necessary tables for payroll system

-- Department table
-- This table stores department information
CREATE TABLE departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE
);

-- table for designations
CREATE TABLE designations (
    designation_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_id INT NOT NULL,
    designation_name VARCHAR(100) NOT NULL,
    UNIQUE(dept_id, designation_name),  -- ✅ No duplicate designations within same dept
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE
);


-- Personal Details
CREATE TABLE employees_personal (
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
CREATE TABLE employees_company (
    emp_id VARCHAR(20) PRIMARY KEY,
    department VARCHAR(50),
    designation VARCHAR(50),
    dateofjoin DATE,
    dateofleave DATE,
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id)
);

-- Financial Details
CREATE TABLE employees_financial (
    emp_id VARCHAR(20) PRIMARY KEY,
    basicsal DECIMAL(10,2),
    total_sal DECIMAL(10,2),
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id)
);

-- Allowances Table
CREATE TABLE employees_allowances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id VARCHAR(20),
    allowance_name VARCHAR(50),
    allowance_amt DECIMAL(10,2),
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id)
);

-- Deductions Table
CREATE TABLE employees_deductions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id VARCHAR(20),
    deduction_name VARCHAR(50),
    deduction_amt DECIMAL(10,2),
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id)
);


-- Drop old table if exists
DROP TABLE IF EXISTS employees_company;

-- Recreate with dept_id and designation_id as foreign keys
CREATE TABLE employees_company (
    emp_id VARCHAR(20) PRIMARY KEY,
    dept_id INT NOT NULL,
    designation_id INT NOT NULL,
    dateofjoin DATE,
    dateofleave DATE,
    FOREIGN KEY (emp_id) REFERENCES employees_personal(emp_id) ON DELETE CASCADE,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE CASCADE,
    FOREIGN KEY (designation_id) REFERENCES designations(designation_id) ON DELETE CASCADE
);
