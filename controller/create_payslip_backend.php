<?php
header('Content-Type: application/json');

// === Prevent HTML errors in JSON API ===
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

include '../config/db.php'; // mysqli connection $conn

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) throw new Exception('Invalid JSON');

    // Required fields
    $required = [
        'emp_id', 'dept_id', 'year', 'month',
        'basic_salary', 'total_allowance', 'total_deduction',
        'net_salary', 'status', 'allowances', 'deductions'
    ];
    foreach ($required as $field) {
        if (!isset($input[$field])) throw new Exception("Missing field: $field");
    }

    // Assign variables safely
    $employee_id     = $input['emp_id'];
    $dept_id         = intval($input['dept_id']);
    $year            = intval($input['year']);
    $month           = trim($input['month']);
    $basic_salary    = floatval($input['basic_salary']);
    $total_allowance = floatval($input['total_allowance']);
    $total_deduction = floatval($input['total_deduction']);
    $net_salary      = floatval($input['net_salary']);
    $status          = trim($input['status']);
    $allowances      = (array) $input['allowances'];
    $deductions      = (array) $input['deductions'];

    $conn->begin_transaction();

    // Check duplicate payslip
    $stmt = $conn->prepare("SELECT employee_id FROM payslips WHERE employee_id=? AND month=? AND year=?");
    $stmt->bind_param("ssi", $employee_id, $month, $year);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        throw new Exception("Payslip already exists for $month $year");
    }
    $stmt->close();

    // Insert into payslips
    $stmt = $conn->prepare("
        INSERT INTO payslips 
        (employee_id, dept_id, year, month, basic_salary, total_allowance, total_deduction, net_salary, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param(
        "siisdddds",
        $employee_id, $dept_id, $year, $month,
        $basic_salary, $total_allowance, $total_deduction,
        $net_salary, $status
    );
    if (!$stmt->execute()) throw new Exception("Payslip insert failed: " . $stmt->error);
    $stmt->close();

    // ===== Allowances (Single Row Insert) =====
    $home      = floatval($allowances['homeallowance'] ?? 0);
    $health    = floatval($allowances['healthallowance'] ?? 0);
    $overtime  = floatval($allowances['overtimeallowance'] ?? 0);
    $festive   = floatval($allowances['festiveallowance'] ?? 0);
    $other     = floatval($allowances['other'] ?? 0);
    $other_name = !empty($allowances['other_allowance_name']) ? $allowances['other_allowance_name'] : null;

    $stmt = $conn->prepare("
        INSERT INTO payslip_allowances
        (employee_id, month, home_allowance, health_allowance, overtime_allowance, festive_allowance, other_allowance, other_allowance_name)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssddddds",
        $employee_id,
        $month,
        $home,
        $health,
        $overtime,
        $festive,
        $other,
        $other_name
    );
    if (!$stmt->execute()) throw new Exception("Allowance insert failed: " . $stmt->error);
    $stmt->close();

    // ===== Deductions =====
    $pf = $tax = $leave = $otherDed = 0;
    $pf_percent = 10;
    $tax_percent = 0;
    $other_ded_name = null;

    foreach ($deductions as $d) {
        $name = strtolower(trim($d['name'] ?? ''));
        $amount = floatval($d['amount'] ?? 0);

        if ($name === 'provident fund' || $name === 'providentfund' || $name === 'pf') {
            $pf = $amount;
            if (isset($d['pf_percent'])) $pf_percent = floatval($d['pf_percent']);
        } elseif ($name === 'tax') {
            $tax = $amount;
            if (isset($d['tax_percent'])) $tax_percent = floatval($d['tax_percent']);
        } elseif ($name === 'leave') {
            $leave = $amount;
        } elseif ($name === 'other') {
            $otherDed = $amount;
            if (!empty($d['other_name'])) $other_ded_name = trim($d['other_name']);
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO payslip_deductions
        (employee_id, month, provident_fund, `leave`, tax, other_deduction, other_deduction_name, pf_percent, tax_percent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssddddsdd",
        $employee_id, $month,
        $pf, $leave, $tax, $otherDed,
        $other_ded_name, $pf_percent, $tax_percent
    );
    if (!$stmt->execute()) throw new Exception("Deduction insert failed: " . $stmt->error);
    $stmt->close();

    $conn->commit();

    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Payslip created successfully',
        'debug' => [
            'allowances' => $allowances,
            'deductions' => [
                'pf' => $pf,
                'pf_percent' => $pf_percent,
                'tax' => $tax,
                'tax_percent' => $tax_percent,
                'leave' => $leave,
                'other_ded' => $otherDed,
                'other_ded_name' => $other_ded_name
            ]
        ]
    ]);
    exit;

} catch (Exception $e) {
    if ($conn) $conn->rollback();
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
