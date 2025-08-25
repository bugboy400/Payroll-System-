<?php
$page_title = "Manage Leave";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/manageleave.css",
];

ob_start();

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

?>
<div id="main-content">

  <div class="manageleave">
    <h3>Manage Leave</h3>
  </div>

  <div id="headerformanageleave">
    <div class="entries">
      <label for="Show">Show</label>
      <input type="number" name="entries" id="entryCount" min="1" value="10">
      <label>entries</label>
    </div>
    <div class="searchbox">
      <label for="Search">Search:</label>
      <input type="text" name="Search" id="searchleave" placeholder="Search leave">
    </div>
  </div>

  <!-- Leave details table -->
  <table id="leavedetails" border="3" cellpadding="8" cellspacing="0" 
  style="width: 100%; margin-top: 15px;">
    <thead>
      <tr>
        <th>#</th>
        <th>Employee Name</th>
        <th>Leave Type</th>
        <th>Duration</th>
        <th>Leave Status</th>
        <th>Comment</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
  <tr>
    <td>1</td>
    <td>Demo Name</td>
    <td>Annual Leave</td>
    <td>2025-08-20 to 2025-08-25</td>
    <td>
  <select class="status-dropdown">
    <option value="pending" selected>Pending</option>
    <option value="Cancelled">Reviewing</option>
    <option value="approved">Approved</option>
  </select>
</td>

    <td>Quick Urgent Leave </td>
    <td>
      <button id="edit" type="button" onclick="window.location.href='editleave.php';">
  <i class="fa-solid fa-pen-fancy"></i> Edit
</button>

      <button id="delete" type="submit"><i class="fa-solid fa-xmark"></i>Delete</button>
    </td>
  </tr>
</tbody>
  </table>

  <!-- Buttons -->
  <div class="buttonscontrol">
    <button type="submit" id="previouspage">Previous</button>
    <button type="submit" id="nextpage">Next</button>
  </div>

</div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
