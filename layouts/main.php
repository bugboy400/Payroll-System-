<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Payroll System' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

   <!-- Global CSS -->
<link rel="stylesheet" href="/payrollself/includes/style.css">

<!-- Page-specific CSS -->
  <?php if (!empty($page_css)): ?>
    <?php foreach ((array)$page_css as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
<?php endif; ?>


</head>
<style>
/* Desktop layout (≥992px) */
.content-wrapper {
    margin-top: 80px; /* Height of navbar */
    margin-left: 240px; /* Width of sidebar */
    padding: 20px;
    transition: all 0.3s ease;
}

/* Fix for 775px–991px (tablet range with sidebar visible) */
@media (min-width: 775px) and (max-width: 991px) {
    .content-wrapper {
        margin-left: 250px; /* Same as full sidebar width */
    }
}

/* Mobile screens (<775px): sidebar off-canvas */
@media (max-width: 774px) {
    .content-wrapper {
        margin-left: 0;
    }
}


</style>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <?= $page_content ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <script src="layouts/scripts/sidebar-responsive.js"></script> -->

</body>
</html>
