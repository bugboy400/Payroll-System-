<?php
session_start();

// If no active session, redirect to login
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

// Prevent browser from caching this page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$page_title = "Create Payslip";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/createpayslip.css"
];

ob_start();
?>

<div id="main-content">
  <h3>Create Payslip</h3>

  <div class="container mt-4 p-4" style="background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(235, 189, 189, 0.08);">
    <form id="payslip-form">
      <div class="row g-4 align-items-end">
        <!-- Department -->
        <div class="col-md-3">
          <label for="department" class="form-label fw-bold">Department</label>
          <select id="department" class="form-control p-2">
            <option value="">Select Department</option>
          </select>
        </div>

        <!-- Employee -->
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

<!-- Employee Info Card -->
<div id="employee-info" class="container-fluid mt-4" style="display:none;">
  <div class="card p-3" style="background:#f8f9fa; border-radius:8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label fw-bold">Gender</label>
        <input type="text" id="gender" class="form-control p-2" readonly style="background:#e9ecef; font-weight:500;">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-bold">Marital Status</label>
        <input type="text" id="marital-status" class="form-control p-2" readonly style="background:#e9ecef; font-weight:500;">
      </div>
    </div>
  </div>
</div>

<!-- Allowances & Deductions -->
<div id="allow-deduct" class="container-fluid mt-4" style="display:none;">
  <div class="row g-4">
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
        <h6>Other Allowance</h6>
        <div class="row mt-2">
          <div class="col-6"><input type="text" name="otherallowancetitle" class="form-control" placeholder="Title"></div>
          <div class="col-6"><input type="number" name="otherallowanceamt" class="form-control" placeholder="Amount"></div>
        </div>
      </div>
    </div>

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
        <h6>Other Deduction</h6>
        <div class="row mt-2">
          <div class="col-6"><input type="text" name="otherdeductiontitle" class="form-control" placeholder="Title"></div>
          <div class="col-6"><input type="number" name="otherdeductionamt" class="form-control" placeholder="Amount"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Summary -->
<div id="summary" style="display:none;">
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
// ===== Initialize Year & Month =====
const yearInput = document.getElementById('year');
yearInput.value = new Date().getFullYear();
const monthSelect = document.getElementById('month');
["January","February","March","April","May","June","July","August","September","October","November","December"]
.forEach((m,i)=>{
  if(i <= new Date().getMonth()){
    const option = document.createElement('option');
    option.value = m;
    option.textContent = m;
    monthSelect.appendChild(option);
  }
});

// ===== Load Departments =====
fetch('../controller/fetchdept_payslip.php')
  .then(res => res.json())
  .then(data => {
    const deptSelect = document.getElementById('department');
    data.departments.forEach(d=>{
      const opt = document.createElement('option');
      opt.value = d.dept_id;
      opt.textContent = d.department_name;
      deptSelect.appendChild(opt);
    });
  })
  .catch(e=>console.error('Error loading departments:', e));

// ===== Employee Autocomplete =====
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
      .then(data => { employeeList = data.employees; updateDatalist(''); })
      .catch(e => console.error('Error fetching employees:', e));
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

