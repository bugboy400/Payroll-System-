<?php
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please login first.");
}

// Get payslip ID
$payslip_id = $_POST['payslip_id'] ?? $_GET['payslip_id'] ?? null;
if (!$payslip_id) die("Payslip ID missing");
$payslip_id = intval($payslip_id);

// Fetch main payslip info
$sql = "SELECT p.*, ep.name AS employee_name, d.department_name, des.designation_name, a.full_name AS admin_name, a.company_name, a.email
        FROM payslips p
        JOIN employees_personal ep ON ep.emp_id = p.employee_id
        JOIN employees_company ec ON ec.emp_id = ep.emp_id
        JOIN departments d ON d.dept_id = ec.dept_id
        JOIN designations des ON des.designation_id = ec.designation_id
        JOIN admins a ON a.admin_id = ?
        WHERE p.payslip_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['admin_id'], $payslip_id);
$stmt->execute();
$res = $stmt->get_result();
$payslip = $res->fetch_assoc();
if (!$payslip) die("Payslip not found");

// Fetch all allowances for this employee for this month/year
$allowances_sql = "SELECT allowance_name, allowance_amt 
                   FROM employees_allowances 
                   WHERE emp_id = ?";
$stmt = $conn->prepare($allowances_sql);
$stmt->bind_param("s", $payslip['employee_id']);
$stmt->execute();
$allowances_res = $stmt->get_result();
$allowances = [];
while ($row = $allowances_res->fetch_assoc()) $allowances[] = $row;

// Fetch all deductions for this employee for this month/year
$deductions_sql = "SELECT deduction_name, deduction_amt 
                   FROM employees_deductions 
                   WHERE emp_id = ?";
$stmt = $conn->prepare($deductions_sql);
$stmt->bind_param("s", $payslip['employee_id']);
$stmt->execute();
$deductions_res = $stmt->get_result();
$deductions = [];
while ($row = $deductions_res->fetch_assoc()) $deductions[] = $row;

// Initialize mPDF
$mpdf = new \Mpdf\Mpdf(['format'=>'A4']);
$mpdf->SetTitle("Payslip - {$payslip['employee_name']}");
$mpdf->SetCreator($payslip['company_name']);
$date_now = date("d M Y, H:i");

// Build PDF HTML
$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
.header { text-align: center; }
.header h1 { margin: 0; font-size: 24px; }
.header p { margin: 2px 0; font-size: 12px; }
.employee-info { margin-top: 15px; }
.employee-info td { padding: 4px 8px; }
.salary-section { margin-top: 15px; }
.allow-ded { display: flex; justify-content: space-between; margin-top: 15px; }
.allow, .deduct { width: 48%; }
.allow table, .deduct table { width: 100%; border-collapse: collapse; }
.allow th, .allow td, .deduct th, .deduct td { border: 1px solid #000; padding: 4px; text-align: left; }
.totals { margin-top: 15px; width: 100%; border-collapse: collapse; }
.totals td { border: 1px solid #000; padding: 6px; text-align: left; }
.footer { text-align: center; margin-top: 25px; font-size: 12px; }
</style>

<div class="header">
    <h1>' . htmlspecialchars($payslip['company_name']) . '</h1>
    <p>Email: ' . htmlspecialchars($payslip['email']) . '</p>
    <p>Generated on: ' . $date_now . '</p>
</div>

<table class="employee-info">
    <tr>
        <td><strong>Employee Name:</strong> ' . htmlspecialchars($payslip['employee_name']) . '</td>
        <td><strong>Employee ID:</strong> ' . htmlspecialchars($payslip['employee_id']) . '</td>
    </tr>
    <tr>
        <td><strong>Department:</strong> ' . htmlspecialchars($payslip['department_name']) . '</td>
        <td><strong>Designation:</strong> ' . htmlspecialchars($payslip['designation_name']) . '</td>
    </tr>
</table>

<div class="salary-section">
    <p><strong>Basic Salary:</strong> Rs. ' . number_format($payslip['basic_salary'],2) . '</p>
</div>

<div class="allow-ded">
    <div class="allow">
        <h4>Allowances</h4>
        <table>
            <tr><th>Allowance</th><th>Amount (Rs.)</th></tr>';

foreach ($allowances as $row) {
    $html .= '<tr><td>' . htmlspecialchars($row['allowance_name']) . '</td><td>' . number_format($row['allowance_amt'],2) . '</td></tr>';
}

$total_allow = array_sum(array_column($allowances,'allowance_amt'));
$html .= '<tr><td><strong>Total Allowances</strong></td><td><strong>' . number_format($total_allow,2) . '</strong></td></tr>';

$html .= '</table></div>';

$html .= '<div class="deduct">
        <h4>Deductions</h4>
        <table>
            <tr><th>Deduction</th><th>Amount (Rs.)</th></tr>';

foreach ($deductions as $row) {
    $html .= '<tr><td>' . htmlspecialchars($row['deduction_name']) . '</td><td>' . number_format($row['deduction_amt'],2) . '</td></tr>';
}

$total_ded = array_sum(array_column($deductions,'deduction_amt'));
$html .= '<tr><td><strong>Total Deductions</strong></td><td><strong>' . number_format($total_ded,2) . '</strong></td></tr>';

$html .= '</table></div></div>';

// Totals section
$html .= '
<table class="totals">
    <tr>
        <td><strong>Basic Salary:</strong> Rs. ' . number_format($payslip['basic_salary'],2) . '</td>
        <td><strong>Total Allowances:</strong> Rs. ' . number_format($total_allow,2) . '</td>
    </tr>
    <tr>
        <td><strong>Total Deductions:</strong> Rs. ' . number_format($total_ded,2) . '</td>
        <td><strong>Net Salary:</strong> Rs. ' . number_format($payslip['net_salary'],2) . '</td>
    </tr>
</table>

<div class="footer">
    Payslip generated by ' . htmlspecialchars($payslip['admin_name']) . ' for ' . htmlspecialchars($payslip['month']) . ' ' . $payslip['year'] . '
</div>
';

// Output PDF
$mpdf->WriteHTML($html);
$mpdf->Output('payslip_' . $payslip_id . '.pdf', 'D');
exit;
?>
