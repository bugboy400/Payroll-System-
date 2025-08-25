<?php
session_start();
require_once '../config/db.php';

// If no active session, redirect to login
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$page_title = "Change Password";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/changepassword.css"
];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current-password'] ?? '';
    $new = $_POST['new-password'] ?? '';
    $confirm = $_POST['confirm-password'] ?? '';

    if (empty($current) || empty($new) || empty($confirm)) {
        $message = "All fields are required.";
    } elseif ($new !== $confirm) {
        $message = "New password and confirm password do not match.";
    } elseif (strlen($new) < 6) {
        $message = "Password must be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $res = $stmt->get_result();
        $admin = $res->fetch_assoc();

        if ($admin && password_verify($current, $admin['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed, $_SESSION['email']);
            $stmt->execute();
            $message = "Password changed successfully!";
        } else {
            $message = "Current password is incorrect.";
        }
    }
}

ob_start();
?>

<div id="main-content" class="d-flex justify-content-center align-items-center bg-light border-0">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-4">Change Password</h3>

    <?php if($message): ?>
        <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3 position-relative">
            <label for="current-password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="current-password" name="current-password" required>
            <span class="toggle-btn" onclick="togglePassword('current-password')">👁️</span>
        </div>

        <div class="mb-3 position-relative">
            <label for="new-password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new-password" name="new-password" required>
            <span class="toggle-btn" onclick="togglePassword('new-password')">👁️</span>
        </div>

        <div class="mb-3 position-relative">
            <label for="confirm-password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
            <span class="toggle-btn" onclick="togglePassword('confirm-password')">👁️</span>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Password</button>
    </form>
  </div>
</div>

<style>
.toggle-btn {
    position: absolute;
    right: 10px;
    top: 35px;
    cursor: pointer;
    user-select: none;
}
.position-relative { position: relative; }
</style>

<script>
function togglePassword(id){
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
