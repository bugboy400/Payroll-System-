<?php
include("../config/db.php");
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if(!$data) { echo json_encode(['success'=>false,'message'=>'No data received']); exit; }

$employee_id = $data['employee_id'] ?? '';
$dept_id = $data['dept_id'] ?? '';
$year = $data['year'] ?? '';
$month = $data['month'] ?? '';
$basic_salary = $data['basic_salary'] ?? 0;
$total_allowance = $data['total_allowance'] ?? 0;
$total_deduction = $data['total_deduction'] ?? 0;
$net_salary = $data['net_salary'] ?? 0;
$status = $data['status'] ?? 'Unpaid';

if(!$employee_id || !$dept_id || !$year || !$month){
    echo json_encode(['success'=>false,'message'=>'Missing required fields']);
    exit;
}

// Check duplicate
$checkSql = "SELECT payslip_id FROM payslips WHERE employee_id=? AND month=? AND year=?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ssi", $employee_id, $month, $year);
$stmt->execute(); $stmt->store_result();
if($stmt->num_rows>0){
    echo json_encode(['success'=>false,'message'=>'Payment already exists for this month']);
    exit;
}
$stmt->close();

// Insert payslip
$insertSql = "INSERT INTO payslips (employee_id, dept_id, month, year, basic_salary, total_allowance, total_deduction, net_salary, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("sissdddds", $employee_id, $dept_id, $month, $year, $basic_salary, $total_allowance, $total_deduction, $net_salary, $status);
if(!$stmt->execute()){ echo json_encode(['success'=>false,'message'=>'Error saving payslip']); exit; }
$payslip_id = $stmt->insert_id;
$stmt->close();

// Save Allowances
if(!empty($data['allowances'])){
    $allowStmt = $conn->prepare("INSERT INTO payslip_allowances (payslip_id, title, amount) VALUES (?, ?, ?)");
    foreach($data['allowances'] as $allow){
        $title = $allow['name'];
        $amt = $allow['amt'];
        $allowStmt->bind_param("isd", $payslip_id, $title, $amt);
        $allowStmt->execute();
    }
    $allowStmt->close();
}

// Save Deductions
if(!empty($data['deductions'])){
    $deductStmt = $conn->prepare("INSERT INTO payslip_deductions (payslip_id, title, amount) VALUES (?, ?, ?)");
    foreach($data['deductions'] as $deduct){
        $title = $deduct['name'];
        $amt = $deduct['amt'];
        $deductStmt->bind_param("isd", $payslip_id, $title, $amt);
        $deductStmt->execute();
    }
    $deductStmt->close();
}

$conn->close();
echo json_encode(['success'=>true,'message'=>'Payslip saved successfully']);
?>
