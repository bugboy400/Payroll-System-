<?php
session_start();
require_once '../config/db.php';

$token=$_GET['token']??'';
if(!$token) die("Invalid reset link.");

$stmt=$conn->prepare("SELECT admin_id, reset_expires FROM admins WHERE reset_token=?");
$stmt->bind_param("s",$token);
$stmt->execute();
$res=$stmt->get_result();
if($res->num_rows===0) die("Invalid or expired token.");

$admin=$res->fetch_assoc();
if(strtotime($admin['reset_expires'])<time()) die("Reset link expired.");

if(isset($_POST['reset'])){
    $password=$_POST['password'];
    $confirm=$_POST['confirm-password'];
    if($password!==$confirm){
        $error="Passwords do not match.";
    }else{
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $stmt=$conn->prepare("UPDATE admins SET password=?, reset_token=NULL, reset_expires=NULL WHERE admin_id=?");
        $stmt->bind_param("si",$hash,$admin['admin_id']);
        $stmt->execute();
        $_SESSION['reset_success']="Password updated successfully. Login now.";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
body{font-family:Arial,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;background:#f0f0f0;}
.card{background:#fff;padding:2rem;border-radius:10px;width:400px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
input{width:100%;padding:0.6rem;margin-bottom:1rem;border-radius:6px;border:1px solid #ccc;}
button{width:100%;padding:0.7rem;background:#28a745;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold;}
button:hover{background:#218838;}
.password-wrapper{position:relative;}
.password-wrapper i{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;}
</style>
</head>
<body>
<div class="card">
<h2>Reset Password</h2>
<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="POST">
<div class="password-wrapper">
<input type="password" name="password" id="password" placeholder="New Password" required>
<i id="toggle-password" class="fa-solid fa-eye-slash"></i>
</div>
<div class="password-wrapper">
<input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>
<i id="toggle-confirm" class="fa-solid fa-eye-slash"></i>
</div>
<button type="submit" name="reset">Update Password</button>
</form>
</div>
<script>
function togglePassword(toggleId,inputId){const toggle=document.getElementById(toggleId);const input=document.getElementById(inputId);toggle.addEventListener('click',()=>{if(input.type==="password"){input.type="text";toggle.classList.replace("fa-eye-slash","fa-eye");}else{input.type="password";toggle.classList.replace("fa-eye","fa-eye-slash");}});}
togglePassword("toggle-password","password");
togglePassword("toggle-confirm","confirm-password");
</script>
</body>
</html>
