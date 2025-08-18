<?php
include("../config/db.php");

if(isset($_GET['dept_id'])){
    $dept_id = (int)$_GET['dept_id'];
    $stmt = $conn->prepare("SELECT designation_name FROM designations WHERE dept_id = ?");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $designations = [];
    while($row = $result->fetch_assoc()){
        $designations[] = $row;
    }
    echo json_encode($designations);
}
