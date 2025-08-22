<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

// Required fields
$emp_id = $data['employee_id'] ?? '';
$dept_id = $data['department_id'] ?? '';
$year = $data['year'] ?? '';
$month = $data['month'] ?? '';
$basic_salary = $data['basic_salary'] ?? 0;
$total_allowance = $data['total_allowance'] ?? 0;
$total_deduction = $data['total_deduction'] ?? 0;
$net_salary = $data['net_salary'] ?? 0;
$status = $data['status'] ?? 'Unpaid';

if (!$emp_id || !$dept_id || !$year || !$month) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check for duplicate payment
$checkSql = "SELECT payslip_id FROM payslips WHERE employee_id=? AND month=? AND year=?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("ssi", $emp_id, $month, $year);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Payslip already exists for this month']);
    exit;
}
$stmt->close();

// Insert payslip
$insertSql = "INSERT INTO payslips (employee_id, dept_id, year, month, basic_salary, total_allowance, total_deduction, net_salary, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertSql);
$stmt->bind_param("siisdddds", $emp_id, $dept_id, $year, $month, $basic_salary, $total_allowance, $total_deduction, $net_salary, $status);
$stmt->execute();
$payslip_id = $stmt->insert_id;
$stmt->close();

// Save allowances
if (!empty($data['allowances'])) {
    $allowStmt = $conn->prepare("INSERT INTO payslip_allowances (payslip_id, title, amount) VALUES (?, ?, ?)");
    foreach ($data['allowances'] as $allow) {
        $name = $allow['name'];
        $amt = $allow['amount'];
        $allowStmt->bind_param("isd", $payslip_id, $name, $amt);
        $allowStmt->execute();
    }
    $allowStmt->close();
}

// Save deductions
if (!empty($data['deductions'])) {
    $deductStmt = $conn->prepare("INSERT INTO payslip_deductions (payslip_id, title, amount) VALUES (?, ?, ?)");
    foreach ($data['deductions'] as $deduct) {
        $name = $deduct['name'];
        $amt = $deduct['amount'];
        $deductStmt->bind_param("isd", $payslip_id, $name, $amt);
        $deductStmt->execute();
    }
    $deductStmt->close();
}

$conn->close();
echo json_encode(['success' => true, 'message' => 'Payslip created successfully']);
?>
