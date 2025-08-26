<?php
header('Content-Type: application/json');
require_once '../config/db.php';

// Get parameters
$dept_id = isset($_GET['dept_id']) ? intval($_GET['dept_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 5;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Create monthly attendance table if not exists
$tableName = "attendance_" . strtolower(date('F_Y', strtotime($date)));
$conn->query("
CREATE TABLE IF NOT EXISTS `$tableName` (
    emp_id VARCHAR(20) NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('P','A','OL') DEFAULT 'A',
    PRIMARY KEY(emp_id, attendance_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// WHERE clauses
$whereDept = $dept_id ? "WHERE ec.dept_id = $dept_id" : "";
$whereSearch = $search ? ($whereDept ? " AND " : " WHERE ") . "(ep.name LIKE '%$search%' OR ep.emp_id LIKE '%$search%')" : "";

// Count total employees
$totalResult = $conn->query("
SELECT COUNT(*) as total
FROM employees_personal ep
INNER JOIN employees_company ec ON ep.emp_id = ec.emp_id
$whereDept $whereSearch
");
$total = $totalResult->fetch_assoc()['total'];

// Pagination
$offset = ($page-1) * $per_page;

// Fetch employees with LEFT JOIN to attendance table
$sql = "
SELECT ep.emp_id, ep.name, COALESCE(a.status,'A') AS status
FROM employees_personal ep
INNER JOIN employees_company ec ON ep.emp_id = ec.emp_id
LEFT JOIN `$tableName` a ON ep.emp_id = a.emp_id AND a.attendance_date = '$date'
$whereDept $whereSearch
ORDER BY ep.name ASC
LIMIT $per_page OFFSET $offset
";

$result = $conn->query($sql);
$employees = [];
while($row = $result->fetch_assoc()){
    $employees[] = $row;
}

echo json_encode(['employees'=>$employees,'total'=>$total]);
