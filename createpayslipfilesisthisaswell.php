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

<!-- Employee Payment History -->
<div id="payment-history" class="container mt-4 p-4" style="display:none; background:#fff; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.08);">
  <h4>Payment History</h4>
  <table class="table table-bordered mt-3">
    <thead>
      <tr>
        <th>Month</th>
        <th>Year</th>
        <th>Basic Salary</th>
        <th>Total Allowance</th>
        <th>Total Deduction</th>
        <th>Net Salary</th>
        <th>Paid On</th>
      </tr>
    </thead>
    <tbody id="payment-history-body"></tbody>
  </table>
</div>

<!-- ALLOWANCES & DEDUCTIONS -->
<div id="allow-deduct" class="container-fluid mt-4" style="display: none;">
  <div class="row g-4">
    <!-- ALLOWANCE -->
    <div class="col-lg-6">
      <div class="form-section">
        <h5>Allowances</h5>
        <div id="allowances-container"></div>
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
        <div id="deductions-container"></div>
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
      <input type="number" id="basic-salary">
    </div>
    <div class="form-rowsum">
      <label for="total-allowance">Total Allowance</label>
      <input type="number" id="total-allowance">
    </div>
    <div class="form-rowsum">
      <label for="total-deduction">Total Deduction</label>
      <input type="number" id="total-deduction">
    </div>
    <div class="form-rowsum">
      <label for="net-salary">Net Salary</label>
      <input type="number" id="net-salary" readonly>
    </div>
    <div class="form-rowsum">
      <label for="status">Status</label>
      <select id="status">
        <option value="Paid">Paid</option>
        <option value="Unpaid" selected>Unpaid</option>
      </select>
    </div>
    <button type="button" id="create-payslip-btn">Create Payslip</button>
  </div>
</div>

<script>
// ===== Initialize =====
document.getElementById('year').value = new Date().getFullYear();
const monthSelect = document.getElementById('month');
const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
monthNames.forEach((m,i)=>{ if(i<=new Date().getMonth()){ const option = document.createElement('option'); option.value = m; option.textContent = m; monthSelect.appendChild(option); }});

// Load Departments
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

// Employee autocomplete
let employeeMap = {};
document.getElementById('department').addEventListener('change', function() {
  const deptId = this.value;
  const empInput = document.getElementById('employee');
  const empDatalist = document.getElementById('employee-list');
  const hiddenEmpId = document.getElementById('employee_id');
  empInput.value = ''; empDatalist.innerHTML = ''; hiddenEmpId.value = '';
  employeeMap = {};
  if(deptId){
    fetch(`../controller/get_employees.php?dept_id=${deptId}`)
      .then(res => res.json())
      .then(data => {
        data.employees.forEach(emp=>{
          const display = `${emp.name} (${emp.emp_id})`;
          const option = document.createElement('option');
          option.value = display;
          empDatalist.appendChild(option);
          employeeMap[display] = emp.emp_id;
        });
      });
  }
});

// ===== Helper: Render Allowances/Deductions =====
function renderAllowances(allowances){
  const container = document.getElementById('allowances-container');
  container.innerHTML = '';
  allowances.forEach(a=>{
    const row = document.createElement('div');
    row.classList.add('form-row','allowance-row');
    row.innerHTML = `<input type="text" class="allowancename" value="${a.name}" readonly>
                     <input type="number" class="allowanceamt" value="${a.amt}">
                     <button type="button" class="btn-icon add-btn add-allowance-btn">+</button>
                     <button type="button" class="btn-icon remove-btn remove-allowance-btn">×</button>`;
    container.appendChild(row);
  });
}
function renderDeductions(deductions){
  const container = document.getElementById('deductions-container');
  container.innerHTML = '';
  deductions.forEach(d=>{
    const row = document.createElement('div');
    row.classList.add('form-row','deduction-row');
    row.innerHTML = `<input type="text" class="deductionname" value="${d.name}" readonly>
                     <input type="number" class="deductionamt" value="${d.amt}">
                     <button type="button" class="btn-icon add-btn add-deduction-btn">+</button>
                     <button type="button" class="btn-icon remove-btn remove-deduction-btn">×</button>`;
    container.appendChild(row);
  });
}

// ===== Employee Selection & Fetch Data =====
document.getElementById('employee').addEventListener('input', function(){
  const value = this.value;
  const hiddenEmpId = document.getElementById('employee_id');
  hiddenEmpId.value = employeeMap[value] || '';
  if(hiddenEmpId.value){
    // only fetch when Next is clicked
  }
});

