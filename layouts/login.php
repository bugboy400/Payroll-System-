<?php
session_start();

// ✅ Handle messages
$successMessage = $_SESSION['register_success'] ?? '';
$errorMessage   = $_SESSION['login_error'] ?? '';
$forgotSuccess  = $_SESSION['forgot_success'] ?? '';
$forgotError    = $_SESSION['forgot_error'] ?? '';

unset($_SESSION['register_success'], $_SESSION['login_error'], $_SESSION['forgot_success'], $_SESSION['forgot_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login/Register</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#050505;}
.form-wrapper{background:#DFE211;padding:2rem;border-radius:10px;width:450px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
h2{text-align:center;margin-bottom:1.2rem;color:#333;}
form{display:flex;flex-direction:column;gap:1rem;}
.form-row{display:flex;align-items:center;gap:10px;}
.form-row label{width:120px;font-weight:bold;color:#444;}
.form-row input{flex:1;padding:0.6rem;border:1px solid #ccc;border-radius:6px;}
.radio-group{display:flex;gap:20px;}
.password-wrapper{position:relative;}
.password-wrapper i{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#555;}
button{padding:0.7rem;background:#28a745;color:#fff;border:none;border-radius:6px;font-weight:bold;cursor:pointer;transition:background 0.3s;margin-top:1rem;}
button:hover{background:#218838;}
.toggle-link{color:#007bff;cursor:pointer;font-weight:bold;}
.toggle-link:hover{text-decoration:underline;}
#loginForm,#registerForm,#forgotForm{display:none;}
#loginForm.active,#registerForm.active,#forgotForm.active{display:block;}
.success-message,.error-message{padding:10px;border-radius:5px;text-align:center;margin-bottom:10px;font-weight:bold;opacity:1;transition:opacity 1s ease-out;}
.success-message{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
.error-message{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
</style>
</head>
<body>
<div class="form-wrapper">

<!-- Login Form -->
<div id="loginForm" class="active">
    <?php if($successMessage) echo "<div class='success-message'>$successMessage</div>"; ?>
    <?php if($errorMessage) echo "<div class='error-message'>$errorMessage</div>"; ?>
    <?php if($forgotSuccess) echo "<div class='success-message'>$forgotSuccess</div>"; ?>
    <?php if($forgotError) echo "<div class='error-message'>$forgotError</div>"; ?>

    <h2>Login</h2>
    <form method="POST" action="../controller/login_admin.php">
        <div class="form-row">
            <label>E-mail:</label>
            <input type="email" name="email" placeholder="example@gmail.com" required>
        </div>
        <div class="form-row password-wrapper">
            <label>Password:</label>
            <input type="password" id="login-password" name="password" placeholder="Password" required>
            <i id="toggle-login-password" class="fa-solid fa-eye-slash"></i>
        </div>
        <button type="submit" name="login">Login</button>
        <p><span class="toggle-link" onclick="showForgot()">Forgot Password?</span></p>
        <p>Don't have an account? <span class="toggle-link" onclick="showRegister()">Register here</span></p>
    </form>
</div>

<!-- Register Form -->
<div id="registerForm">
    <h2>Register</h2>
    <form method="POST" action="../controller/register_admin.php">
        <div class="form-row">
            <label>Full Name:</label>
            <input type="text" name="full_name" placeholder="Your full name" required>
        </div>
        <div class="form-row">
            <label>Company:</label>
            <input type="text" name="company_name" placeholder="Your company name" required>
        </div>
        <div class="form-row">
            <label>E-mail:</label>
            <input type="email" name="email" placeholder="example@gmail.com" required>
        </div>
        <div class="form-row">
            <label>Gender:</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="Male" required> Male</label>
                <label><input type="radio" name="gender" value="Female"> Female</label>
            </div>
        </div>
        <div class="form-row password-wrapper">
            <label>Password:</label>
            <input type="password" id="reg-password" name="password" placeholder="Password" required>
            <i id="toggle-reg-password" class="fa-solid fa-eye-slash"></i>
        </div>
        <button type="submit" name="register">Register</button>
        <p>Already have an account? <span class="toggle-link" onclick="showLogin()">Login here</span></p>
    </form>
</div>

<!-- Forgot Password Form -->
<div id="forgotForm">
    <h2>Forgot Password</h2>
    <form method="POST" action="../controller/forgot_password.php">
        <div class="form-row">
            <label>E-mail:</label>
            <input type="email" name="email" placeholder="example@gmail.com" required>
        </div>
        <button type="submit" name="forgot">Send Reset Link</button>
        <p>Remembered? <span class="toggle-link" onclick="showLogin()">Login here</span></p>
    </form>
</div>

</div>

<script>
// Form toggle
function showForgot(){document.getElementById('loginForm').classList.remove('active');document.getElementById('registerForm').classList.remove('active');document.getElementById('forgotForm').classList.add('active');}
function showLogin(){document.getElementById('forgotForm').classList.remove('active');document.getElementById('registerForm').classList.remove('active');document.getElementById('loginForm').classList.add('active');}
function showRegister(){document.getElementById('loginForm').classList.remove('active');document.getElementById('forgotForm').classList.remove('active');document.getElementById('registerForm').classList.add('active');}

// Toggle passwords
function togglePassword(toggleId,inputId){const toggle=document.getElementById(toggleId);const input=document.getElementById(inputId);toggle.addEventListener('click',()=>{if(input.type==="password"){input.type="text";toggle.classList.replace("fa-eye-slash","fa-eye");}else{input.type="password";toggle.classList.replace("fa-eye","fa-eye-slash");}});}
togglePassword("toggle-login-password","login-password");
togglePassword("toggle-reg-password","reg-password");
</script>
</body>
</html>
