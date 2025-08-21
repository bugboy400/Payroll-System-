<?php
require_once '../config/db.php'; // your DB connection

if(!isset($_GET['emp_id'])) {
    echo json_encode(['error' => 'Employee ID required']);
    exit;
}

$emp_id = $_GET['emp_id'];

// Fetch basic salary
$stmt = $conn->prepare("SELECT basic_salary FROM employees WHERE emp_id=?");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

// Fetch allowances
$allowances = [];
$stmt2 = $conn->prepare("SELECT allowance_name, amount FROM allowances WHERE emp_id=?");
$stmt2->bind_param("i", $emp_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while($row = $res2->fetch_assoc()){
    $allowances[] = $row;
}

// Fetch deductions
$deductions = [];
$stmt3 = $conn->prepare("SELECT deduction_name, amount FROM deductions WHERE emp_id=?");
$stmt3->bind_param("i", $emp_id);
$stmt3->execute();
$res3 = $stmt3->get_result();
while($row = $res3->fetch_assoc()){
    $deductions[] = $row;
}

// Combine data
$response = [
    'basic_salary' => $employee['basic_salary'] ?? 0,
    'allowances' => $allowances,
    'deductions' => $deductions
];

echo json_encode($response);
?>
