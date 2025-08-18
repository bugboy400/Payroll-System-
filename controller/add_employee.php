<?php
session_start();
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== Personal Details =====
    $name = trim($_POST['name']);
    $fatherName = trim($_POST['fatherName']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $nationality = trim($_POST['nationality']);
    $phone1 = trim($_POST['phno']);
    $phone2 = trim($_POST['phno2']);
    $localaddress = trim($_POST['localaddress']);
    $permanentaddress = trim($_POST['permanentaddress']);
    $maritalstatus = $_POST['maritalstatus'];

    // ===== Company Details =====
    $department = $_POST['dept'];       // Department NAME
    $designation = $_POST['designation']; // Designation NAME
    $dateofjoin = $_POST['dateofjoin'];
    $dateofleave = $_POST['dateofleave'];

    // ===== Financial Details =====
    $basicsal = $_POST['basicsal'] ?? 0;
    $total_sal = $_POST['totalsal'] ?? $basicsal;

    $allowance_names = $_POST['allowance'] ?? [];
    $allowance_amts = $_POST['allowanceamt'] ?? [];

    $deduction_names = $_POST['deduction'] ?? [];
    $deduction_amts = $_POST['deductionamt'] ?? [];

    // ===== Employee ID Generation =====
    $nameParts = explode(' ', $name);
    $initials = (count($nameParts) >= 2) 
        ? strtoupper($nameParts[0][0] . $nameParts[count($nameParts)-1][0])
        : strtoupper(substr($name,0,2));

    $deptInit = strtoupper(substr($department,0,2));
    $desInit = strtoupper(implode('', array_map(function($w){ return $w[0]; }, explode(' ', $designation))));

    $emp_id = '';
    $unique = false;
    while (!$unique) {
        $randomNum = str_pad(rand(1,100), 3, '0', STR_PAD_LEFT);
        $emp_id = $initials . $randomNum . $deptInit . $desInit;

        $stmt = $conn->prepare("SELECT emp_id FROM employees_personal WHERE emp_id = ?");
        $stmt->bind_param("s",$emp_id);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows === 0) $unique = true;
        $stmt->close();
    }

    // ===== Get dept_id =====
    $stmtDept = $conn->prepare("SELECT dept_id FROM departments WHERE department_name = ?");
    $stmtDept->bind_param("s", $department);
    $stmtDept->execute();
    $stmtDept->bind_result($dept_id);
    if(!$stmtDept->fetch()){
        die("Invalid department selected.");
    }
    $stmtDept->close();

    // ===== Get designation_id =====
    $stmtDes = $conn->prepare("SELECT designation_id FROM designations WHERE designation_name = ? AND dept_id = ?");
    $stmtDes->bind_param("si", $designation, $dept_id);
    $stmtDes->execute();
    $stmtDes->bind_result($designation_id);
    if(!$stmtDes->fetch()){
        die("Invalid designation selected for the department.");
    }
    $stmtDes->close();

    // ===== Insert Personal Details =====
    $stmt = $conn->prepare("INSERT INTO employees_personal (emp_id,name,fatherName,dob,gender,nationality,phone1,phone2,localaddress,permanentaddress,maritalstatus) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssssss",$emp_id,$name,$fatherName,$dob,$gender,$nationality,$phone1,$phone2,$localaddress,$permanentaddress,$maritalstatus);
    $stmt->execute();

    // ===== Insert Company Details =====
    $stmt = $conn->prepare("INSERT INTO employees_company (emp_id,dept_id,designation_id,dateofjoin,dateofleave) VALUES (?,?,?,?,?)");
    $stmt->bind_param("siiss",$emp_id,$dept_id,$designation_id,$dateofjoin,$dateofleave);
    $stmt->execute();

    // ===== Insert Financial Details =====
    $stmt = $conn->prepare("INSERT INTO employees_financial (emp_id,basicsal,total_sal) VALUES (?,?,?)");
    $stmt->bind_param("sdd",$emp_id,$basicsal,$total_sal);
    $stmt->execute();

    // ===== Insert Allowances =====
    $stmt = $conn->prepare("INSERT INTO employees_allowances (emp_id,allowance_name,allowance_amt) VALUES (?,?,?)");
    for($i=0; $i<count($allowance_names); $i++){
        $name_ = $allowance_names[$i];
        $amt_ = (float)$allowance_amts[$i];
        if(!empty($name_) && $amt_>0){
            $stmt->bind_param("ssd",$emp_id,$name_,$amt_);
            $stmt->execute();
        }
    }

    // ===== Insert Deductions =====
    $stmt = $conn->prepare("INSERT INTO employees_deductions (emp_id,deduction_name,deduction_amt) VALUES (?,?,?)");
    for($i=0; $i<count($deduction_names); $i++){
        $name_ = $deduction_names[$i];
        $amt_ = (float)$deduction_amts[$i];
        if(!empty($name_) && $amt_>0){
            $stmt->bind_param("ssd",$emp_id,$name_,$amt_);
            $stmt->execute();
        }
    }

    $_SESSION['success'] = "Employee added successfully with Employee ID: $emp_id";
    header("Location: ../pages/manageemployee.php");
    exit();
}
?>
