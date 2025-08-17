<?php
require 'db.php'; // include the DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $full_name    = trim($_POST['full_name'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $gender       = trim($_POST['gender'] ?? '');
    $password     = $_POST['password'] ?? '';

    // Basic validation
    if (!$full_name || !$company_name || !$email || !$gender || !$password) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM adminusers WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        die("Email already registered.");
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO adminusers (full_name, company_name, email, gender, password_hash)
        VALUES (:full_name, :company_name, :email, :gender, :password_hash)
    ");

    $stmt->execute([
        ':full_name'     => $full_name,
        ':company_name'  => $company_name,
        ':email'         => $email,
        ':gender'        => $gender,
        ':password_hash' => $password_hash
    ]);

    // Redirect to home.html with a success message
    header("Location: payrollself/header/home.html");
    exit;
} else {
    die("Invalid request method.");
}
?>