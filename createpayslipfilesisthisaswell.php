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

        <!-- Employee (Searchable with ID, Gender, Marital Status) -->
        <div class="col-md-6">
          <label class="form-label fw-bold">Employee</label>
          <div class="d-flex gap-2 flex-wrap">
            <input list="employee-list" id="employee" class="form-control p-2" placeholder="Search Employee" style="flex:2;">
            <input type="text" id="employee_id_display" class="form-control p-2" placeholder="Employee ID" readonly style="width:100px;">
            <input type="text" id="gender_display" class="form-control p-2" placeholder="Gender" readonly style="width:100px;">
            <input type="text" id="marital_status_display" class="form-control p-2" placeholder="Marital Status" readonly style="width:120px;">
          </div>
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

<!-- Allowances & Deductions -->
<div id="allow-deduct" class="container-fluid mt-4" style="display: none;">
  <!-- Allowances and Deductions same as before... (keep existing code) -->
</div>

<!-- Summary -->
<div id="summary" style="display: none;">
  <!-- Summary same as before... (keep existing code) -->
</div>

<script>
// =====================
// Initialize Year & Month
// =====================
document.getElementById('year').value = new Date().getFullYear();
const monthSelect = document.getElementById('month');
["January","February","March","April","May","June","July","August","September","October","November","December"]
.forEach((m,i)=>{ 
  if(i <= new Date().getMonth()){ 
    const option = document.createElement('option'); 
    option.value = m; option.textContent = m; 
    monthSelect.appendChild(option); 
  }
});

// =====================
// Load Departments
// =====================
function loadDepartments() {
  fetch('../controller/fetchdept_payslip.php')
    .then(res => res.json())
    .then(data => {
      const deptSelect = document.getElementById('department');
      data.departments.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.dept_id;
        opt.textContent = d.department_name;
        deptSelect.appendChild(opt);
      });
    });
}
loadDepartments();

// =====================
// Employee Autocomplete (with ID, Gender, Marital Status)
// =====================
let employeeList = [];
document.getElementById('department').addEventListener('change', function(){
  const deptId = this.value;
  const empInput = document.getElementById('employee');
  const empDatalist = document.getElementById('employee-list');

  // Reset all fields
  empInput.value = ''; 
  empDatalist.innerHTML = ''; 
  document.getElementById('employee_id').value = '';
  document.getElementById('employee_id_display').value = '';
  document.getElementById('gender_display').value = '';
  document.getElementById('marital_status_display').value = '';
  employeeList = [];

  if(deptId){
    fetch(`../controller/get_employees.php?dept_id=${deptId}`)
      .then(res=>res.json())
      .then(data=>{ 
        employeeList = data.employees; 
        updateDatalist('');
      });
  }
});

