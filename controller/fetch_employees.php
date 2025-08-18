<?php
include("../config/db.php");

$search = $_GET['search'] ?? '';
$page = intval($_GET['page'] ?? 1);
$entries = intval($_GET['entries'] ?? 10);
$offset = ($page - 1) * $entries;

$searchTerm = "%$search%";

// Fetch employees with department and designation
$sql = "SELECT e.emp_id, e.name, e.email, d.department_name, des.designation_name
        FROM employees_personal e
        LEFT JOIN employees_company c ON e.emp_id = c.emp_id
        LEFT JOIN departments d ON c.dept_id = d.dept_id
        LEFT JOIN designations des ON c.designation_id = des.designation_id
        WHERE e.name LIKE ? OR e.email LIKE ? OR d.department_name LIKE ? OR des.designation_name LIKE ?
        ORDER BY e.name ASC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $entries, $offset);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];
while($row = $result->fetch_assoc()){
    $employees[] = $row;
}

// Count total matching records (optional, for future pagination display)
$countSql = "SELECT COUNT(*) as total
             FROM employees_personal e
             LEFT JOIN employees_company c ON e.emp_id = c.emp_id
             LEFT JOIN departments d ON c.dept_id = d.dept_id
             LEFT JOIN designations des ON c.designation_id = des.designation_id
             WHERE e.name LIKE ? OR e.email LIKE ? OR d.department_name LIKE ? OR des.designation_name LIKE ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'];

echo json_encode([
    'employees' => $employees,
    'total' => $totalRecords
]);
?>
