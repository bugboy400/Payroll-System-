<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    // Not logged in → redirect to login
    header("Location: ../layouts/login.php");
    exit();
}
?>