// ===== Next Button =====
document.getElementById('next-btn').addEventListener('click', function(){
  const empId = document.getElementById('employee_id').value;
  if(!empId){ alert('Select employee first'); return; }

  fetch(`../controller/get_employee_finance.php?emp_id=${empId}`)
    .then(res => {
      if(!res.ok) throw new Error('Network response was not ok: '+res.status);
      return res.json();
    })
    .then(data => {
      if(!data) { alert('No employee data found'); return; }

      // Show cards
      document.getElementById('employee-info').style.display='flex';
      document.getElementById('allow-deduct').style.display='block';
      document.getElementById('summary').style.display='block';

      // Populate info
      document.getElementById('gender').value = data.gender || '';
      document.getElementById('marital-status').value = data.marital_status || '';
      document.getElementById('basic-salary').value = data.basic_salary || 0;

      // Allowances
      const allowContainer = document.getElementById('allowances-container');
      allowContainer.innerHTML = '';
      if(data.allowances && data.allowances.length){
        data.allowances.forEach(a=>{
          const row = document.createElement('div');
          row.className='form-row allowance-row';
          row.innerHTML=`
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

      // Deductions
      const deductContainer = document.getElementById('deductions-container');
      deductContainer.innerHTML='';

      // PF
      const pfRow = document.createElement('div');
      pfRow.className='form-row deduction-row auto-row';
      pfRow.innerHTML=`<label style="flex:1; font-weight:bold; color:#0056b3;">Provident Fund (10%)</label>
        <input type="number" class="deductionamt pf-amt" readonly style="background:#eaf4ff; font-weight:bold;">`;
      deductContainer.appendChild(pfRow);

      // Tax
      const taxRow = document.createElement('div');
      taxRow.className='form-row deduction-row auto-row';
      taxRow.innerHTML=`<label style="flex:1; font-weight:bold; color:#b30000;">Tax (<span id="tax-percent">0%</span>)</label>
        <input type="number" class="deductionamt tax-amt" readonly style="background:#ffeaea; font-weight:bold;">`;
      deductContainer.appendChild(taxRow);

      // Leave
      const leaveRow = document.createElement('div');
      leaveRow.className='form-row deduction-row';
      leaveRow.innerHTML=`<select class="deductionname"><option value="leave">Leave</option></select>
        <input type="number" class="deductionamt" value="0">
        <button type="button" class="btn-icon add-btn add-deduction-btn">+</button>
        <button type="button" class="btn-icon remove-btn remove-deduction-btn">×</button>`;
      deductContainer.appendChild(leaveRow);

      calculateTotals();
    })
    .catch(err => { console.error(err); alert('Failed to fetch employee finance data'); });
});

// ===== The rest of your add/remove row, calculateTotals, create payslip code remains the same =====

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
    if(!newRow.classList.contains('auto-row')){ // prevent cloning PF/Tax
      newRow.querySelectorAll('input').forEach(inp=>inp.value='');
      container.appendChild(newRow);
      attachInputListener(newRow.querySelector('.deductionamt'));
    }
  }
  if(e.target.classList.contains('remove-deduction-btn') && !e.target.closest('.deduction-row').classList.contains('auto-row'))
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

  const basic = parseFloat(basicSalary.value)||0;

  // PF Calculation (10%)
  const pfAmt = (basic * 0.10).toFixed(2);
  const pfInput = document.querySelector('.pf-amt');
  if(pfInput) pfInput.value = pfAmt;

  // Tax Calculation (Simplified)
  let taxableIncome = (basic + totalA) - pfAmt;
  let taxPercent = 0, taxAmt = 0;
  if(taxableIncome > 50000){ taxPercent = 10; taxAmt = taxableIncome*0.10; }
  if(taxableIncome > 100000){ taxPercent = 20; taxAmt = taxableIncome*0.20; }
  document.getElementById('tax-percent').innerText = taxPercent+"%";
  const taxInput = document.querySelector('.tax-amt');
  if(taxInput) taxInput.value = taxAmt.toFixed(2);

  // Other Deductions
  document.querySelectorAll('.deductionamt').forEach(d=>{ 
    if(!d.classList.contains('pf-amt') && !d.classList.contains('tax-amt'))
      totalD += parseFloat(d.value)||0; 
  });

  totalD += parseFloat(pfAmt)||0;
  totalD += parseFloat(taxAmt)||0;
  totalDeduction.value = totalD;

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
    deductions: []
  };

  // Include PF & Tax
  const pfVal = parseFloat(document.querySelector('.pf-amt').value)||0;
  const taxVal = parseFloat(document.querySelector('.tax-amt').value)||0;
  data.deductions.push({name:'Provident Fund', amount:pfVal});
  data.deductions.push({name:'Tax', amount:taxVal});

  // Include Other Deductions
  Array.from(document.querySelectorAll('.deduction-row')).forEach(r=>{
    const nameEl = r.querySelector('.deductionname');
    const amtEl = r.querySelector('.deductionamt');
    if(nameEl && amtEl){
      const name = nameEl.value;
      const amt = parseFloat(amtEl.value)||0;
      if(name && amt>0) data.deductions.push({name, amount: amt});
    }
  });

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
