<?php
$page_title = "Change Password";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/changepassword.css"
];

ob_start();
?>

<!-- MAIN CONTENT -->
 <div id="main-content" class="d-flex justify-content-center align-items-center  bg-light border-0">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-4">Change Password</h3>
    <form action="/change-password" method="post">
      <div class="mb-3">
        <label for="current-password" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="current-password" name="current-password" required>
      </div>

      <div class="mb-3">
        <label for="new-password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="new-password" name="new-password" required>
      </div>

      <div class="mb-3">
        <label for="confirm-password" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Update Password</button>
    </form>
  </div>
</div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
