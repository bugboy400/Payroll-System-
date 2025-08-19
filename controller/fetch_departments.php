<?php
include("../config/db.php");

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;

$offset = ($page - 1) * $entries;
$like = "%{$search}%";

// First, get department IDs with pagination
$dept_sql = "
    SELECT d.dept_id, d.department_name
    FROM departments d
    WHERE d.department_name LIKE ?
    ORDER BY d.department_name ASC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($dept_sql);
$stmt->bind_param("sii", $like, $entries, $offset);
$stmt->execute();
$dept_result = $stmt->get_result();

$departments = [];
$dept_ids = [];
while ($row = $dept_result->fetch_assoc()) {
    $dept_id = $row['dept_id'];
    $departments[$dept_id] = [
        'dept_id' => $dept_id,
        'department_name' => $row['department_name'],
        'designations' => [],
        'employee_counts' => []
    ];
    $dept_ids[] = $dept_id;
}
$stmt->close();

if (!empty($dept_ids)) {
    $ids_placeholder = implode(',', $dept_ids);

    // Get designations and employee counts for these departments
    $sql = "
        SELECT d.dept_id, des.designation_name, COUNT(ec.emp_id) AS emp_count
        FROM departments d
        LEFT JOIN designations des ON d.dept_id = des.dept_id
        LEFT JOIN employees_company ec ON ec.dept_id = d.dept_id AND ec.designation_id = des.designation_id
        WHERE d.dept_id IN ($ids_placeholder)
        GROUP BY d.dept_id, des.designation_id
        ORDER BY des.designation_name ASC
    ";

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $dept_id = $row['dept_id'];
        $departments[$dept_id]['designations'][] = $row['designation_name'];
        $departments[$dept_id]['employee_counts'][] = $row['emp_count'];
    }
}

// Re-index departments
$departments = array_values($departments);

// Count total departments for pagination
$count_sql = "SELECT COUNT(*) AS total FROM departments WHERE department_name LIKE ?";
$stmt_count = $conn->prepare($count_sql);
$stmt_count->bind_param("s", $like);
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total = $count_result->fetch_assoc()['total'];
$stmt_count->close();

// Output JSON
header('Content-Type: application/json');
echo json_encode([
    "departments" => $departments,
    "total" => $total
]);
?>
