<?php
session_start();
include "../config/db.php";

$form_disabled = false;

// --- Check if an admin already exists ---
$result = $conn->query("SELECT COUNT(*) as admin_count FROM admins");
$row = $result->fetch_assoc();
if($row['admin_count'] >= 1){
    $form_disabled = true; // disable registration
}

if(isset($_POST['register'])){
    // If form is disabled, prevent registration
    if($form_disabled){
        $_SESSION['register_error'] = "System limit reached. Cannot register more admins.";
        header("Location: ../layouts/login.php");
        exit();
    }

    $full_name    = trim($_POST['full_name']);
    $company_name = trim($_POST['company_name']);
    $email        = trim($_POST['email']);
    $gender       = $_POST['gender'] ?? '';
    $password     = $_POST['password'];

    $errors = [];

    // Name validation
    if(!preg_match("/^[a-zA-Z\s]+$/",$full_name)){
        $errors['full_name'] = "Only letters & spaces allowed";
    }

    // Email validation
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Invalid email format";
    } else {
        $stmt=$conn->prepare("SELECT * FROM admins WHERE email=? LIMIT 1");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res=$stmt->get_result();
        if($res->num_rows>0){
            $errors['email']="Email already used. Cannot register.";
        }
    }

    // Password validation
    if(!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/",$password)){
        $errors['password']="Min 8 chars, 1 upper, 1 lower, 1 special";
    }

    if(!empty($errors)){
        $_SESSION['form_error'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../layouts/login.php");
        exit();
    }

    // Insert new admin
    $hashed = password_hash($password,PASSWORD_BCRYPT);
    $stmt=$conn->prepare("INSERT INTO admins(full_name,company_name,email,gender,password) VALUES(?,?,?,?,?)");
    $stmt->bind_param("sssss",$full_name,$company_name,$email,$gender,$hashed);
    if($stmt->execute()){
        $admin_id = $stmt->insert_id; // ✅ Get the new admin_id

        // ✅ Insert into companydetails as well
        $stmt2 = $conn->prepare("INSERT INTO companydetails (admin_id, company_name, email) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $admin_id, $company_name, $email);
        $stmt2->execute();

        $_SESSION['register_success']="Admin registered successfully";
    } else {
        $_SESSION['register_error']="Failed to register";
    }
    header("Location: ../layouts/login.php");
    exit();
}
?>
