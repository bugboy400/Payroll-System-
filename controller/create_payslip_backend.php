<?php
// create_payslip_backend.php
include("../config/db.php"); // Adjust path if needed
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['error' => 'No data received']);
    exit;
}

// Extract data from POST
$emp_id = $data['employee_id'] ?? '';
$dept_id = $data['department_id'] ?? '';
$year = $data['year'] ?? date('Y');
$month = $data['month'] ?? date('F');
$basic_salary = $data['basic_salary'] ?? 0;
$total_allowance = $data['total_allowance'] ?? 0;
$total_deduction = $data['total_deduction'] ?? 0;
$net_salary = $data['net_salary'] ?? 0;
$status = $data['status'] ?? 'Unpaid';

$allowances = $data['allowances'] ?? []; // Array of ['name' => '', 'amount' => '']
$deductions = $data['deductions'] ?? [];
$other_allowances = $data['other_allowances'] ?? [];
$other_deductions = $data['other_deductions'] ?? [];

// Check if employee has already paid for this month
$stmt = $conn->prepare("SELECT * FROM payslips WHERE employee_id = ? AND year = ? AND month = ?");
$stmt->bind_param("sis", $emp_id, $year, $month);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existing = $result->fetch_assoc();
    echo json_encode([
        'error' => "Salary already paid for $month $year",
        'payslip' => $existing
    ]);
    exit;
}

$conn->begin_transaction();

try {
    // Insert main payslip
    $stmt = $conn->prepare("
        INSERT INTO payslips (employee_id, dept_id, year, month, basic_salary, total_allowance, total_deduction, net_salary, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("siisddds", $emp_id, $dept_id, $year, $month, $basic_salary, $total_allowance, $total_deduction, $net_salary, $status);
    $stmt->execute();
    $payslip_id = $stmt->insert_id;
    $stmt->close();

    // Insert standard allowances
    if (!empty($allowances)) {
        $stmt = $conn->prepare("
            INSERT INTO payslip_allowances (payslip_id, allowance_name, allowance_amt)
            VALUES (?, ?, ?)
        ");
        foreach ($allowances as $allow) {
            $stmt->bind_param("isd", $payslip_id, $allow['name'], $allow['amount']);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Insert other allowances
    if (!empty($other_allowances)) {
        $stmt = $conn->prepare("
            INSERT INTO payslip_allowances (payslip_id, allowance_name, allowance_amt)
            VALUES (?, ?, ?)
        ");
        foreach ($other_allowances as $allow) {
            $stmt->bind_param("isd", $payslip_id, $allow['name'], $allow['amount']);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Insert standard deductions
    if (!empty($deductions)) {
        $stmt = $conn->prepare("
            INSERT INTO payslip_deductions (payslip_id, deduction_name, deduction_amt)
            VALUES (?, ?, ?)
        ");
        foreach ($deductions as $ded) {
            $stmt->bind_param("isd", $payslip_id, $ded['name'], $ded['amount']);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Insert other deductions
    if (!empty($other_deductions)) {
        $stmt = $conn->prepare("
            INSERT INTO payslip_deductions (payslip_id, deduction_name, deduction_amt)
            VALUES (?, ?, ?)
        ");
        foreach ($other_deductions as $ded) {
            $stmt->bind_param("isd", $payslip_id, $ded['name'], $ded['amount']);
            $stmt->execute();
        }
        $stmt->close();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Payslip saved successfully']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