// ===== Show Form on Next =====
document.getElementById('next-btn').addEventListener('click', function(){
  const empId = document.getElementById('employee_id').value;
  if(!empId){ alert('Select employee first'); return; }

  fetch(`../controller/check_payment.php?emp_id=${empId}`)
    .then(res=>res.json())
    .then(data=>{
      // Payment history if already paid
      if(data.already_paid){
        const historyDiv = document.getElementById('payment-history');
        const historyBody = document.getElementById('payment-history-body');
        historyBody.innerHTML = '';
        data.payments.forEach(p=>{
          historyBody.innerHTML += `<tr>
            <td>${p.month}</td><td>${p.year}</td><td>${p.basic_salary}</td>
            <td>${p.total_allowance}</td><td>${p.total_deduction}</td>
            <td>${p.net_salary}</td><td>${p.created_at}</td>
          </tr>`;
        });
        historyDiv.style.display = 'block';
        document.getElementById('allow-deduct').style.display = 'none';
        document.getElementById('summary').style.display = 'none';
      } else {
        document.getElementById('payment-history').style.display = 'none';
        document.getElementById('allow-deduct').style.display = 'block';
        document.getElementById('summary').style.display = 'block';
        // Fill data
        document.getElementById('basic-salary').value = data.basic_salary||0;
        document.getElementById('total-allowance').value = data.total_allowance||0;
        document.getElementById('total-deduction').value = data.total_deduction||0;
        calculateNetSalary();
        renderAllowances(data.allowances||[]);
        renderDeductions(data.deductions||[]);
      }
    });
});

// ===== Dynamic Rows =====
document.addEventListener('click', function(e){
  if(e.target.classList.contains('add-allowance-btn')){
    const container = document.getElementById('allowances-container');
    const newRow = e.target.closest('.allowance-row').cloneNode(true);
    newRow.querySelectorAll('input').forEach(input=>{ if(!input.classList.contains('allowancename')) input.value=''; });
    container.appendChild(newRow);
  }
  if(e.target.classList.contains('remove-allowance-btn')){
    const rows = document.querySelectorAll('.allowance-row'); if(rows.length>1) e.target.closest('.allowance-row').remove();
  }
  if(e.target.classList.contains('add-deduction-btn')){
    const container = document.getElementById('deductions-container');
    const newRow = e.target.closest('.deduction-row').cloneNode(true);
    newRow.querySelectorAll('input').forEach(input=>{ if(!input.classList.contains('deductionname')) input.value=''; });
    container.appendChild(newRow);
  }
  if(e.target.classList.contains('remove-deduction-btn')){
    const rows = document.querySelectorAll('.deduction-row'); if(rows.length>1) e.target.closest('.deduction-row').remove();
  }
});

// ===== Net Salary Calculation =====
const basicSalary = document.getElementById('basic-salary');
const totalAllowance = document.getElementById('total-allowance');
const totalDeduction = document.getElementById('total-deduction');
const netSalary = document.getElementById('net-salary');
function calculateNetSalary(){
  const basic = parseFloat(basicSalary.value)||0;
  const allowance = parseFloat(totalAllowance.value)||0;
  const deduction = parseFloat(totalDeduction.value)||0;
  netSalary.value = (basic+allowance-deduction).toFixed(2);
}
[basicSalary,totalAllowance,totalDeduction].forEach(el=>el.addEventListener('input',calculateNetSalary));

// ===== Create Payslip =====
document.getElementById('create-payslip-btn').addEventListener('click', function(){
  const empId = document.getElementById('employee_id').value;
  const deptId = document.getElementById('department').value;
  const year = document.getElementById('year').value;
  const month = document.getElementById('month').value;
  if(!empId || !month){ alert('Select employee and month first'); return; }

  // Collect allowances
  let allowances = [];
  document.querySelectorAll('#allowances-container .allowance-row').forEach(row=>{
    const name = row.querySelector('.allowancename').value;
    const amt = parseFloat(row.querySelector('.allowanceamt').value)||0;
    if(amt>0) allowances.push({name, amt});
  });
  const otherTitle = document.querySelector('input[name="otherallowancetitle"]').value;
  const otherAmt = parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
  if(otherAmt>0 && otherTitle) allowances.push({name:otherTitle, amt:otherAmt});

  // Collect deductions
  let deductions = [];
  document.querySelectorAll('#deductions-container .deduction-row').forEach(row=>{
    const name = row.querySelector('.deductionname').value;
    const amt = parseFloat(row.querySelector('.deductionamt').value)||0;
    if(amt>0) deductions.push({name, amt});
  });
  const otherDedTitle = document.querySelector('input[name="otherdeductiontitle"]').value;
  const otherDedAmt = parseFloat(document.querySelector('input[name="otherdeductionamt"]').value)||0;
  if(otherDedAmt>0 && otherDedTitle) deductions.push({name:otherDedTitle, amt:otherDedAmt});

  const payload = {
    employee_id: empId,
    dept_id: deptId,
    year, month,
    basic_salary: parseFloat(basicSalary.value)||0,
    total_allowance: parseFloat(totalAllowance.value)||0,
    total_deduction: parseFloat(totalDeduction.value)||0,
    net_salary: parseFloat(netSalary.value)||0,
    status: document.getElementById('status').value,
    allowances, deductions
  };

  fetch('../controller/save_payment.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify(payload)
  }).then(res=>res.json())
    .then(data=>{
      if(data.success){ alert('Payslip created successfully'); location.reload(); }
      else{ alert('Error: '+data.message); }
    });
});
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
