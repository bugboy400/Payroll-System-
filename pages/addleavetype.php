<?php
session_start();

// If no active session, redirect to login
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

// Prevent browser from caching this page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$page_title = "Add Leave Type";
$page_css = [
    "/payrollself/includes/dashboard.css",
];

ob_start();
?>
<h2>Add leave type</h2>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
