<?php
require_once '../config/db.php';
require_once '../vendor/autoload.php';

if (!isset($_POST['payslip_id'])) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Payslip ID missing"]);
    exit;
}

$payslip_id = intval($_POST['payslip_id']);

// Fetch payslip data
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

if (!$payslip || empty($payslip['employee_id']) || $payslip['employee_id'] == 0) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Payslip or valid Employee ID not found"]);
    exit;
}

// Initialize MPDF
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->SetCreator('Payroll System');
$mpdf->SetTitle('Payslip ' . $payslip_id);

// Simple payslip content
$html = '
<style>
    h2 { text-align: center; font-family: Arial; }
    p { font-family: Arial; font-size: 12px; }
</style>
<h2>Payslip</h2>
<p><strong>Payslip ID:</strong> ' . htmlspecialchars($payslip['payslip_id']) . '</p>
<p><strong>Employee ID:</strong> ' . htmlspecialchars($payslip['employee_id']) . '</p>
<p><strong>Employee Name:</strong> ' . htmlspecialchars($payslip['employee_name']) . '</p>
<p><strong>Net Salary:</strong> $' . number_format($payslip['net_salary'], 2) . '</p>
<p><strong>Status:</strong> ' . htmlspecialchars($payslip['status']) . '</p>';

$mpdf->WriteHTML($html);
$mpdf->Output('payslip_' . $payslip_id . '.pdf', 'D');
?>