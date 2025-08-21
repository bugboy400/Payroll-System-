<?php
$page_title = "Create Payslip";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/createpayslip.css"
];

ob_start();
?>

<div id="main-content">
  <h3>Create Payslip</h3>

  <div class="container mt-4 p-4" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
    <form id="payslip-form">
      <div class="row g-4 align-items-end">
        <!-- Department -->
        <div class="col-md-3">
          <label for="department" class="form-label fw-bold">Department</label>
          <select id="department" class="form-control p-2">
            <option value="">Select Department</option>
          </select>
        </div>

        <!-- Employee (Searchable) -->
        <div class="col-md-3">
          <label for="employee" class="form-label fw-bold">Employee</label>
          <input list="employee-list" id="employee" class="form-control p-2" placeholder="Search Employee">
          <datalist id="employee-list"></datalist>
          <input type="hidden" id="employee_id" name="employee_id">
        </div>

        <!-- Year -->
        <div class="col-md-2">
          <label for="year" class="form-label fw-bold">Year</label>
          <input type="number" id="year" class="form-control p-2" readonly>
        </div>

        <!-- Month -->
        <div class="col-md-2">
          <label for="month" class="form-label fw-bold">Month</label>
          <select id="month" class="form-control p-2"></select>
        </div>

        <!-- Next Button -->
        <div class="col-md-2">
          <button type="button" id="next-btn" class="btn w-100 py-2">Next</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ALLOWANCES & DEDUCTIONS (Original Code) -->
<div id="allow-deduct" class="container-fluid mt-4" style="display: none;">
  <div class="row g-4">
    <!-- ALLOWANCE -->
    <div class="col-lg-6">
      <div class="form-section">
        <h5>Allowances</h5>
        <div id="allowances-container">
          <div class="form-row allowance-row">
            <select name="allowance" class="allowancename">
              <option value="homeallowance">Home Allowance</option>
              <option value="healthallowance">Health Allowance</option>
              <option value="overtimeallowance">OT Allowance</option>
              <option value="festiveallowance">Festive Allowance</option>
            </select>
            <input type="number" name="allowanceamt" class="allowanceamt" placeholder="Amount">
            <button type="button" class="btn-icon add-btn add-allowance-btn">+</button>
            <button type="button" class="btn-icon remove-btn remove-allowance-btn">×</button>
          </div>
        </div>

        <hr class="mt-4">

        <h6 class="mt-3">Other Allowance</h6>
        <div class="row mt-2">
          <div class="col-6">
            <input type="text" name="otherallowancetitle" class="form-control" placeholder="Title">
          </div>
          <div class="col-6">
            <input type="number" name="otherallowanceamt" class="form-control" placeholder="Amount">
          </div>
        </div>
      </div>
    </div>

    <!-- DEDUCTIONS -->
    <div class="col-lg-6">
      <div class="form-section">
        <h5>Deductions</h5>
        <div id="deductions-container">
          <div class="form-row deduction-row">
            <select name="deduction" class="deductionname">
              <option value="providentfund">Provident Fund</option>
              <option value="leave">Leave</option>
            </select>
            <input type="number" name="deductionamt" class="deductionamt" placeholder="Amount">
            <button type="button" class="btn-icon add-btn add-deduction-btn">+</button>
            <button type="button" class="btn-icon remove-btn remove-deduction-btn">×</button>
          </div>
        </div>

        <hr class="mt-4">

        <h6 class="mt-3">Other Deduction</h6>
        <div class="row mt-2">
          <div class="col-6">
            <input type="text" name="otherdeductiontitle" class="form-control" placeholder="Title">
          </div>
          <div class="col-6">
            <input type="number" name="otherdeductionamt" class="form-control" placeholder="Amount">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SUMMARY -->
<div id="summary" style="display: none;">
  <h3>Summary</h3>
  <div class="form-container">
    <div class="form-rowsum">
      <label for="basic-salary">Basic Salary</label>
      <input type="number" name="basic_salary" id="basic-salary">
    </div>

    <div class="form-rowsum">
      <label for="total-allowance">Total Allowance</label>
      <input type="number" name="total_allowance" id="total-allowance">
    </div>

    <div class="form-rowsum">
      <label for="total-deduction">Total Deduction</label>
      <input type="number" name="total_deduction" id="total-deduction">
    </div>

    <div class="form-rowsum">
      <label for="net-salary">Net Salary</label>
      <input type="number" name="net_salary" id="net-salary">
    </div>

    <div class="form-rowsum">
      <label for="status">Status</label>
      <select name="status" id="status">
        <option value="Paid">Paid</option>
        <option value="Unpaid" selected>Unpaid</option>
      </select>
    </div>

    <button type="button" id="create-payslip-btn">Create Payslip</button>
  </div>
