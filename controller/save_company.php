<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../layouts/login.php");
    exit();
}

$admin_id     = $_SESSION['admin_id'];
$company_name = $_POST['company_name'] ?? '';
$phone        = $_POST['phone'] ?? '';
$email        = $_POST['email'] ?? '';
$website      = $_POST['website'] ?? '';
$address      = $_POST['address'] ?? '';
$city         = $_POST['city'] ?? '';
$state        = $_POST['state'] ?? '';
$postal       = $_POST['postal'] ?? '';
$country      = $_POST['country'] ?? '';

// check if exists
$stmt = $conn->prepare("SELECT company_id FROM companydetails WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Update
    $stmt = $conn->prepare("UPDATE companydetails 
        SET company_name=?, phone=?, email=?, website=?, address=?, city=?, state=?, postal=?, country=? 
        WHERE admin_id=?");
    $stmt->bind_param("sssssssssi", $company_name, $phone, $email, $website, $address, $city, $state, $postal, $country, $admin_id);
    $stmt->execute();
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO companydetails 
        (admin_id, company_name, phone, email, website, address, city, state, postal, country) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssss", $admin_id, $company_name, $phone, $email, $website, $address, $city, $state, $postal, $country);
    $stmt->execute();
}
/* ✅ Update admins table as well */
$stmt = $conn->prepare("UPDATE admins SET company_name=? WHERE admin_id=?");
$stmt->bind_param("si", $company_name, $admin_id);
$stmt->execute();
// ✅ Update session so navbar reflects immediately
$_SESSION['company_name'] = $company_name;

header("Location: ../pages/dashboard.php"); 
exit();
?>
