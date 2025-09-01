<?php
session_start();

// If no active session, redirect to login
if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

// Prevent caching
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

<!-- Employee Info -->
<div id="employee-info" style="display:none; margin-top:15px; background:#f9f9f9; padding:10px; border-radius:6px;">
  <h5>Employee Details</h5>
  <p><strong>Name:</strong> <span id="emp-name"></span></p>
  <p><strong>Employee ID:</strong> <span id="emp-id-display"></span></p>
  <p><strong>Gender:</strong> <span id="emp-gender"></span></p>
  <p><strong>Marital Status:</strong> <span id="emp-marital"></span></p>
</div>

<!-- Allowances & Deductions -->
<div id="allow-deduct" class="container-fluid mt-4" style="display: none;">
  <div class="row g-4">
    <!-- Allowances -->
    <div class="col-lg-6">
      <div class="form-section">
        <h5>Allowances</h5>
        <div id="allowances-container"></div>

        <hr class="mt-4">
        <h6 class="mt-3">Other Allowance</h6>
        <div class="row mt-2">
          <div class="col-6"><input type="text" name="otherallowancetitle" class="form-control" placeholder="Title"></div>
          <div class="col-6"><input type="number" name="otherallowanceamt" class="form-control" placeholder="Amount" value="0"></div>
        </div>
      </div>
    </div>

    <!-- Deductions -->
    <div class="col-lg-6">
      <div class="form-section">
        <h5>Deductions</h5>
        <div id="deductions-container"></div>

        <hr class="mt-4">
        <h6 class="mt-3">Other Deduction</h6>
        <div class="row mt-2">
          <div class="col-6"><input type="text" name="otherdeductiontitle" class="form-control" placeholder="Title"></div>
          <div class="col-6"><input type="number" name="otherdeductionamt" class="form-control" placeholder="Amount" value="0"></div>
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
const empInput = document.getElementById('employee');
const empDatalist = document.getElementById('employee-list');
const hiddenEmpId = document.getElementById('employee_id');
let employeeList = [];

document.getElementById('department').addEventListener('change', function(){
  const deptId = this.value;
  empInput.value = '';
  empDatalist.innerHTML = '';
  hiddenEmpId.value = '';
  employeeList = [];
  if(!deptId) return;

  fetch(`../controller/get_employees.php?dept_id=${deptId}`)
    .then(res=>res.json())
    .then(data => {
      employeeList = data.employees;
      updateDatalist('');
    });
});

