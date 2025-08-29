<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-custom fixed-top px-4 bg-dark">
  <!-- ✅ Company Name on left -->
  <a href="#" class="navbar-brand text-warning fw-bold">
  <?= htmlspecialchars($_SESSION['company_name'] ?? 'Company Name') ?>
</a>


  <!-- ✅ User dropdown on right -->
  <div class="ms-auto dropdown">
    <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown">
      <?= htmlspecialchars($_SESSION['full_name'] ?? 'User Name') ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="./profile.php"><i class="bi bi-person-circle me-2"></i>Profile</a></li>
      <li><a class="dropdown-item" href="../controller/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Log Out</a></li>
    </ul>
  </div>
</nav>
