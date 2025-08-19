<?php
include("../config/db.php");

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;

$offset = ($page - 1) * $entries;
$like = "%{$search}%";

// ✅ Main query with joins and pagination
$sql = "
    SELECT ep.emp_id, ep.name, ep.phone1, d.department_name, des.designation_name
    FROM employees_personal ep
    INNER JOIN employees_company ec ON ep.emp_id = ec.emp_id
    INNER JOIN departments d ON ec.dept_id = d.dept_id
    INNER JOIN designations des ON ec.designation_id = des.designation_id
    WHERE ep.name LIKE ? 
       OR ep.emp_id LIKE ? 
       OR d.department_name LIKE ? 
       OR des.designation_name LIKE ?
    ORDER BY ep.name ASC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssii", $like, $like, $like, $like, $entries, $offset);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];
while ($row = $result->fetch_assoc()) {
    // Add actions for frontend
    $row['actions'] = "
        <button class='btn view' data-id='{$row['emp_id']}'>View</button>
        <button class='btn edit' data-id='{$row['emp_id']}'>Edit</button>
        <button class='btn delete' data-id='{$row['emp_id']}'>Delete</button>
    ";
    $employees[] = $row;
}
$stmt->close();

// ✅ Count total records for pagination
$count_sql = "
    SELECT COUNT(*) as total
    FROM employees_personal ep
    INNER JOIN employees_company ec ON ep.emp_id = ec.emp_id
    INNER JOIN departments d ON ec.dept_id = d.dept_id
    INNER JOIN designations des ON ec.designation_id = des.designation_id
    WHERE ep.name LIKE ? 
       OR ep.emp_id LIKE ? 
       OR d.department_name LIKE ? 
       OR des.designation_name LIKE ?
";

$stmt_count = $conn->prepare($count_sql);
$stmt_count->bind_param("ssss", $like, $like, $like, $like);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total = $count_result->fetch_assoc()['total'];
$stmt_count->close();

// ✅ Output JSON
header('Content-Type: application/json');
echo json_encode([
    "employees" => $employees,
    "total" => $total
]);
?>
