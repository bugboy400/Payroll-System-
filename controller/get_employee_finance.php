<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$emp_id = $_GET['emp_id'] ?? '';
if (!$emp_id) {
    echo json_encode(['error' => 'Employee ID required']);
    exit;
}

// Fetch basic salary
$stmt = $conn->prepare("SELECT basicsal FROM employees_financial WHERE emp_id=?");
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$basic_salary = $employee['basicsal'] ?? 0;

// Fetch allowances
$allowances = [];
$stmt2 = $conn->prepare("SELECT allowance_name, allowance_amt FROM employees_allowances WHERE emp_id=?");
$stmt2->bind_param("s", $emp_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $allowances[] = ['name' => $row['allowance_name'], 'amount' => (float)$row['allowance_amt']];
}

// Fetch deductions
$deductions = [];
$stmt3 = $conn->prepare("SELECT deduction_name, deduction_amt FROM employees_deductions WHERE emp_id=?");
$stmt3->bind_param("s", $emp_id);
$stmt3->execute();
$res3 = $stmt3->get_result();
while ($row = $res3->fetch_assoc()) {
    $deductions[] = ['name' => $row['deduction_name'], 'amount' => (float)$row['deduction_amt']];
}

// Return JSON
$response = [
    'basic_salary' => (float)$basic_salary,
    'allowances' => $allowances,
    'deductions' => $deductions
];

echo json_encode($response);
$conn->close();
?>
