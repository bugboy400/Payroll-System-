<?php
session_start();

// If no active session, redirect to login
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

// Prevent browser from caching this page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$page_title = "Edit Leave";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/editleave.css",
];

ob_start();
?>
<div id="main-content">
    <h3>Edit Leave</h3>
    
    <form action="updateleave.php" method="POST">
      <div class="form-group">
        <label for="employeeName">Employee Name</label>
        <input type="text" id="employeeName" name="employeeName" value="Demo Name">
      </div>

      <div class="form-group">
        <label for="leaveType">Leave Type</label>
        <select id="leaveType" name="leaveType">
          <option value="annual">Annual Leave</option>
          <option value="sick">Sick Leave</option>
          <option value="casual">Casual Leave</option>
        </select>
      </div>

      <div class="form-group">
        <label for="fromDate">From</label>
        <input type="date" id="fromDate" name="fromDate" value="2025-08-20">
      </div>

      <div class="form-group">
        <label for="toDate">To</label>
        <input type="date" id="toDate" name="toDate" value="2025-08-25">
      </div>

      <div class="form-group">
        <label for="comment">Comment</label>
        <textarea id="comment" name="comment">Quick Urgent Leave</textarea>
      </div>

      <div class="form-group">
        <label for="leaveStatus">Leave Status</label>
        <select id="leaveStatus" name="leaveStatus">
          <option value="pending" selected>Pending</option>
          <option value="approved">Approved</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>

      <button type="submit" class="update-btn">
        <i class="fa-solid fa-rotate-right"></i> Update
      </button>
    </form>
  </div>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
