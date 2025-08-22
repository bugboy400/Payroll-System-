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
          <div class="col-6"><input type="number" name="otherallowanceamt" class="form-control other-allowance" placeholder="Amount"></div>
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
          <div class="col-6"><input type="number" name="otherdeductionamt" class="form-control other-deduction" placeholder="Amount"></div>
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
    <div class="form-rowsum"><label>Total Allowance</label><input type="number" id="total-allowance"></div>
    <div class="form-rowsum"><label>Total Deduction</label><input type="number" id="total-deduction"></div>
    <div class="form-rowsum"><label>Net Salary</label><input type="number" id="net-salary"></div>
    <div class="form-rowsum"><label>Status</label>
      <select id="status"><option value="Paid">Paid</option><option value="Unpaid" selected>Unpaid</option></select>
    </div>
    <button type="button" id="create-payslip-btn">Create Payslip</button>
  </div>
</div>

<script>
// =====================
// Year & Month init
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
// Auto calc functions
// =====================
function calculateTotals() {
  let allow = 0, deduct = 0;

  document.querySelectorAll('.allowanceamt').forEach(inp=>{
    allow += parseFloat(inp.value)||0;
  });
  allow += parseFloat(document.querySelector('.other-allowance')?.value||0);

  document.querySelectorAll('.deductionamt').forEach(inp=>{
    deduct += parseFloat(inp.value)||0;
  });
  deduct += parseFloat(document.querySelector('.other-deduction')?.value||0);

  document.getElementById('total-allowance').value = allow;
  document.getElementById('total-deduction').value = deduct;

  const basic = parseFloat(document.getElementById('basic-salary').value)||0;
  document.getElementById('net-salary').value = (basic + allow - deduct).toFixed(2);
}

// Attach listeners (delegated)
document.addEventListener('input', function(e){
  if(e.target.classList.contains('allowanceamt') ||
     e.target.classList.contains('deductionamt') ||
     e.target.classList.contains('other-allowance') ||
     e.target.classList.contains('other-deduction') ||
     e.target.id === 'basic-salary'){
    calculateTotals();
  }
});

// =====================
// (rest of your code ... load depts, employees, next button, backend call ... unchanged)
// =====================
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
