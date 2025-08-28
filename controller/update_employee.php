<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: ../layouts/login.php");
    exit();
}

include("../config/db.php");

// Check if form submitted
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $emp_id = $_POST['emp_id'];

   // ===== Handle Photo Upload =====
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $uploadDir = "../uploads/employees/"; // make sure this folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Allowed image types
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    if (in_array($_FILES['photo']['type'], $allowedTypes)) {

        // ✅ Get old photo path
        $stmtOld = $conn->prepare("SELECT photo FROM employees_personal WHERE emp_id=?");
        $stmtOld->bind_param("s", $emp_id);
        $stmtOld->execute();
        $stmtOld->bind_result($oldPhoto);
        $stmtOld->fetch();
        $stmtOld->close();

        // Determine file name to use
        if (!empty($oldPhoto) && $oldPhoto !== "uploads/employees/default.png") {
            $fileName = basename($oldPhoto); // reuse existing file name
        } else {
            $fileExt = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = $emp_id . "." . $fileExt; // new file name based on emp_id
        }

        $fileTmp = $_FILES['photo']['tmp_name'];
        $filePath = $uploadDir . $fileName;
        $dbPath = "uploads/employees/" . $fileName;

        // Move new file and replace old one
        if (move_uploaded_file($fileTmp, $filePath)) {
            // Update DB with same path
            $stmt = $conn->prepare("UPDATE employees_personal SET photo=? WHERE emp_id=?");
            $stmt->bind_param("ss", $dbPath, $emp_id);
            $stmt->execute();
            $stmt->close();

            // ✅ Delete old file if different (not default)
            if (!empty($oldPhoto) && file_exists("../" . $oldPhoto) && $oldPhoto !== $dbPath && $oldPhoto !== "uploads/employees/default.png") {
                unlink("../" . $oldPhoto);
            }
        }
    }
}


    // ===== Personal Details =====
    $name = $_POST['name'];
    $fatherName = $_POST['fatherName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $nationality = $_POST['nationality'];
    $maritalstatus = $_POST['maritalstatus'];
    $phone1 = $_POST['phone1'];
    $phone2 = $_POST['phone2'];
    $localaddress = $_POST['localaddress'];
    $permanentaddress = $_POST['permanentaddress'];

    $stmt = $conn->prepare("UPDATE employees_personal SET name=?, fatherName=?, dob=?, gender=?, nationality=?, maritalstatus=?, phone1=?, phone2=?, localaddress=?, permanentaddress=? WHERE emp_id=?");
    $stmt->bind_param("sssssssssss", $name, $fatherName, $dob, $gender, $nationality, $maritalstatus, $phone1, $phone2, $localaddress, $permanentaddress, $emp_id);
    $stmt->execute();

    // ===== Company Details =====
    $dept_id = $_POST['dept_id'];
    $designation_id = $_POST['designation_id'];
    $dateofjoin = $_POST['dateofjoin'];
    $dateofleave = $_POST['dateofleave'];

    $stmt = $conn->prepare("INSERT INTO employees_company (emp_id, dept_id, designation_id, dateofjoin, dateofleave) 
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE dept_id=?, designation_id=?, dateofjoin=?, dateofleave=?");
    $stmt->bind_param("sisssisss", $emp_id, $dept_id, $designation_id, $dateofjoin, $dateofleave, $dept_id, $designation_id, $dateofjoin, $dateofleave);
    $stmt->execute();

    // ===== Financial Details =====
    $basicsal = $_POST['basicsal'];
    $total_sal = $_POST['total_sal'];

    $stmt = $conn->prepare("INSERT INTO employees_financial (emp_id, basicsal, total_sal)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE basicsal=?, total_sal=?");
    $stmt->bind_param("sdddd", $emp_id, $basicsal, $total_sal, $basicsal, $total_sal);
    $stmt->execute();

    // ===== Allowances =====
    $allowance_ids = $_POST['allowance_id'];
    $allowance_names = $_POST['allowance_name'];
    $allowance_amts = $_POST['allowance_amt'];

    foreach($allowance_names as $index => $name){
        $amt = $allowance_amts[$index];
        $id = $allowance_ids[$index];

        if(!empty($id)){
            // Update existing
            $stmt = $conn->prepare("UPDATE employees_allowances SET allowance_name=?, allowance_amt=? WHERE id=? AND emp_id=?");
            $stmt->bind_param("sdss", $name, $amt, $id, $emp_id);
            $stmt->execute();
        } else {
            // Insert new
            $stmt = $conn->prepare("INSERT INTO employees_allowances (emp_id, allowance_name, allowance_amt) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $emp_id, $name, $amt);
            $stmt->execute();
        }
    }

    // ===== Deductions =====
    $deduction_ids = $_POST['deduction_id'];
    $deduction_names = $_POST['deduction_name'];
    $deduction_amts = $_POST['deduction_amt'];

    foreach($deduction_names as $index => $name){
        $amt = $deduction_amts[$index];
        $id = $deduction_ids[$index];

        if(!empty($id)){
            // Update existing
            $stmt = $conn->prepare("UPDATE employees_deductions SET deduction_name=?, deduction_amt=? WHERE id=? AND emp_id=?");
            $stmt->bind_param("sdss", $name, $amt, $id, $emp_id);
            $stmt->execute();
        } else {
            // Insert new
            $stmt = $conn->prepare("INSERT INTO employees_deductions (emp_id, deduction_name, deduction_amt) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $emp_id, $name, $amt);
            $stmt->execute();
        }
    }

    $_SESSION['success'] = "Employee details updated successfully!";
    header("Location: ../pages/employeedetails.php?emp_id=".$emp_id);
    exit();
}
?>
