<?php
$page_title = "Manage Employee";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/manageemployee.css"
];

ob_start();
?>

  <div id="main-content">

  <div class="manageemployee">
    <h3>Manage Employees</h3>
  </div>

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

  <!-- Employee details table -->
  <table id="empdetails" border="3" cellpadding="8" cellspacing="0" 
  style="width: 100%; margin-top: 15px;">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
  <tr>
    <td>1</td>
    <td>Demo Name</td>
    <td>demo@example.com</td> <!-- Email was missing or wrong -->
    <td>IT</td>
    <td>Cybsec</td>
    <td>
      <button id="edit" type="submit"><a style="color: white; text-decoration: none;" href="employeedetails.html">View Details</a></button>
      <button id="edit" type="submit"><a style="color: white; text-decoration: none;" href="edit.html"><i class="fa-solid fa-pen-fancy"></i>Edit</a></button>
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
