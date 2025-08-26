<?php
// Remove extra session_start
if (!isset($_SESSION)) session_start();

// Check login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../layouts/login.php");
    exit();
}

// Include DB connection
require_once '../config/db.php'; 
include("../includes/auth_check.php"); 

$page_title = "Dashboard";
$page_css = ["/payrollself/includes/dashboard.css"];  

ob_start();

// --- Total employees ---
$totalEmployees = 0;
$resEmp = $conn->query("SELECT COUNT(*) as total FROM employees_personal");
if ($resEmp) $totalEmployees = $resEmp->fetch_assoc()['total'];

// --- Total departments ---
$totalDepartments = 0;
$resDept = $conn->query("SELECT COUNT(*) as total FROM departments");
if ($resDept) $totalDepartments = $resDept->fetch_assoc()['total'];

// --- Today's attendance ---
$today = date("Y-m-d");
$tableName = "attendance_" . strtolower(date('F_Y', strtotime($today)));

$presentToday = 0;
$absentToday = 0;
$onLeaveToday = 0;

// Check if table exists
$res = $conn->query("SHOW TABLES LIKE '$tableName'");
if ($res && $res->num_rows > 0) {
    $res2 = $conn->query("SELECT status, COUNT(*) as cnt 
                          FROM `$tableName` 
                          WHERE attendance_date = '$today' 
                          GROUP BY status");
    if ($res2) {
        while ($row = $res2->fetch_assoc()) {
            if ($row['status'] === 'P') $presentToday = $row['cnt'];
            if ($row['status'] === 'A') $absentToday = $row['cnt'];
            if ($row['status'] === 'OL') $onLeaveToday = $row['cnt'];
        }
    }
}
?>

<div id="main-content">
    <div class="heading-line">
        <h3>Dashboard</h3>
    </div>

    <div class="row numberings mb-4">
        <div class="col-md-3 mb-3">
            <div class="box text-center h-100">
                <i class="bi bi-people"></i>
                <h5 class="mt-2">Total Employees</h5>
                <p class="number"><?= $totalEmployees ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="box text-center h-100">
                <i class="bi bi-building"></i>
                <h5 class="mt-2">Total Departments</h5>
                <p class="number"><?= $totalDepartments ?></p>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="box text-center h-100" style="background:#d4edda; color:#155724;">
                <h5 class="mt-2">Present Today</h5>
                <p class="number"><?= $presentToday ?></p>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="box text-center h-100" style="background:#f8d7da; color:#721c24;">
                <h5 class="mt-2">Absent Today</h5>
                <p class="number"><?= $absentToday ?></p>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="box text-center h-100" style="background:#fff3cd; color:#856404;">
                <h5 class="mt-2">On Leave Today</h5>
                <p class="number"><?= $onLeaveToday ?></p>
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
?>
