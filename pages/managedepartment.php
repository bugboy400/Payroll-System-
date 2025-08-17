<?php
$page_title = "Manage Department";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/managedepartment.css"
];
ob_start();
?>

<div id="main-content">

  <div class="managedepartment">
    Manage Department
  </div>

  <div id="headerformanagedepartment">
    <div class="entries">
      <label for="Show">Show</label>
      <input type="number" name="entries" id="entryCount" min="1"  max="10">
      <label>entries</label>
    </div>
    <div class="searchbox">
      <label for="Search">Search:</label>
      <input type="text" name="Search" id="searchemployee" placeholder="Search Department">
    </div>
  </div>

  <!-- Department details table -->
  <table id="deptdetails" border="3" cellpadding="8" cellspacing="0" 
  style="width: 100%; margin-top: 15px;">
    <thead>
      <tr>
        <th>Department</th>
        <th>Designation</th>
        <th>Total Employees</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
  <tr>
    <td>IT</td>
    <td>Analyst</td>
    <td>18</td>
    <td>
      <button id="edit" type="submit"><i class="fa-solid fa-pen-fancy"></i>Edit</button>
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
