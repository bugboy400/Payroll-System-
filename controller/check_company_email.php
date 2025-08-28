<?php
include "../config/db.php";

if(isset($_POST['company_name'])){
    $company = trim($_POST['company_name']);
    $stmt = $conn->prepare("SELECT * FROM admins LIMIT 1");
    $stmt->execute();
    $res = $stmt->get_result();
    if($res && $res->num_rows > 0){
        echo "company_blocked"; // Only 1 company allowed
    } else {
        echo "ok";
    }
}

if(isset($_POST['email'])){
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res && $res->num_rows > 0){
        echo "email_used";
    } else {
        echo "ok";
    }
}
?>
