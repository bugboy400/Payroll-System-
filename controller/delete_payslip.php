<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if (!isset($_POST['payslip_id'])) {
    echo json_encode(["success" => false, "message" => "Payslip ID missing"]);
    exit;
}

$payslip_id = intval($_POST['payslip_id']);

// Fetch payslip before deleting
$sql = "SELECT p.payslip_id, p.employee_id, e.name AS employee_name, 
               p.net_salary, p.status
        FROM payslips p
        JOIN employees_personal e ON e.emp_id = p.employee_id
        WHERE p.payslip_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payslip_id);
$stmt->execute();
$res = $stmt->get_result();
$payslip = $res->fetch_assoc();

if (!$payslip) {
    echo json_encode(["success" => false, "message" => "Payslip not found"]);
    exit;
}

// Insert into deleted_payslips
$archive_sql = "INSERT INTO deleted_payslips (payslip_id, employee_id, employee_name, net_salary, status) 
                VALUES (?, ?, ?, ?, ?)";
$archive_stmt = $conn->prepare($archive_sql);
$archive_stmt->bind_param("iisis", $payslip['payslip_id'], $payslip['employee_id'], $payslip['employee_name'], $payslip['net_salary'], $payslip['status']);
$archive_stmt->execute();

// Delete from main table
$del_sql = "DELETE FROM payslips WHERE payslip_id = ?";
$del_stmt = $conn->prepare($del_sql);
$del_stmt->bind_param("i", $payslip_id);
$del_stmt->execute();

echo json_encode(["success" => true, "message" => "Payslip deleted successfully"]);
