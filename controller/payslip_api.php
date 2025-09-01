<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$year   = $_GET['year']   ?? date('Y');
$month  = $_GET['month']  ?? date('F');
$dept   = $_GET['dept']   ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT p.employee_id, e.name AS employee_name,
               p.year, p.month, p.basic_salary,
               p.total_allowance, p.total_deduction,
               p.net_salary, p.status, p.created_at
        FROM payslips p
        JOIN employees_personal e ON e.emp_id = p.employee_id
        JOIN employees_company ec ON ec.emp_id = e.emp_id
        WHERE p.year = ? AND LOWER(p.month) = LOWER(?)";

$params = [$year, $month];
$types  = "is";

// department filter
if ($dept !== '') {
    $sql .= " AND ec.dept_id = ?";
    $params[] = $dept;
    $types   .= "i";
}

// search filter
if ($search !== '') {
    $sql .= " AND (e.emp_id LIKE ? OR e.name LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types   .= "ss";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($data);
