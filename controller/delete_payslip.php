<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_POST['employee_id']) || !isset($_POST['month']) || !isset($_POST['year'])) {
    echo json_encode(["success" => false, "message" => "Employee ID, month, or year missing"]);
    exit;
}

$employee_id = intval($_POST['employee_id']);
$month       = strtolower($_POST['month']);
$year        = intval($_POST['year']);

// Fetch the payslip first
$sql = "SELECT p.employee_id, e.name AS employee_name, 
               p.net_salary, p.status
        FROM payslips p
        JOIN employees_personal e ON e.emp_id = p.employee_id
        WHERE p.employee_id = ? AND LOWER(p.month) = ? AND p.year = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $employee_id, $month, $year);
$stmt->execute();
$res = $stmt->get_result();
$payslip = $res->fetch_assoc();

if (!$payslip) {
    echo json_encode(["success" => false, "message" => "Payslip not found"]);
    exit;
}

// Archive into deleted_payslips
$archive_sql = "INSERT INTO deleted_payslips (employee_id, employee_name, net_salary, status, month, year, deleted_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
$archive_stmt = $conn->prepare($archive_sql);
$archive_stmt->bind_param(
    "issssi",
    $payslip['employee_id'],
    $payslip['employee_name'],
    $payslip['net_salary'],
    $payslip['status'],
    $month,
    $year
);
$archive_stmt->execute();

// Delete from main table
$del_sql = "DELETE FROM payslips WHERE employee_id = ? AND LOWER(month) = ? AND year = ?";
$del_stmt = $conn->prepare($del_sql);
$del_stmt->bind_param("isi", $employee_id, $month, $year);
$del_stmt->execute();

echo json_encode(["success" => true, "message" => "Payslip deleted successfully"]);
