<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$emp_id = $_GET['emp_id'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

if(!$emp_id || !$month || !$year){
    echo json_encode(['success'=>false,'message'=>'Missing parameters']);
    exit;
}

// Check if payslip already exists
$checkSql = "SELECT payslip_id FROM payslips WHERE employee_id=? AND month=? AND year=?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ssi", $emp_id, $month, $year);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){
    echo json_encode(['success'=>true,'exists'=>true]);
} else {
    echo json_encode(['success'=>true,'exists'=>false]);
}

$stmt->close();
$conn->close();
?>
