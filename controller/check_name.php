<?php
session_start();
include "../config/db.php";

$name = trim($_POST['full_name'] ?? '');

if($name){
    $stmt = $conn->prepare("SELECT * FROM admins WHERE full_name=? LIMIT 1");
    $stmt->bind_param("s",$name);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result && $result->num_rows > 0){
        echo "exists";
    } else {
        echo "ok";
    }
}
?>