function updateDatalist(filter){
  const empDatalist = document.getElementById('employee-list');
  empDatalist.innerHTML = '';
  employeeList.filter(emp=>emp.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(emp=>{ 
      const opt = document.createElement('option'); 
      opt.value = emp.name; 
      opt.dataset.id = emp.emp_id; 
      opt.dataset.gender = emp.gender;
      opt.dataset.marital = emp.marital_status;
      empDatalist.appendChild(opt); 
    });
}

document.getElementById('employee').addEventListener('input', function(){
  const val = this.value.trim();
  const match = employeeList.find(emp => emp.name === val);
  if(match){
    document.getElementById('employee_id').value = match.emp_id;
    document.getElementById('employee_id_display').value = match.emp_id;
    document.getElementById('gender_display').value = match.gender || '';
    document.getElementById('marital_status_display').value = match.marital_status || '';
  } else {
    document.getElementById('employee_id').value = '';
    document.getElementById('employee_id_display').value = '';
    document.getElementById('gender_display').value = '';
    document.getElementById('marital_status_display').value = '';
  }
});

// =====================
// Next Button: Fetch Employee Data & Populate Allowances/Deductions
// =====================
// ... keep existing code for next button, dynamic rows, calculate totals, create payslip
// =====================
// Next Button: Fetch Employee Data
// =====================
document.getElementById('next-btn').addEventListener('click', function(){
  const empId = document.getElementById('employee_id').value;
  if(!empId){ alert('Select employee first'); return; }

  const allowDeduct = document.getElementById('allow-deduct');
  const summary = document.getElementById('summary');
  allowDeduct.style.display='block';
  summary.style.display='block';
  allowDeduct.scrollIntoView({behavior:'smooth'});

  fetch(`/payrollself/controller/get_employee_finance.php?emp_id=${empId}`)
    .then(res=>res.json())
    .then(data=>{
      document.getElementById('basic-salary').value = data.basic_salary || 0;

      const allowContainer = document.getElementById('allowances-container');
      allowContainer.innerHTML = '';
      if(data.allowances.length){
        data.allowances.forEach(a=>{
          const row = document.createElement('div');
          row.className = 'form-row allowance-row';
          row.innerHTML = `
            <select class="allowancename">
              <option value="homeallowance">Home Allowance</option>
              <option value="healthallowance">Health Allowance</option>
              <option value="overtimeallowance">OT Allowance</option>
              <option value="festiveallowance">Festive Allowance</option>
            </select>
            <input type="number" class="allowanceamt" value="${a.amount}">
            <button type="button" class="btn-icon add-btn add-allowance-btn">+</button>
            <button type="button" class="btn-icon remove-btn remove-allowance-btn">×</button>
          `;
          row.querySelector('.allowancename').value = a.name;
          allowContainer.appendChild(row);
        });
      }

      const deductContainer = document.getElementById('deductions-container');
      deductContainer.innerHTML = '';
      if(data.deductions.length){
        data.deductions.forEach(d=>{
          const row = document.createElement('div');
          row.className = 'form-row deduction-row';
          row.innerHTML = `
            <select class="deductionname">
              <option value="providentfund">Provident Fund</option>
              <option value="leave">Leave</option>
            </select>
            <input type="number" class="deductionamt" value="${d.amount}">
            <button type="button" class="btn-icon add-btn add-deduction-btn">+</button>
            <button type="button" class="btn-icon remove-btn remove-deduction-btn">×</button>
          `;
          row.querySelector('.deductionname').value = d.name;
          deductContainer.appendChild(row);
        });
      }

      calculateTotals();
    });
});

// =====================
// Dynamic Add/Remove Rows
// =====================
document.addEventListener('click', function(e){
  if(e.target.classList.contains('add-allowance-btn')){
    const container = document.getElementById('allowances-container');
    const newRow = e.target.closest('.allowance-row').cloneNode(true);
    newRow.querySelectorAll('input').forEach(inp=>inp.value='');
    container.appendChild(newRow);
    attachInputListener(newRow.querySelector('.allowanceamt'));
  }
  if(e.target.classList.contains('remove-allowance-btn') && document.querySelectorAll('.allowance-row').length>1)
    e.target.closest('.allowance-row').remove();
  if(e.target.classList.contains('add-deduction-btn')){
    const container = document.getElementById('deductions-container');
    const newRow = e.target.closest('.deduction-row').cloneNode(true);
    newRow.querySelectorAll('input').forEach(inp=>inp.value='');
    container.appendChild(newRow);
    attachInputListener(newRow.querySelector('.deductionamt'));
  }
  if(e.target.classList.contains('remove-deduction-btn') && document.querySelectorAll('.deduction-row').length>1)
    e.target.closest('.deduction-row').remove();
});

// =====================
// Calculate Totals & Net Salary
// =====================
const basicSalary = document.getElementById('basic-salary');
const totalAllowance = document.getElementById('total-allowance');
const totalDeduction = document.getElementById('total-deduction');
const netSalary = document.getElementById('net-salary');

function calculateTotals(){
  let totalA = 0, totalD = 0;
  
  document.querySelectorAll('.allowanceamt').forEach(a=>{ totalA += parseFloat(a.value)||0; });
  totalA += parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
  totalAllowance.value = totalA;

  document.querySelectorAll('.deductionamt').forEach(d=>{ totalD += parseFloat(d.value)||0; });
  totalD += parseFloat(document.querySelector('input[name="otherdeductionamt"]').value)||0;
  totalDeduction.value = totalD;

  const basic = parseFloat(basicSalary.value)||0;
  netSalary.value = (basic + totalA - totalD).toFixed(2);
}

function attachInputListener(input){
  input.addEventListener('input', calculateTotals);
}
[basicSalary, document.querySelector('input[name="otherallowanceamt"]'), document.querySelector('input[name="otherdeductionamt"]')].forEach(el=>el.addEventListener('input', calculateTotals));
document.querySelectorAll('.allowanceamt, .deductionamt').forEach(attachInputListener);

// =====================
// Create Payslip
// =====================
document.getElementById('create-payslip-btn').addEventListener('click', function(){
  const data = {
    employee_id: document.getElementById('employee_id').value,
    department_id: document.getElementById('department').value,
    year: document.getElementById('year').value,
    month: document.getElementById('month').value,
    basic_salary: parseFloat(basicSalary.value)||0,
    total_allowance: parseFloat(totalAllowance.value)||0,
    total_deduction: parseFloat(totalDeduction.value)||0,
    net_salary: parseFloat(netSalary.value)||0,
    status: document.getElementById('status').value,
    allowances: Array.from(document.querySelectorAll('.allowance-row')).map(r=>({
      name: r.querySelector('.allowancename').value,
      amount: parseFloat(r.querySelector('.allowanceamt').value)||0
    })),
    deductions: Array.from(document.querySelectorAll('.deduction-row')).map(r=>({
      name: r.querySelector('.deductionname').value,
      amount: parseFloat(r.querySelector('.deductionamt').value)||0
    }))
  };

  const otherAllowTitle = document.querySelector('input[name="otherallowancetitle"]').value.trim();
  const otherAllowAmt = parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
  if(otherAllowTitle && otherAllowAmt>0) data.allowances.push({name: otherAllowTitle, amount: otherAllowAmt});

  const otherDeductTitle = document.querySelector('input[name="otherdeductiontitle"]').value.trim();
  const otherDeductAmt = parseFloat(document.querySelector('input[name="otherdeductionamt"]').value)||0;
  if(otherDeductTitle && otherDeductAmt>0) data.deductions.push({name: otherDeductTitle, amount: otherDeductAmt});

  fetch('../controller/create_payslip_backend.php', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(data)
  })
  .then(res=>res.json())
  .then(res=>alert(res.message || res.error));
});
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
