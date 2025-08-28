<?php
include "../config/db.php";

if(isset($_POST['company_name'])){
    $company_name = trim($_POST['company_name']);
    $stmt = $conn->prepare("SELECT * FROM admins WHERE company_name=? LIMIT 1");
    $stmt->bind_param("s",$company_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result && $result->num_rows >= 1){
        echo "exists";
    } else {
        echo "available";
    }
}
?>
