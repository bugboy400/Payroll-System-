<?php
session_start();
require_once '../config/db.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['forgot'])){
    $email = trim($_POST['email']);

    // ✅ Server-side email validation
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['forgot_error'] = "Please enter a valid email address!";
        $_SESSION['form_data'] = ['email' => $email]; // retain input
        header("Location: ../layouts/login.php");
        exit();
    }

    // Check if email exists in admins table
    $stmt = $conn->prepare("SELECT admin_id, full_name FROM admins WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows === 0){
        $_SESSION['forgot_error'] = "No admin found with this email.";
        $_SESSION['form_data'] = ['email' => $email];
        header("Location: ../layouts/login.php");
        exit();
    }

    $admin = $res->fetch_assoc();

    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $stmt = $conn->prepare("UPDATE admins SET reset_token=?, reset_expires=? WHERE admin_id=?");
    $stmt->bind_param("ssi",$token,$expires,$admin['admin_id']);
    $stmt->execute();

    $reset_link = "http://{$_SERVER['HTTP_HOST']}/payrollself/layouts/reset_password.php?token=$token";

    // Send email via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'heymantadhami@gmail.com'; // replace with your email
        $mail->Password = 'amcm louv ilvm bldm';   // replace with your app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@yourdomain.com','Admin Panel');
        $mail->addAddress($email,$admin['full_name']);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "<p>Hello {$admin['full_name']},</p>
                       <p>Click this link to reset your password (valid 1 hour):</p>
                       <p><a href='$reset_link'>$reset_link</a></p>
                       <p>If you did not request this, ignore this email.</p>";
        $mail->send();

        $_SESSION['forgot_success'] = "A password reset link has been sent to your email.";
    } catch(Exception $e) {
        $_SESSION['forgot_error'] = "Failed to send reset link. Mailer Error: {$mail->ErrorInfo}";
    }

    header("Location: ../layouts/login.php");
    exit();
}
?>
