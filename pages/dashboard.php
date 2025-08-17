<?php
session_start();

// ✅ Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}
include("../includes/auth_check.php"); // make sure path is correct
$page_title = "Dashboard";
$page_css = [
    "/payrollself/includes/dashboard.css",  
];

ob_start();
?>
<div id="main-content">
    <div class="heading-line">
        <h3>Dashboard</h3>
    </div>

    <div class="row numberings mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="box text-center h-100">
                <i class="bi bi-people"></i>
                <h5 class="mt-2">Total Employees</h5>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="box text-center h-100">
                <i class="bi bi-building"></i>
                <h5 class="mt-2">Total Departments</h5>
            </div>
        </div>
    </div>

    <div id="quote">
        <h3>Welcome Back!</h3>
        <h6>Stay productive and keep shining!</h6>
    </div>
</div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
