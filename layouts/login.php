<?php
session_start();
include "../config/db.php";

// --- Check if an admin already exists ---
$result = $conn->query("SELECT COUNT(*) as admin_count FROM admins");
$row = $result->fetch_assoc();
$form_disabled = ($row['admin_count'] >= 1);

// ✅ Handle messages
$successMessage = $_SESSION['register_success'] ?? '';
$errorMessage   = $_SESSION['login_error'] ?? '';
$forgotSuccess  = $_SESSION['forgot_success'] ?? '';
$forgotError    = $_SESSION['forgot_error'] ?? '';
$formData       = $_SESSION['form_data'] ?? [];

unset($_SESSION['register_success'], $_SESSION['login_error'], $_SESSION['forgot_success'], $_SESSION['forgot_error'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login/Register</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="../includes/auth.css">
</head>
<body>
<div class="form-wrapper">

<!-- Login Form -->
<div id="loginForm" class="active">
    <?php if($successMessage) echo "<div class='success-message message-auto-hide'>$successMessage</div>"; ?>
    <?php if($errorMessage) echo "<div class='error-message message-auto-hide'>$errorMessage</div>"; ?>
    <?php if($forgotSuccess) echo "<div class='success-message message-auto-hide'>$forgotSuccess</div>"; ?>
    <?php if($forgotError) echo "<div class='error-message message-auto-hide'>$forgotError</div>"; ?>

    <h2>Login</h2>
    <form method="POST" action="../controller/login_admin.php" id="loginFormElement">
        <div class="form-row">
            <label>E-mail:</label>
            <input type="email" name="email" placeholder="example@gmail.com" required value="<?= $formData['email'] ?? '' ?>">
            <div class="form-error"></div>
        </div>
        <div class="form-row password-wrapper">
            <label>Password:</label>
            <input type="password" id="login-password" name="password" placeholder="Password" required>
            <i id="toggle-login-password" class="fa-solid fa-eye-slash"></i>
            <div class="form-error"></div>
        </div>
        <button type="submit" name="login">Login</button>
        <p><span class="toggle-link" onclick="showForgot()">Forgot Password?</span></p>
        <p>Don't have an account? <span class="toggle-link" onclick="showRegister()">Register here</span></p>
    </form>
</div>

<!-- Register Form -->
<div id="registerForm">
    <h2>Register</h2>

    <?php if($form_disabled): ?>
        <div class="error-message">
            System limit reached. Only one admin/company allowed.
        </div>
        <div style="text-align:center; margin-top:10px;">
            <button type="button" onclick="showLogin()" style="padding:0.5rem 1rem; border:none; border-radius:6px; background:#4ecca3; color:#fff; cursor:pointer; font-weight:bold;">
                Return to Login
            </button>
        </div>
    <?php else: ?>
        <form method="POST" action="../controller/register_admin.php" id="registerFormElement">
            <div class="form-row">
                <label>Full Name:</label>
                <input type="text" name="full_name" placeholder="Your full name" required value="<?= $formData['full_name'] ?? '' ?>">
                <div class="form-error"></div>
            </div>
            <div class="form-row">
                <label>Company:</label>
                <input type="text" name="company_name" placeholder="Your company name" required value="<?= $formData['company_name'] ?? '' ?>">
                <div class="form-error"></div>
            </div>
            <div class="form-row">
                <label>E-mail:</label>
                <input type="email" name="email" placeholder="example@gmail.com" required value="<?= $formData['email'] ?? '' ?>">
                <div class="form-error"></div>
            </div>
            <div class="form-row">
                <label>Gender:</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Male" <?= (isset($formData['gender']) && $formData['gender']=='Male')?'checked':'' ?> required> Male</label>
                    <label><input type="radio" name="gender" value="Female" <?= (isset($formData['gender']) && $formData['gender']=='Female')?'checked':'' ?>> Female</label>
                </div>
                <div class="form-error"></div>
            </div>
            <div class="form-row password-wrapper">
                <label>Password:</label>
                <input type="password" id="reg-password" name="password" placeholder="Password" required>
                <i id="toggle-reg-password" class="fa-solid fa-eye-slash"></i>
                <div class="form-error"></div>
            </div>
            <button type="submit" name="register">Register</button>
        </form>
    <?php endif; ?>

</div>


<!-- Forgot Password Form -->
<div id="forgotForm">
    <h2>Forgot Password</h2>
    <form method="POST" action="../controller/forgot_password.php" id="forgotFormElement">
        <div class="form-row">
            <label>E-mail:</label>
            <input type="email" name="email" placeholder="example@gmail.com" required value="<?= $formData['email'] ?? '' ?>">
            <div class="form-error"></div>
        </div>
        <button type="submit" name="forgot">Send Reset Link</button>
        <p>Remembered? <span class="toggle-link" onclick="showLogin()">Login here</span></p>
    </form>
</div>

</div>
<script src="../includes/auth.js"></script>
</body>
</html>
