<?php
// controller/fetchdept_payslip.php
include("../config/db.php"); // Adjust path if needed

header('Content-Type: application/json');

// Optional: get search parameter if you want to filter departments
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$like = "%{$search}%";

// Fetch departments
$sql = "SELECT dept_id, department_name FROM departments WHERE department_name LIKE ? ORDER BY department_name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = [
        'dept_id' => $row['dept_id'],
        'department_name' => $row['department_name']
    ];
}

$stmt->close();
$conn->close();

// Return JSON
echo json_encode([
    "departments" => $departments
]);
