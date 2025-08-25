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

$page_title = "Manage Attendance";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/manageattendance.css"
];

ob_start();
?>

<div id="main-content">
    <h3>Manage Attendance</h3>
    <div class="deptemp">
        <!-- Department Employee -->
        <div class="deptemp-section">
            <label for="year-input">Employee by Department</label>
            <input type="text" id="deptemp-input">
        </div>

        <!-- date section -->
        <div class="date-section">
    <label for="date-input">Date:</label>
    <div class="date-input-container">
        <input type="date" id="date-input">
    </div>
</div>


        <!-- Find button -->
        <button id="get-employee-list">Get Employee List</button>
    </div>

    <div class="listhere">
        <div id="headerformanageemployee">
            <div class="entries">
                <label for="Show">Show</label>
                <input type="number" name="entries" id="entryCount" min="1" value="10">
                <label>entries</label>
            </div>
            <div class="searchbox">
                <label for="Search">Search:</label>
                <input type="text" name="Search" id="searchemployee" placeholder="Search employee">
            </div>
        </div>

        <!-- Attendance details table -->
        <div class="table-responsive">
            <table id="empdetails" border="3" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Attendance By</th>
                        <th>Date</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Demo Name</td>
                        <td>Demo by admin</td>
                        <td>today date here</td>
                        <td>Intime</td>
                        <td>Outtime</td>
                        <td>
            <select class="attendance-status">
                <option value="Present" selected>Present</option>
                <option value="Absent">Absent</option>
                <option value="On Leave">Absent</option>
            </select>
        </td>

                    </tr>
                   
                </tbody>
            </table>
        </div>

        <!-- Buttons -->
        <div class="buttonscontrol">
            <button type="submit" id="savepage">Save</button>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.attendance-status').forEach(select => {
  function updateColor() {
    if (select.value === "present") {
      select.style.backgroundColor = "#d4edda";
      select.style.color = "#155724";
    } else {
      select.style.backgroundColor = "#f8d7da";
      select.style.color = "#721c24";
    }
  }
  updateColor();
  select.addEventListener("change", updateColor);
});
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
