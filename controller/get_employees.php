<?php
include("../config/db.php");
header('Content-Type: application/json');

$dept_id = isset($_GET['dept_id']) ? intval($_GET['dept_id']) : 0;

if ($dept_id > 0) {
    $sql = "SELECT ep.emp_id, ep.name
            FROM employees_personal ep
            INNER JOIN employees_company ec ON ep.emp_id = ec.emp_id
            WHERE ec.dept_id = ?
            ORDER BY ep.name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    while($row = $result->fetch_assoc()) {
        $employees[] = [
            'emp_id' => $row['emp_id'],
            'name'   => $row['name']
        ];
    }

    $stmt->close();
    $conn->close();

    echo json_encode(['employees' => $employees]);
} else {
    echo json_encode(['employees' => []]);
}