function updateDatalist(filter){
  empDatalist.innerHTML = '';
  employeeList.filter(emp=>emp.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(emp=>{
      const opt = document.createElement('option');
      opt.value = emp.name;
      opt.dataset.id = emp.emp_id;
      empDatalist.appendChild(opt);
    });
}

empInput.addEventListener('input', function(){
  const emp = employeeList.find(e=>e.name.toLowerCase() === empInput.value.toLowerCase());
  hiddenEmpId.value = emp ? emp.emp_id : '';
});

empInput.addEventListener('blur', function(){
  if(empInput.value && !hiddenEmpId.value){
    alert('Please select an employee from the list!');
    empInput.value = '';
  }
});

// =====================
// Nepal FY 2082/83 Tax Calculation
// =====================
function calculateNepalTax(annualIncome){
    const slabs = [
        { upto: 500000, rate: 0.01 },
        { upto: 700000, rate: 0.10 },
        { upto: 1000000, rate: 0.20 },
        { upto: 2000000, rate: 0.30 },
        { upto: 5000000, rate: 0.36 },
        { upto: Infinity, rate: 0.39 }
    ];
    let remaining = annualIncome;
    let prevLimit = 0;
    let tax = 0;
    for(const slab of slabs){
        const taxable = Math.min(remaining, slab.upto - prevLimit);
        if(taxable > 0){
            tax += taxable * slab.rate;
            remaining -= taxable;
        }
        prevLimit = slab.upto;
        if(remaining <= 0) break;
    }
    return tax;
}

// =====================
// NEXT BUTTON CLICK
// =====================
const basicSalary = document.getElementById('basic-salary');
const totalAllowance = document.getElementById('total-allowance');
const totalDeduction = document.getElementById('total-deduction');
const netSalary = document.getElementById('net-salary');

document.getElementById('next-btn').addEventListener('click', async function() {
    const empId = hiddenEmpId.value;
    const year = document.getElementById('year').value;
    const month = document.getElementById('month').value;

    if(!empId){ alert('Select employee first'); return; }
    if(!month){ alert('Select month'); return; }

    try {
        const res = await fetch(`../controller/get_employee_finance.php?emp_id=${empId}`);
        const data = await res.json();
        if(data.error){ alert(data.error); return; }

        const emp = employeeList.find(e => e.emp_id == empId);
        document.getElementById('emp-name').textContent = emp.name;
        document.getElementById('emp-id-display').textContent = emp.emp_id;
        document.getElementById('emp-gender').textContent = data.gender;
        document.getElementById('emp-marital').textContent = data.marital_status;
        document.getElementById('employee-info').style.display = 'block';
        document.getElementById('allow-deduct').style.display = 'block';
        document.getElementById('summary').style.display = 'block';

        basicSalary.value = data.basic_salary;

        // ===== Allowances =====
        const allowanceContainer = document.getElementById('allowances-container');
        allowanceContainer.innerHTML = '';
        const allAllowanceOptions = ["homeallowance","healthallowance","overtimeallowance","festiveallowance"];

        // Convert DB allowances to map for quick lookup
        const allowanceMap = {};
        if(data.allowances && data.allowances.length){
            data.allowances.forEach(a => {
                allowanceMap[a.name.toLowerCase()] = parseFloat(a.amount);
            });
        }

        // Always render all standard allowances with default 0 if not in DB
        allAllowanceOptions.forEach(opt => {
            createAllowanceRow(opt, allowanceMap[opt] || 0);
        });

        document.querySelector('input[name="otherallowancetitle"]').value = '';
        document.querySelector('input[name="otherallowanceamt"]').value = 0;

        // ===== Deductions =====
        const deductionContainer = document.getElementById('deductions-container');
        deductionContainer.innerHTML = '';

        const pfAmount = parseFloat(basicSalary.value) * 0.1;
        deductionContainer.innerHTML += `
            <div class="form-row deduction-row">
                <label>Provident Fund</label>
                <input type="number" name="pf" class="deductionamt" value="${pfAmount}" readonly>
            </div>
        `;

        const annualIncome = parseFloat(basicSalary.value) * 12;
        const taxAmount = calculateNepalTax(annualIncome);
        const taxPercent = (taxAmount / annualIncome * 100).toFixed(2);
        deductionContainer.innerHTML += `
            <div class="form-row deduction-row">
                <label>Tax</label>
                <input type="number" id="tax-amount" name="tax" class="deductionamt" value="${(taxAmount/12).toFixed(2)}" readonly>
                <input type="hidden" id="tax-percent" value="${taxPercent}">
            </div>
        `;

        const leave = data.deductions.find(d=>d.name.toLowerCase()==='leave') || {amount:0};
        deductionContainer.innerHTML += `
            <div class="form-row deduction-row">
                <label>Leave</label>
                <input type="number" name="deductionamt" class="deductionamt" value="${leave.amount}" >
            </div>
        `;

        calculateTotals();

    } catch(err){
        console.error(err);
        alert('Failed to fetch employee data');
    }
});

// ===== Helper for Allowance Rows =====
function createAllowanceRow(name='', amount=0){
    const row = document.createElement('div');
    row.classList.add('form-row','allowance-row');

    const label = document.createElement('label');
    label.textContent = name.replace('allowance',' ').replace('_',' ').replace(/\b\w/g, l => l.toUpperCase());

    const input = document.createElement('input');
    input.type = 'number';
    input.name = name;
    input.classList.add('allowanceamt');
    input.value = amount.toFixed(2);

    row.append(label, input);
    document.getElementById('allowances-container').appendChild(row);

    input.addEventListener('input', calculateTotals);
}

// =====================
// Totals & Net Salary
// =====================
function calculateTotals(){
  const basic = parseFloat(basicSalary.value) || 0;
  let totalA = 0;
  document.querySelectorAll('.allowanceamt').forEach(a=> totalA += parseFloat(a.value)||0);
  totalA += parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
  totalAllowance.value = totalA;

  const pfAmount = parseFloat(document.querySelector('input[name="pf"]').value) || 0;
  const taxInput = document.getElementById('tax-amount');
  let totalD = pfAmount + parseFloat(taxInput.value||0);

  document.querySelectorAll('.deduction-row input').forEach(d=>{
      if(d.name !== 'pf' && d.id !== 'tax-amount'){
          totalD += parseFloat(d.value)||0;
      }
  });
  totalD += parseFloat(document.querySelector('input[name="otherdeductionamt"]').value)||0;
  totalDeduction.value = totalD;

  netSalary.value = (basic + totalA - totalD).toFixed(2);
}

[basicSalary, document.querySelector('input[name="otherallowanceamt"]'), document.querySelector('input[name="otherdeductionamt"]')]
.forEach(el=>el.addEventListener('input', calculateTotals));
document.addEventListener('input', function(e){
    if(e.target.classList.contains('allowanceamt') || e.target.classList.contains('deductionamt')){
        calculateTotals();
    }
});

// =====================
// Create Payslip Button
// =====================
document.getElementById("create-payslip-btn").addEventListener("click", async function () {
    const empId = document.getElementById("employee_id").value;
    const deptId = document.getElementById("department").value;
    const year = document.getElementById("year").value;
    const month = document.getElementById("month").value;
    const basic = parseFloat(document.getElementById("basic-salary").value);
    const totalAllowanceVal = parseFloat(document.getElementById("total-allowance").value);
    const totalDeductionVal = parseFloat(document.getElementById("total-deduction").value);
    const netSalaryVal = parseFloat(document.getElementById("net-salary").value);
    const status = document.getElementById("status").value;

    if (!empId || !deptId || !basic || !month || !year) {
        alert("Missing required fields!");
        return;
    }

    // ===== Collect Allowances =====
    let allowances = {
        homeallowance: 0,
        healthallowance: 0,
        overtimeallowance: 0,
        festiveallowance: 0,
        other: 0,
        other_allowance_name: ''
    };

    document.querySelectorAll("#allowances-container .allowance-row").forEach(row => {
        const input = row.querySelector(".allowanceamt");
        const name = input.name.toLowerCase();
        const amount = parseFloat(input.value)||0;
        allowances[name] = amount;
    });

    const otherName = document.querySelector('input[name="otherallowancetitle"]').value;
    const otherAmt = parseFloat(document.querySelector('input[name="otherallowanceamt"]').value)||0;
    if(otherAmt){
        allowances.other = otherAmt;
        allowances.other_allowance_name = otherName || 'Other';
    }

    // ===== Deductions =====
    let deductions = [];
    const pfAmount = parseFloat(document.querySelector('input[name="pf"]').value)||0;
    const taxAmount = parseFloat(document.getElementById('tax-amount').value)||0;
    const taxPercent = parseFloat(document.getElementById('tax-percent').value)||0;

    deductions.push({ name: 'Provident Fund', amount: pfAmount, pf_percent: 10 });
    deductions.push({ name: 'Tax', amount: taxAmount, tax_percent: taxPercent });

    document.querySelectorAll("#deductions-container .deduction-row input").forEach(d=>{
        if(d.name !== 'pf' && d.id !== 'tax-amount'){
            deductions.push({
                name: d.previousElementSibling.textContent,
                amount: parseFloat(d.value)||0
            });
        }
    });

    const otherDedTitle = document.querySelector('input[name="otherdeductiontitle"]').value;
    const otherDedAmt = parseFloat(document.querySelector('input[name="otherdeductionamt"]').value) || 0;
    if(otherDedTitle && otherDedAmt){
        deductions.push({ name: 'Other', other_name: otherDedTitle, amount: otherDedAmt });
    }

    const payload = {
        emp_id: empId,
        dept_id: parseInt(deptId),
        year: parseInt(year),
        month,
        basic_salary: basic,
        total_allowance: totalAllowanceVal,
        total_deduction: totalDeductionVal,
        net_salary: netSalaryVal,
        status,
        allowances,
        deductions
    };

    try {
        const res = await fetch("../controller/create_payslip_backend.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if(result.success){
            alert("Payslip created successfully!");
            location.reload();
        } else {
            alert("Error: " + result.message);
        }
    } catch(err){
        console.error(err);
        alert("Fetch failed: see console");
    }
});
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
