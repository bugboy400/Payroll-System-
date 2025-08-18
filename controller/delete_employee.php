<?php
session_start();
include("../config/db.php");

header('Content-Type: application/json');

if(isset($_GET['emp_id'])){
    $emp_id = $_GET['emp_id'];

    // Begin transaction to ensure all related deletes succeed
    $conn->begin_transaction();

    try {
        // Delete from allowances
        $stmt = $conn->prepare("DELETE FROM employees_allowances WHERE emp_id = ?");
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $stmt->close();

        // Delete from deductions
        $stmt = $conn->prepare("DELETE FROM employees_deductions WHERE emp_id = ?");
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $stmt->close();

        // Delete from financial
        $stmt = $conn->prepare("DELETE FROM employees_financial WHERE emp_id = ?");
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $stmt->close();

        // Delete from company
        $stmt = $conn->prepare("DELETE FROM employees_company WHERE emp_id = ?");
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $stmt->close();

        // Delete from personal
        $stmt = $conn->prepare("DELETE FROM employees_personal WHERE emp_id = ?");
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo json_encode(['success'=>true]);
    } catch(Exception $e){
        $conn->rollback();
        echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
    }

} else {
    echo json_encode(['success'=>false, 'error'=>'Employee ID missing']);
}
?>
