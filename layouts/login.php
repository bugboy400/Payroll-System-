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
/* Body & Layout */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    background: linear-gradient(135deg, #1a1a2e, #162447);
    transition: background 0.5s ease;
}

.form-wrapper {
    background: #f0f4f8;
    padding: 2.5rem;
    border-radius: 15px;
    width: 450px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.form-wrapper:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #1b1b3a;
    font-size: 2rem;
    letter-spacing: 1px;
}

/* Form Fields */
form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.form-row {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
}

.form-row label {
    width: 120px;
    font-weight: 600;
    color: #1b1b3a;
    letter-spacing: 0.5px;
}

/* Normal Inputs */
.form-row input[type="text"],
.form-row input[type="email"],
.password-wrapper input[type="password"] {
    flex: 1;
    padding: 0.65rem 0.9rem;
    border: 1px solid #bbb;
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}
.form-row input:focus {
    border-color: #4ecca3;
    box-shadow: 0 0 8px rgba(78,204,163,0.3);
    outline: none;
}

/* Password Field */
.password-wrapper {
    display: flex;
    align-items: center;
    position: relative;
    flex: 1;
}
.password-wrapper input[type="password"] {
    padding-right: 2.5rem;
}
.password-wrapper i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    font-size: 1.1rem;
    transition: color 0.3s ease, transform 0.2s ease;
}
.password-wrapper i:hover {
    color: #4ecca3;
    transform: scale(1.2);
}

/* Radio Buttons */
.radio-group {
    display: flex;
    gap: 20px;
}
.radio-group input[type="radio"] {
    accent-color: #4ecca3;
}

/* Buttons */
button {
    padding: 0.75rem;
    background: #4ecca3;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}
button:hover {
    background: #3bb78f;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78,204,163,0.4);
}

/* Toggle Links */
.toggle-link {
    color: #4ecca3;
    cursor: pointer;
    font-weight: 600;
    transition: color 0.3s ease;
}
.toggle-link:hover {
    color: #3bb78f;
    text-decoration: underline;
}

/* Form Visibility */
#loginForm, #registerForm, #forgotForm {
    display: none;
    animation: fadeIn 0.5s ease forwards;
}
#loginForm.active, #registerForm.active, #forgotForm.active {
    display: block;
}

/* Messages */
.success-message, .error-message {
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 12px;
    font-weight: 600;
    opacity: 1;
    transform: translateY(0);
    transition: opacity 0.8s ease-out, transform 0.5s ease-out;
}
.success-message {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.error-message {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
/* Center bottom links */
form p {
    display: flex;
    justify-content: center;
    margin: 0.5rem 0 0 0;
}


/* Fade Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 500px) {
    .form-wrapper {
        width: 90%;
        padding: 2rem;
    }
}

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
