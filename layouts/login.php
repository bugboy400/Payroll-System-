<?php
// ✅ Start session
session_start();

// ✅ Handle registration success message
$successMessage = "";
if (isset($_SESSION['register_success'])) {
    $successMessage = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

// ✅ Handle login errors
$errorMessage = "";
if (isset($_SESSION['login_error'])) {
    $errorMessage = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Register & Login</title>
  <!-- ✅ Add Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: rgb(5, 5, 5);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      overflow: hidden;
    }

    .form-wrapper {
      background: rgb(223, 226, 17);
      padding: 2rem;
      border-radius: 10px;
      width: 450px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 1.2rem;
      color: #333;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .form-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-row label {
      width: 120px;
      font-weight: bold;
      color: #444;
    }

    .form-row input {
      flex: 1;
      padding: 0.6rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .radio-group {
      display: flex;
      gap: 20px;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper i {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
    }

    button {
      padding: 0.7rem;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
      margin-top: 1rem;
    }

    button:hover {
      background: #218838;
    }

    .toggle-link {
      color: #007bff;
      cursor: pointer;
      font-weight: bold;
    }

    .toggle-link:hover {
      text-decoration: underline;
    }

    #loginForm, #registerForm {
      display: none;
    }

    #registerForm.active, #loginForm.active {
      display: block;
    }

    .form-wrapper p {
      text-align: center;
      margin-top: 0.8rem;
      font-size: 14px;
      color: #555;
    }

    /* ✅ Success + Error messages */
    .success-message, .error-message {
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
      opacity: 1;
      transition: opacity 1s ease-out; /* fade effect */
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
  </style>
</head>
<body>
  <div class="form-wrapper">
    <!-- Login Form -->
    <div id="loginForm" class="active">
      <?php if (!empty($successMessage)): ?>
        <div class="success-message" id="successMessage"><?php echo $successMessage; ?></div>
      <?php endif; ?>

      <?php if (!empty($errorMessage)): ?>
        <div class="error-message" id="errorMessage"><?php echo $errorMessage; ?></div>
      <?php endif; ?>

      <h2>Login</h2>
      <form method="POST" action="../controller/login_admin.php">
        <div class="form-row">
          <label for="login-email" class="form-label">E-mail:</label>
          <input type="email" id="login-email" name="email" placeholder="example@gmail.com" required>
        </div>

        <div class="form-row password-wrapper">
          <label for="login-password" class="form-label">Password:</label>
          <input id="login-password" name="password" type="password" placeholder="Password" required>
          <i id="toggle-login-password" class="fa-solid fa-eye-slash"></i>
        </div>

        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <span class="toggle-link" onclick="showRegister()">Register here</span></p>
      </form>
    </div>

    <!-- Register Form -->
    <div id="registerForm">
      <h2>Register</h2>
      <form method="POST" action="../controller/register_admin.php">
        <div class="form-row">
          <label for="reg-name" class="form-label">Full Name:</label>
          <input type="text" id="reg-name" name="full_name" placeholder="Your full name" required>
        </div>

        <div class="form-row">
          <label for="reg-company" class="form-label">Company:</label>
          <input type="text" id="reg-company" name="company_name" placeholder="Your company name" required>
        </div>

        <div class="form-row">
          <label for="reg-email" class="form-label">E-mail:</label>
          <input type="email" id="reg-email" name="email" placeholder="example@gmail.com" required>
        </div>

        <div class="form-row">
          <label class="form-label">Gender:</label>
          <div class="radio-group">
            <label><input type="radio" name="gender" value="Male" required> Male</label>
            <label><input type="radio" name="gender" value="Female"> Female</label>
          </div>
        </div>

        <div class="form-row password-wrapper">
          <label for="reg-password" class="form-label">Password:</label>
          <input id="reg-password" type="password" name="password" placeholder="Password" required>
          <i id="toggle-reg-password" class="fa-solid fa-eye-slash"></i>
        </div>

        <button type="submit" name="register">Register</button>
        <p>Already have an account? <span class="toggle-link" onclick="showLogin()">Login here</span></p>
      </form>
    </div>
  </div>

  <script>
    function showLogin() {
      document.getElementById('registerForm').classList.remove('active');
      document.getElementById('loginForm').classList.add('active');
    }
    function showRegister() {
      document.getElementById('loginForm').classList.remove('active');
      document.getElementById('registerForm').classList.add('active');
    }

    // Toggle password visibility
    function togglePassword(toggleId, inputId) {
      const toggle = document.getElementById(toggleId);
      const input = document.getElementById(inputId);
      if (toggle) {
        toggle.addEventListener('click', function() {
          if (input.type === "password") {
            input.type = "text";
            toggle.classList.remove("fa-eye-slash");
            toggle.classList.add("fa-eye");
          } else {
            input.type = "password";
            toggle.classList.remove("fa-eye");
            toggle.classList.add("fa-eye-slash");
          }
        });
      }
    }

    togglePassword("toggle-reg-password", "reg-password");
    togglePassword("toggle-login-password", "login-password");

    // ✅ Auto-hide success/error messages after 3 seconds with fade-out
    setTimeout(() => {
      const success = document.getElementById("successMessage");
      const error = document.getElementById("errorMessage");
      if (success) success.style.opacity = "0";
      if (error) error.style.opacity = "0";
    }, 3000);
  </script>
</body>
</html>
