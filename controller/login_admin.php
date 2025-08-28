<?php
session_start();
require_once '../config/db.php';

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_id, full_name, email, password FROM admins WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res && $res->num_rows===1){
        $admin = $res->fetch_assoc();
        if(password_verify($password, $admin['password'])){
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['full_name'] = $admin['full_name'];
            $_SESSION['email'] = $admin['email'];
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            $_SESSION['form_error'] = ['password' => 'Invalid password'];
            $_SESSION['form_data'] = ['email'=>$email];
        }
    } else {
        $_SESSION['form_error'] = ['email' => 'No account with this email'];
        $_SESSION['form_data'] = ['email'=>$email];
    }
    header("Location: ../layouts/login.php");
    exit();
}
?>