</div>

<script>
// =====================
// Initialize Year & Month
// =====================
document.getElementById('year').value = new Date().getFullYear();
const monthSelect = document.getElementById('month');
const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
monthNames.forEach((m,i)=>{ if(i<=new Date().getMonth()){ 
  const option = document.createElement('option'); 
  option.value = m; option.textContent = m; monthSelect.appendChild(option); 
}});

// =====================
// Load Departments
// =====================
function loadDepartments() {
  fetch('../controller/fetchdept_payslip.php')
    .then(res => res.json())
    .then(data => {
      const deptSelect = document.getElementById('department');
      data.departments.forEach(dept => {
        const option = document.createElement('option');
        option.value = dept.dept_id;
        option.textContent = dept.department_name;
        deptSelect.appendChild(option);
      });
    });
}
loadDepartments();

// =====================
// Employee autocomplete
// =====================
let employeeList = [];

document.getElementById('department').addEventListener('change', function(){
  const deptId = this.value;
  const empInput = document.getElementById('employee');
  const empDatalist = document.getElementById('employee-list');
  const hiddenEmpId = document.getElementById('employee_id');

  empInput.value = '';
  empDatalist.innerHTML = '';
  hiddenEmpId.value = '';
  employeeList = [];

  if(deptId){
    fetch(`../controller/get_employees.php?dept_id=${deptId}`)
      .then(res => res.json())
      .then(data => {
        employeeList = data.employees;
        updateDatalist(''); // show all initially
      });
  }
});

function updateDatalist(filter){
  const empDatalist = document.getElementById('employee-list');
  empDatalist.innerHTML = '';
  employeeList
    .filter(emp => emp.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(emp=>{
      const display = `${emp.name} (${emp.emp_id})`;
      const option = document.createElement('option');
      option.value = display;
      empDatalist.appendChild(option);
    });
}

// Extract employee_id from selected datalist
document.getElementById('employee').addEventListener('input', function(){
  const value = this.value.trim();
  const hiddenEmpId = document.getElementById('employee_id');
  const match = value.match(/\(([^)]+)\)$/); // get id inside parentheses
  if(match){ hiddenEmpId.value = match[1]; }
  else{ hiddenEmpId.value = ''; }
  updateDatalist(value); // filter suggestions
});

// =====================
// Show Allowances & Summary on Next
// =====================
document.getElementById('next-btn').addEventListener('click', function(){
  const empId = document.getElementById('employee_id').value;
  if(!empId){ alert('Select employee first'); return; }

  document.getElementById('allow-deduct').style.display = 'block';
  document.getElementById('summary').style.display = 'block';
  document.getElementById('allow-deduct').scrollIntoView({behavior:'smooth'});
});

// =====================
// Dynamic Add/Remove Rows
// =====================
document.addEventListener('click', function(e){
  if(e.target.classList.contains('add-allowance-btn')){
    const container = document.getElementById('allowances-container');
    const newRow = e.target.closest('.allowance-row').cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(inp=>{ if(!inp.classList.contains('allowancename')) inp.value=''; });
    container.appendChild(newRow);
  }
  if(e.target.classList.contains('remove-allowance-btn')){
    if(document.querySelectorAll('.allowance-row').length>1)
      e.target.closest('.allowance-row').remove();
  }
  if(e.target.classList.contains('add-deduction-btn')){
    const container = document.getElementById('deductions-container');
    const newRow = e.target.closest('.deduction-row').cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(inp=>{ if(!inp.classList.contains('deductionname')) inp.value=''; });
    container.appendChild(newRow);
  }
  if(e.target.classList.contains('remove-deduction-btn')){
    if(document.querySelectorAll('.deduction-row').length>1)
      e.target.closest('.deduction-row').remove();
  }
});

// =====================
// Net Salary Calculation
// =====================
const basicSalary = document.getElementById('basic-salary');
const totalAllowance = document.getElementById('total-allowance');
const totalDeduction = document.getElementById('total-deduction');
const netSalary = document.getElementById('net-salary');

function calculateNetSalary(){
  const basic = parseFloat(basicSalary.value)||0;
  const allowance = parseFloat(totalAllowance.value)||0;
  const deduction = parseFloat(totalDeduction.value)||0;
  netSalary.value = (basic + allowance - deduction).toFixed(2);
}

[basicSalary, totalAllowance, totalDeduction].forEach(el=>el.addEventListener('input', calculateNetSalary));

</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
