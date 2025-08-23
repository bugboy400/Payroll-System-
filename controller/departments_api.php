<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$sql = "SELECT dept_id, department_name FROM departments ORDER BY department_name ASC";
$result = $conn->query($sql);

$departments = [];
while ($row = $result->fetch_assoc()) {
    $departments[] = $row;
}

echo json_encode(["departments" => $departments]);
