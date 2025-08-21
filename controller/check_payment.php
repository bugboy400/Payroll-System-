<?php
include("../config/db.php");
header('Content-Type: application/json');

$emp_id = $_GET['emp_id'] ?? '';
if(!$emp_id){ echo json_encode(['already_paid'=>false, 'payments'=>[]]); exit; }

// Check existing payslips
$sql = "SELECT month, year, basic_salary, total_allowance, total_deduction, net_salary, created_at 
        FROM payslips WHERE employee_id=? ORDER BY year DESC, MONTH(created_at) DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

$payments = [];
while($row=$result->fetch_assoc()) $payments[] = $row;
$stmt->close();

echo json_encode([
    'already_paid'=>count($payments)>0,
    'payments'=>$payments,
    'basic_salary'=>0 // You can fetch default basic salary if needed
]);
?>
