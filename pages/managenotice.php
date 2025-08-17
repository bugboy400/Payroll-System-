<?php
$page_title = "Manage Notice";
$page_css = [
    "/payrollself/includes/dashboard.css",
];

ob_start();
?>
<h2>Manage Notice</h2>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
