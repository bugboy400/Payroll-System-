<?php
$page_title = "Add Holiday";
$page_css = [
    "/payrollself/includes/dashboard.css",
];

ob_start();
?>
<h2>Add Holiday</h2>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
