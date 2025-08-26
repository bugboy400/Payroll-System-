<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$date = $data['date'] ?? date('Y-m-d');  // Expects YYYY-MM-DD from JS
$attendance = $data['attendance'] ?? [];

if(empty($attendance)){
    echo json_encode(['success'=>false,'error'=>'No attendance data received']);
    exit;
}

// Table name based on month/year
$tableName = "attendance_" . strtolower(date('F_Y', strtotime($date)));

// Create table if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS `$tableName` (
    emp_id VARCHAR(20) NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('P','A','OL') NOT NULL,
    PRIMARY KEY(emp_id, attendance_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
if(!$conn->query($createTableSQL)){
    echo json_encode(['success'=>false,'error'=>'Failed to create table: '.$conn->error]);
    exit;
}

// Prepare insert statement
$stmt = $conn->prepare("INSERT INTO `$tableName` (emp_id, attendance_date, status) VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE status=VALUES(status)");
if(!$stmt){
    echo json_encode(['success'=>false,'error'=>'Prepare failed: '.$conn->error]);
    exit;
}

$success = true;
$error = '';

foreach($attendance as $att){
    $emp_id = $att['emp_id'];
    $status = $att['status'];
    if(!$stmt->bind_param('sss', $emp_id, $date, $status) || !$stmt->execute()){
        $success = false;
        $error = $stmt->error;
        break;
    }
}

$stmt->close();
$conn->close();

echo json_encode(['success'=>$success,'error'=>$error]);
