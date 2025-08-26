<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin_id'])) header("Location: ../layouts/login.php");

require_once '../config/db.php'; 

$page_title = "Dashboard";
$page_css = ["/payrollself/includes/dashboard.css"];  

ob_start();

// Total employees
$resEmp = $conn->query("SELECT COUNT(*) as total FROM employees_personal");
$totalEmployees = $resEmp ? $resEmp->fetch_assoc()['total'] : 0;

// Total departments
$resDept = $conn->query("SELECT COUNT(*) as total FROM departments");
$totalDepartments = $resDept ? $resDept->fetch_assoc()['total'] : 0;

// Today's attendance
$today = date("Y-m-d");
$tableName = "attendance_" . strtolower(date('F_Y', strtotime($today)));
$presentToday = $absentToday = $onLeaveToday = 0;

$res = $conn->query("SHOW TABLES LIKE '$tableName'");
if($res && $res->num_rows>0){
    $res2 = $conn->query("SELECT status, COUNT(*) as cnt FROM `$tableName` WHERE attendance_date='$today' GROUP BY status");
    if($res2){
        while($row=$res2->fetch_assoc()){
            if($row['status']=='P') $presentToday=$row['cnt'];
            if($row['status']=='A') $absentToday=$row['cnt'];
            if($row['status']=='OL') $onLeaveToday=$row['cnt'];
        }
    }
}

// Get implemented quote
$resQuote = $conn->query("SELECT quote_text, quote_author FROM daily_quotes WHERE status='implemented' LIMIT 1");
$implQuote = $resQuote && $resQuote->num_rows>0 ? $resQuote->fetch_assoc() : null;
?>

<div id="main-content">
    <div class="heading-line"><h3>Dashboard</h3></div>

    <div class="row numberings mb-4">
        <div class="col-md-3 mb-3">
            <div class="box text-center h-100">
                <i class="bi bi-people" style="font-size:2rem;"></i>
                <h5 class="mt-2">Total Employees</h5>
                <p class="number"><?= $totalEmployees ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="box text-center h-100">
                <i class="bi bi-building" style="font-size:2rem;"></i>
                <h5 class="mt-2">Total Departments</h5>
                <p class="number"><?= $totalDepartments ?></p>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="box text-center h-100" style="background:#d4edda; color:#155724;">
                <i class="bi bi-check-circle" style="font-size:2rem;"></i>
                <h5 class="mt-2">Present Today</h5>
                <p class="number"><?= $presentToday ?></p>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="box text-center h-100" style="background:#f8d7da; color:#721c24;">
                <i class="bi bi-x-circle" style="font-size:2rem;"></i>
                <h5 class="mt-2">Absent Today</h5>
                <p class="number"><?= $absentToday ?></p>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="box text-center h-100" style="background:#fff3cd; color:#856404;">
                <i class="bi bi-calendar-minus" style="font-size:2rem;"></i>
                <h5 class="mt-2">On Leave Today</h5>
                <p class="number"><?= $onLeaveToday ?></p>
            </div>
        </div>
    </div>

    <div id="quote" class="mt-4 text-center">
        <?php if($implQuote): ?>
            <h3><i class="bi bi-quote"></i> <?= htmlspecialchars($implQuote['quote_text']) ?> <i class="bi bi-quote"></i></h3>
            <h6>- <?= htmlspecialchars($implQuote['quote_author']) ?></h6>
        <?php else: ?>
            <h3>Welcome Back!</h3>
            <h6>Stay productive and keep shining!</h6>
        <?php endif; ?>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
