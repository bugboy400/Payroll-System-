<?php
session_start();
include "../config/db.php";

if (isset($_POST['login'])) {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $row['password'])) {
            // ✅ Added admin_id to session
            $_SESSION['admin_id']     = $row['admin_id'];  // make sure your table has `id` (or change if it's `admin_id`)
            $_SESSION['full_name']    = $row['full_name'];
            $_SESSION['email']        = $row['email'];
            $_SESSION['company_name'] = $row['company_name'];

            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password!";
        }
    } else {
        $_SESSION['login_error'] = "No account found with this email!";
    }

    header("Location: ../layouts/login.php");
    exit();
}
?>
