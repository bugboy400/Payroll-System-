<?php
$page_title = "Manage Leave Type";
$page_css = [
    "/payrollself/includes/dashboard.css",
];

ob_start();
?>
<h2>Manage Leave Type</h2>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
