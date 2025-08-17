<?php
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
