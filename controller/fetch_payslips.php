<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('F');
$search = isset($_GET['search']) ? $_GET['search'] : '';

$searchParam = "%$search%";

$sql = "SELECT p.payslip_id, p.employee_id, e.name AS employee_name, 
               p.basic_salary, p.total_allowance, p.total_deduction, 
               p.net_salary, p.status, p.created_at, p.month
        FROM payslips p
        JOIN employees_personal e ON e.emp_id = p.employee_id
        WHERE p.year = ? AND LOWER(p.month) = LOWER(?) 
        AND (e.emp_id LIKE ? OR e.name LIKE ?)
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $year, $month, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
?>
