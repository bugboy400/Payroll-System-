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

<!-- Allowances & Deductions -->
<div id="allow-deduct" class="container-fluid mt-4" style="display: none;">
  <div class="row g-4">
    <!-- Allowances -->
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
          <div class="col-6"><input type="text" name="otherallowancetitle" class="form-control" placeholder="Title"></div>
          <div class="col-6"><input type="number" name="otherallowanceamt" class="form-control" placeholder="Amount"></div>
        </div>
      </div>
    </div>

    <!-- Deductions -->
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
          <div class="col-6"><input type="text" name="otherdeductiontitle" class="form-control" placeholder="Title"></div>
          <div class="col-6"><input type="number" name="otherdeductionamt" class="form-control" placeholder="Amount"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Summary -->
<div id="summary" style="display: none;">
  <h3>Summary</h3>
  <div class="form-container">
    <div class="form-rowsum"><label>Basic Salary</label><input type="number" id="basic-salary"></div>
    <div class="form-rowsum"><label>Total Allowance</label><input type="number" id="total-allowance" readonly></div>
    <div class="form-rowsum"><label>Total Deduction</label><input type="number" id="total-deduction" readonly></div>
    <div class="form-rowsum"><label>Net Salary</label><input type="number" id="net-salary" readonly></div>
    <div class="form-rowsum"><label>Status</label>
      <select id="status"><option value="Paid">Paid</option><option value="Unpaid" selected>Unpaid</option></select>
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
// Employee Autocomplete
// =====================
let employeeList = [];
document.getElementById('department').addEventListener('change', function(){
  const deptId = this.value;
  const empInput = document.getElementById('employee');
  const empDatalist = document.getElementById('employee-list');
  const hiddenEmpId = document.getElementById('employee_id');

  empInput.value = ''; empDatalist.innerHTML = ''; hiddenEmpId.value = ''; employeeList = [];

  if(deptId){
    fetch(`../controller/get_employees.php?dept_id=${deptId}`)
      .then(res=>res.json())
      .then(data=>{ employeeList = data.employees; updateDatalist(''); });
  }
});

function updateDatalist(filter){
  const empDatalist = document.getElementById('employee-list');
  empDatalist.innerHTML = '';
  employeeList.filter(emp=>emp.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(emp=>{ 
      const opt = document.createElement('option'); 
      opt.value = `${emp.name} (${emp.emp_id})`; 
      empDatalist.appendChild(opt); 
    });
}

document.getElementById('employee').addEventListener('input', function(){
  const val = this.value.trim(); 
  const hiddenEmpId = document.getElementById('employee_id');
  const match = val.match(/\(([^)]+)\)$/);
  hiddenEmpId.value = match ? match[1] : '';
  updateDatalist(val);
});

// =====================
// Next Button: Fetch and Populate Employee Data
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

      calculateTotals(); // calculate totals on load
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
  const otherAllow = parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
  totalA += otherAllow;
  totalAllowance.value = totalA;

  document.querySelectorAll('.deductionamt').forEach(d=>{ totalD += parseFloat(d.value)||0; });
  const otherDeduct = parseFloat(document.querySelector('input[name="otherdeductionamt"]').value)||0;
  totalD += otherDeduct;
  totalDeduction.value = totalD;

  const basic = parseFloat(basicSalary.value)||0;
  netSalary.value = (basic + totalA - totalD).toFixed(2);
}

// Attach listener to input
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

  // Include Other Allowance
  const otherAllowTitle = document.querySelector('input[name="otherallowancetitle"]').value.trim();
  const otherAllowAmt = parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
  if(otherAllowTitle && otherAllowAmt>0) data.allowances.push({name: otherAllowTitle, amount: otherAllowAmt});

  // Include Other Deduction
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
