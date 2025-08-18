<?php
$page_title = "Manage Employee";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/manageemployee.css"
];

include("../config/db.php");
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
  <table id="empdetails" border="3" cellpadding="8" cellspacing="0" style="width: 100%; margin-top: 15px;">
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
    <tbody id="employee-tbody">
      <!-- Data loaded via AJAX -->
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="buttonscontrol mt-3">
    <button type="button" id="previouspage">Previous</button>
    <button type="button" id="nextpage">Next</button>
  </div>

</div>

<script>
let currentPage = 1;
let entriesPerPage = parseInt(document.getElementById('entryCount').value);

function fetchEmployees() {
    const search = document.getElementById('searchemployee').value;

    fetch(`../controller/fetch_employees.php?search=${encodeURIComponent(search)}&page=${currentPage}&entries=${entriesPerPage}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('employee-tbody');
            tbody.innerHTML = '';

            if(data.employees.length === 0){
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No records found</td></tr>';
                return;
            }

            data.employees.forEach((emp, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${(currentPage-1)*entriesPerPage + index + 1}</td>
                    <td>${emp.name}</td>
                    <td>${emp.email || ''}</td>
                    <td>${emp.department_name || ''}</td>
                    <td>${emp.designation_name || ''}</td>
                    <td>
                        <button id="view" type="button"><a style="color:white;text-decoration:none;" href="employeedetails.php?emp_id=${emp.emp_id}">View Details</a></button>
                        <button id="edit" type="button"><a style="color:white;text-decoration:none;" href="editemployee.php?emp_id=${emp.emp_id}">Edit</a></button>
                        <button class="delete-btn" type="button" data-id="${emp.emp_id}">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Delete functionality
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    if(confirm("Are you sure you want to delete this employee?")){
                        const empId = btn.dataset.id;
                        fetch(`../controller/delete_employee.php?emp_id=${empId}`)
                        .then(res => res.json())
                        .then(resData => {
                            if(resData.success) fetchEmployees();
                            else alert('Error deleting employee');
                        });
                    }
                });
            });
        });
}

// Event listeners
document.getElementById('entryCount').addEventListener('input', e=>{
    entriesPerPage = parseInt(e.target.value);
    currentPage = 1;
    fetchEmployees();
});

document.getElementById('searchemployee').addEventListener('input', ()=>{
    currentPage = 1;
    fetchEmployees();
});

document.getElementById('previouspage').addEventListener('click', ()=>{
    if(currentPage > 1){ currentPage--; fetchEmployees(); }
});

document.getElementById('nextpage').addEventListener('click', ()=>{
    currentPage++;
    fetchEmployees();
});

// Initial fetch
fetchEmployees();
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
