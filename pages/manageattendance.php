<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
        <div class="deptemp-section">
            <label for="deptemp-input">Employee by Department</label>
            <select id="deptemp-input">
                <option value="">All Departments</option>
            </select>
        </div>

        <div class="date-section">
            <label for="date-input">Date:</label>
            <input type="date" id="date-input"
                   value="<?= date('Y-m-d') ?>"
                   max="<?= date('Y-m-d') ?>"
                   min="<?= date('Y-m-01') ?>">
        </div>

        <div class="bulk-status">
            <label for="bulk-status-select">Set status for selected:</label>
            <select id="bulk-status-select" disabled>
                <option value="">--Select--</option>
                <option value="P">Present</option>
                <option value="A">Absent</option>
                <option value="OL">On Leave</option>
            </select>
            <button id="apply-bulk" disabled>Apply</button>
        </div>
    </div>

    <div class="listhere">
        <div id="headerformanageemployee">
            <div class="entries">
                <label>Show</label>
                <input type="number" id="entryCount" min="1" value="5">
                <label>entries</label>
            </div>
            <div class="searchbox">
                <label>Search:</label>
                <input type="text" id="searchemployee" placeholder="Search employee">
            </div>
        </div>

        <div class="table-responsive">
            <table id="empdetails" border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align:center;">Loading employees...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="pagination">
            <button id="prev-page">Prev</button>
            <span id="page-info">Page 1</span>
            <button id="next-page">Next</button>
        </div>

        <div class="buttonscontrol">
            <button type="submit" id="savepage" class="btn-save">Save Attendance</button>
        </div>
    </div>
</div>

<style>
/* Basic improved styling */
#main-content h3 { margin-bottom: 20px; }
.deptemp { display: flex; gap: 20px; margin-bottom: 15px; align-items: center; }
.deptemp select, .deptemp input, .bulk-status select { padding: 5px 10px; border-radius: 5px; border: 1px solid #ccc; }
.bulk-status button { padding: 5px 10px; margin-left: 5px; border-radius: 5px; cursor: pointer; }
.table-responsive { margin-top: 10px; overflow-x:auto; }
#empdetails { width: 100%; border-collapse: collapse; }
#empdetails th, #empdetails td { text-align: left; }
.attendance-status { padding: 3px 5px; border-radius: 4px; }
.btn-save { background-color:#007bff; color:white; padding:7px 15px; border:none; border-radius:5px; cursor:pointer; }
.btn-save:hover { background-color:#0056b3; }
</style>

<script>
let employeesData = [];
let currentPage = 1;
let rowsPerPage = 5;
let totalEmployees = 0;

function fetchDepartments() {
    fetch('../controller/departments_api.php')
        .then(res => res.json())
        .then(data => {
            const deptSelect = document.getElementById('deptemp-input');
            data.departments.forEach(d => {
                const option = document.createElement('option');
                option.value = d.dept_id;
                option.textContent = d.department_name;
                deptSelect.appendChild(option);
            });
        });
}

function fetchEmployees() {
    const dept_id = document.getElementById('deptemp-input').value;
    const date = document.getElementById('date-input').value;
    const search = document.getElementById('searchemployee').value;

    fetch(`../controller/fetch_employees_for_attendance.php?dept_id=${dept_id}&date=${date}&page=${currentPage}&per_page=${rowsPerPage}&search=${encodeURIComponent(search)}`)
        .then(res => res.json())
        .then(data => {
            employeesData = data.employees;
            totalEmployees = data.total;
            renderTable();
        });
}

function renderTable() {
    const tbody = document.querySelector('#empdetails tbody');
    tbody.innerHTML = '';

    if (!employeesData.length) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No employees found</td></tr>';
        return;
    }

    employeesData.forEach(emp => {
        const tr = document.createElement('tr');
        tr.dataset.empId = emp.emp_id;
        tr.innerHTML = `
            <td><input type="checkbox" class="emp-select"></td>
            <td>${emp.emp_id}</td>
            <td>${emp.name}</td>
            <td>
                <select class="attendance-status">
                    <option value="P" ${emp.status==='P'?'selected':''}>Present</option>
                    <option value="A" ${emp.status==='A'?'selected':''}>Absent</option>
                    <option value="OL" ${emp.status==='OL'?'selected':''}>On Leave</option>
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    });

    applyStatusColors();
    updateBulkStatusEnable();
    document.getElementById('page-info').textContent = `Page ${currentPage} of ${Math.ceil(totalEmployees/rowsPerPage)}`;
}

function applyStatusColors() {
    document.querySelectorAll('.attendance-status').forEach(sel => {
        function updateColor() {
            if(sel.value==='P'){ sel.style.background='#d4edda'; sel.style.color='#155724'; }
            else if(sel.value==='A'){ sel.style.background='#f8d7da'; sel.style.color='#721c24'; }
            else { sel.style.background='#fff3cd'; sel.style.color='#856404'; }
        }
        updateColor();
        sel.addEventListener('change', updateColor);
    });
}

// Bulk selection and enable bulk status
document.getElementById('select-all').addEventListener('change', e => {
    const checked = e.target.checked;
    document.querySelectorAll('.emp-select').forEach(cb => cb.checked = checked);
    updateBulkStatusEnable();
});

document.addEventListener('change', e => {
    if(e.target.classList.contains('emp-select')){
        updateBulkStatusEnable();
    }
});

function updateBulkStatusEnable(){
    const anyChecked = document.querySelectorAll('.emp-select:checked').length > 0;
    document.getElementById('bulk-status-select').disabled = !anyChecked;
    document.getElementById('apply-bulk').disabled = !anyChecked;
}

// Apply bulk status
document.getElementById('apply-bulk').addEventListener('click', () => {
    const status = document.getElementById('bulk-status-select').value;
    if(!status) return;
    document.querySelectorAll('.emp-select:checked').forEach(cb => {
        cb.closest('tr').querySelector('.attendance-status').value = status;
    });
    applyStatusColors();
});

// Pagination
document.getElementById('prev-page').addEventListener('click', ()=>{
    if(currentPage>1){ currentPage--; fetchEmployees(); }
});
document.getElementById('next-page').addEventListener('click', ()=>{
    const totalPages = Math.ceil(totalEmployees/rowsPerPage);
    if(currentPage<totalPages){ currentPage++; fetchEmployees(); }
});

// Save Attendance
document.getElementById('savepage').addEventListener('click', ()=>{
    const date = document.getElementById('date-input').value;
    const attendance = Array.from(document.querySelectorAll('#empdetails tbody tr')).map(tr=>({
        emp_id: tr.dataset.empId,
        status: tr.querySelector('.attendance-status').value
    }));

    fetch('../controller/save_attendance.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({date, attendance})
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){ 
            alert('Attendance saved'); 
            fetchEmployees(); 
        } else alert('Error: ' + (data.error || 'Unknown'));
    });
});

// Search and entry count
document.getElementById('searchemployee').addEventListener('input', ()=>{
    currentPage = 1;
    fetchEmployees();
});
document.getElementById('entryCount').addEventListener('change', ()=>{
    rowsPerPage = parseInt(document.getElementById('entryCount').value) || 5;
    currentPage = 1;
    fetchEmployees();
});

// Department/date change
document.getElementById('deptemp-input').addEventListener('change', ()=>{ currentPage = 1; fetchEmployees(); });
document.getElementById('date-input').addEventListener('change', ()=>{ currentPage = 1; fetchEmployees(); });

// Initialize
fetchDepartments();
fetchEmployees();
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
