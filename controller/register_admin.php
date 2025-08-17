<?php
include "../config/db.php"; // database connection

if (isset($_POST['register'])) {
    $full_name    = $conn->real_escape_string($_POST['full_name']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $email        = $conn->real_escape_string($_POST['email']);
    $gender       = $conn->real_escape_string($_POST['gender']);
    $password     = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $check = $conn->query("SELECT admin_id FROM admins WHERE email='$email' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $_SESSION['login_error'] = "Email already registered! Please login.";
        header("Location: ../layouts/login.php");
        exit();
    }

    // Insert new admin
    $sql = "INSERT INTO admins (full_name, company_name, email, gender, password)
            VALUES ('$full_name', '$company_name', '$email', '$gender', '$password')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['register_success'] = "Registration successful! You can now log in.";
        header("Location: ../layouts/login.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Registration failed. Please try again.";
        header("Location: ../layouts/login.php");
        exit();
    }
}
?>
