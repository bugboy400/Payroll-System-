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
    <h3>Manage Departments</h3>
  </div>

  <div id="headerformanagedepartment">
    <div class="entries">
      <label for="Show">Show</label>
      <input type="number" name="entries" id="entryCount" min="1" value="10">
      <label>entries</label>
    </div>
    <div class="searchbox">
      <label for="Search">Search:</label>
      <input type="text" name="Search" id="searchdepartment" placeholder="Search Department">
    </div>
  </div>

  <!-- Department details table -->
  <table id="deptdetails" class="department-table">
    <thead>
      <tr>
        <th>Department</th>
        <th>Designation</th>
        <th>Total Employees</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="department-tbody">
      <!-- Data loaded via AJAX -->
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="buttonscontrol mt-3">
    <button type="button" id="previouspage">Previous</button>
    <button type="button" id="nextpage">Next</button>
  </div>

</div>

<style>
.department-table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
  margin-top: 15px;
}
.department-table th, .department-table td {
  border: 1px solid #ccc;
  padding: 8px 12px;
  text-align: left;
  vertical-align: top;
}
.department-table th {
  background-color: #f4f4f4;
}
.btn {
  padding: 4px 8px;
  margin: 0 2px;
  border: none;
  border-radius: 4px;
  color: #fff;
  cursor: pointer;
  font-size: 0.85rem;
}
.btn.edit { background-color: #f1c40f; }
.btn.delete { background-color: #e74c3c; }
.btn a { color: white; text-decoration: none; }
.btn:hover { opacity: 0.85; }
</style>

<script>
let currentPage = 1;
let entriesPerPage = parseInt(document.getElementById('entryCount').value);
let totalRecords = 0;

function fetchDepartments() {
    const search = document.getElementById('searchdepartment').value;

    fetch(`../controller/fetch_departments.php?search=${encodeURIComponent(search)}&page=${currentPage}&entries=${entriesPerPage}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('department-tbody');
            tbody.innerHTML = '';
            totalRecords = data.total;

            if(data.departments.length === 0){
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No records found</td></tr>';
                return;
            }

            data.departments.forEach(dept => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${dept.department_name}</td>
                    <td>${dept.designations.join('<br>')}</td>
                    <td>${dept.employee_counts.join('<br>')}</td>
                    <td>
                        <button class="btn edit"><a href="editdepartment.php?dept_id=${dept.dept_id}">Edit</a></button>
                        <button class="btn delete" type="button" data-id="${dept.dept_id}">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Delete functionality
            document.querySelectorAll('.btn.delete').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    if(confirm("Are you sure you want to delete this department?")){
                        const deptId = btn.dataset.id;
                        fetch(`../controller/delete_department.php?dept_id=${deptId}`)
                        .then(res => res.json())
                        .then(resData => {
                            if(resData.success) fetchDepartments();
                            else alert('Error deleting department');
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
    fetchDepartments();
});

document.getElementById('searchdepartment').addEventListener('input', ()=>{
    currentPage = 1;
    fetchDepartments();
});

document.getElementById('previouspage').addEventListener('click', ()=>{
    if(currentPage > 1){ currentPage--; fetchDepartments(); }
});

document.getElementById('nextpage').addEventListener('click', ()=>{
    const maxPage = Math.ceil(totalRecords / entriesPerPage);
    if(currentPage < maxPage){ currentPage++; fetchDepartments(); }
});

// Initial fetch
fetchDepartments();
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
