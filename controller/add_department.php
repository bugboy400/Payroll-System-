<?php
session_start();
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $newDepartment = trim($_POST['newDepartment'] ?? "");
    $designations = $_POST['designations'] ?? [];

    if ($department === "new" && !empty($newDepartment)) {
        $department = $newDepartment; // overwrite with new dept
    }

    if (!empty($department)) {
        // Check if department already exists
        $stmt = $conn->prepare("SELECT dept_id FROM departments WHERE department_name = ?");
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $stmt->bind_result($dept_id);
        $exists = $stmt->fetch();
        $stmt->close();

        if (!$exists) {
            // Insert new department if not exists
            $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (?)");
            $stmt->bind_param("s", $department);
            if ($stmt->execute()) {
                $dept_id = $stmt->insert_id;
            }
            $stmt->close();
        }

        // Insert new designations
        if (!empty($designations)) {
            $desStmt = $conn->prepare("INSERT INTO designations (dept_id, designation_name) VALUES (?, ?)");
            foreach ($designations as $des) {
                $des = trim($des);
                if (!empty($des)) {
                    // Avoid duplicates
                    $check = $conn->prepare("SELECT designation_id FROM designations WHERE dept_id = ? AND designation_name = ?");
                    $check->bind_param("is", $dept_id, $des);
                    $check->execute();
                    $check->store_result();

                    if ($check->num_rows === 0) { 
                        $desStmt->bind_param("is", $dept_id, $des);
                        $desStmt->execute();
                    }
                    $check->close();
                }
            }
            $desStmt->close();
        }

        // Insert new designations
if(!empty($designations)){
    $desStmt = $conn->prepare("INSERT IGNORE INTO designations (dept_id, designation_name) VALUES (?, ?)");
    foreach($designations as $des){
        $des = trim($des);
        if(!empty($des)){
            $desStmt->bind_param("is", $dept_id, $des);
            $desStmt->execute();
        }
    }
    $desStmt->close();
}


        $_SESSION['success'] = "Department & designations saved successfully!";
    } else {
        $_SESSION['error'] = "Department name cannot be empty!";
    }

    header("Location: ../pages/managedepartment.php");
    exit();
}
