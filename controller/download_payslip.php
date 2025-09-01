<?php
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php';

if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please login first.");
}

$employee_id = $_GET['employee_id'] ?? null;
$year = $_GET['year'] ?? null;
$month = $_GET['month'] ?? null;

if (!$employee_id || !$year || !$month) {
    die("Missing payslip parameters.");
}

// --- Fetch payslip with employee and admin info ---
$sql = "SELECT p.*, ep.name AS employee_name, d.department_name, des.designation_name,
               a.full_name AS admin_name, a.company_name, a.email
        FROM payslips p
        JOIN employees_personal ep ON ep.emp_id = p.employee_id
        JOIN employees_company ec ON ec.emp_id = ep.emp_id
        JOIN departments d ON d.dept_id = ec.dept_id
        JOIN designations des ON des.designation_id = ec.designation_id
        JOIN admins a ON a.admin_id = ?
        WHERE p.employee_id = ? AND p.year = ? AND LOWER(p.month) = LOWER(?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isis", $_SESSION['admin_id'], $employee_id, $year, $month);
$stmt->execute();
$payslip = $stmt->get_result()->fetch_assoc();
if (!$payslip) die("Payslip not found");

// --- Fetch allowances ---
$allow_sql = "SELECT * FROM payslip_allowances WHERE employee_id = ? AND LOWER(month) = LOWER(?)";
$stmt = $conn->prepare($allow_sql);
$stmt->bind_param("is", $employee_id, $month);
$stmt->execute();
$allowances = $stmt->get_result()->fetch_assoc();

// --- Fetch deductions ---
$ded_sql = "SELECT * FROM payslip_deductions WHERE employee_id = ? AND LOWER(month) = LOWER(?)";
$stmt = $conn->prepare($ded_sql);
$stmt->bind_param("is", $employee_id, $month);
$stmt->execute();
$deductions = $stmt->get_result()->fetch_assoc();

// --- Prepare allowances list ---
$allowances_list = [];
if ($allowances) {
    foreach ($allowances as $key => $value) {
        if ($key === 'employee_id' || $key === 'month') continue;
        $value = floatval($value ?? 0.00);
        if ($key === 'other_allowance') {
            $other_name = !empty($allowances['other_allowance_name']) ? $allowances['other_allowance_name'] : 'Other Allowance';
            $allowances_list[] = [
                'allowance_name' => "Other Allowance - {$other_name}",
                'allowance_amt' => $value
            ];
        } else {
            $allowances_list[] = [
                'allowance_name' => str_replace('_', ' ', ucfirst($key)),
                'allowance_amt' => $value
            ];
        }
    }
}

// --- Prepare deductions list ---
$deductions_list = [];
if ($deductions) {
    foreach ($deductions as $key => $value) {
        if ($key === 'employee_id' || $key === 'month') continue;
        $value = floatval($value ?? 0.00);
        if ($key === 'other_deduction') {
            $other_name = !empty($deductions['other_deduction_name']) ? $deductions['other_deduction_name'] : 'Other Deduction';
            $deductions_list[] = [
                'deduction_name' => "Other Deduction - {$other_name}",
                'deduction_amt' => $value
            ];
        } else {
            $deductions_list[] = [
                'deduction_name' => str_replace('_', ' ', ucfirst($key)),
                'deduction_amt' => $value
            ];
        }
    }
}

// --- Initialize mPDF ---
$mpdf = new \Mpdf\Mpdf(['format'=>'A4']);
$mpdf->SetTitle("Payslip - {$payslip['employee_name']}");
$mpdf->SetCreator($payslip['company_name']);
$date_now = date("d M Y, H:i");

// --- Build HTML ---
$html = '<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
.header { text-align: center; }
.header h1 { margin: 0; font-size: 24px; }
.header p { margin: 2px 0; font-size: 12px; }
.table-section { margin-top: 15px; }
table { width: 100%; border-collapse: collapse; margin-bottom:15px; }
th, td { border: 1px solid #000; padding: 4px; text-align: left; }
.totals { margin-top: 10px; }
.footer { text-align: center; margin-top: 25px; font-size: 12px; }
</style>';

// --- Header ---
$html .= '<div class="header">
<h1>'.htmlspecialchars($payslip['company_name']).'</h1>
<p>Email: '.htmlspecialchars($payslip['email']).'</p>
<p>Generated on: '.$date_now.'</p>
</div>';

// --- Employee Info ---
$html .= '<div class="table-section"><table>
<tr><td><strong>Employee Name:</strong> '.htmlspecialchars($payslip['employee_name']).'</td>
<td><strong>Employee ID:</strong> '.htmlspecialchars($payslip['employee_id']).'</td></tr>
<tr><td><strong>Department:</strong> '.htmlspecialchars($payslip['department_name']).'</td>
<td><strong>Designation:</strong> '.htmlspecialchars($payslip['designation_name']).'</td></tr>
<tr><td><strong>Month/Year:</strong> '.htmlspecialchars($payslip['month'].' '.$payslip['year']).'</td>
<td><strong>Status:</strong> '.htmlspecialchars($payslip['status']).'</td></tr>
<tr><td colspan="2"><strong>Created At:</strong> '.htmlspecialchars($payslip['created_at']).'</td></tr>
</table></div>';

// --- Allowances Table ---
$html .= '<div class="table-section"><h4>Allowances</h4><table><tr><th>Allowance</th><th>Amount (Rs.)</th></tr>';
foreach($allowances_list as $row){
    $amount = floatval($row['allowance_amt']);
    $html .= '<tr><td>'.htmlspecialchars($row['allowance_name']).'</td><td>'.number_format($amount,2).'</td></tr>';
}
$html .= '</table></div>';

// --- Deductions Table ---
$html .= '<div class="table-section"><h4>Deductions</h4><table><tr><th>Deduction</th><th>Amount (Rs.)</th></tr>';
foreach($deductions_list as $row){
    $amount = floatval($row['deduction_amt']);
    $html .= '<tr><td>'.htmlspecialchars($row['deduction_name']).'</td><td>'.number_format($amount,2).'</td></tr>';
}
$html .= '</table></div>';

// --- Totals ---
$total_allowance = floatval($payslip['total_allowance'] ?? 0);
$total_deduction = floatval($payslip['total_deduction'] ?? 0);
$net_salary = floatval($payslip['net_salary'] ?? 0);

$html .= '<div class="table-section totals"><table>
<tr><td><strong>Total Allowances:</strong></td><td>Rs. '.number_format($total_allowance,2).'</td></tr>
<tr><td><strong>Total Deductions:</strong></td><td>Rs. '.number_format($total_deduction,2).'</td></tr>
<tr><td><strong>Net Salary:</strong></td><td>Rs. '.number_format($net_salary,2).'</td></tr>
</table></div>';

// --- Footer ---
$html .= '<div class="footer">Payslip generated by '.htmlspecialchars($payslip['admin_name']).'</div>';

// --- Output PDF ---
$filename = 'payslip_'.$employee_id.'_'.$month.'_'.$year.'.pdf';
$mpdf->WriteHTML($html);
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.$filename.'"'); // inline display
$mpdf->Output($filename,'I');
exit;
?>
