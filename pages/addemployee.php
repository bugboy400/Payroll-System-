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

$page_title = "Add Employee";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/addemployee.css"
];

include("../config/db.php");
ob_start();

// Fetch departments from DB
$departments = $conn->query("SELECT dept_id, department_name FROM departments ORDER BY department_name");
$deptList = [];
while($row = $departments->fetch_assoc()){
    $deptList[] = $row;
}
?>

<div id="main-content" class="container bg-light rounded p-4">
    <h3 class="mb-4">Add Employee</h3>
    <form action="../controller/add_employee.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">

            <!-- PERSONAL DETAILS -->
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 p-3">
                    <h4>Personal Details</h4>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fatherName" class="form-label">Father Name</label>
                        <input type="text" name="fatherName" id="fatherName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select" required>
                            <option value="" disabled selected hidden>Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" name="nationality" id="nationality" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="phno" class="form-label">Phone No. 1</label>
                        <input type="text" name="phno" id="phno" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="phno2" class="form-label">Phone No. 2</label>
                        <input type="text" name="phno2" id="phno2" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="localaddress" class="form-label">Local Address</label>
                        <input type="text" name="localaddress" id="localaddress" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="permanentaddress" class="form-label">Permanent Address</label>
                        <input type="text" name="permanentaddress" id="permanentaddress" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="maritalstatus" class="form-label">Marital Status</label>
                        <select name="maritalstatus" id="maritalstatus" class="form-select" required>
                            <option value="" disabled selected hidden>Select</option>
                            <option value="married">Married</option>
                            <option value="unmarried">Unmarried</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="imageSelect" class="form-label">Photo</label>
                        <input type="file" name="imageSelect" id="imageSelect" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- COMPANY + FINANCIAL DETAILS -->
            <div class="col-lg-6 col-md-12">
                <!-- COMPANY DETAILS -->
                <div class="card h-50 p-3 mb-4">
                    <h4>Company Details</h4>
                    <div class="mb-3">
                        <label for="empid" class="form-label">Employee Id</label>
                        <input type="text" name="empid" id="empid" class="form-control" placeholder="Auto Generated" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="dept" class="form-label">Department</label>
                        <select name="dept" id="dept" class="form-select" required>
                            <option value="">Select Department</option>
                            <?php foreach($deptList as $d): ?>
                                <option value="<?= htmlspecialchars($d['department_name']) ?>" data-id="<?= $d['dept_id'] ?>">
                                    <?= htmlspecialchars($d['department_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation</label>
                        <select name="designation" id="designation" class="form-select" required>
                            <option value="">Select Designation</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dateofjoin" class="form-label">Date of Joining</label>
                        <input type="date" name="dateofjoin" id="dateofjoin" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="dateofleave" class="form-label">Date of Leaving</label>
                        <input type="date" name="dateofleave" id="dateofleave" class="form-control">
                    </div>
                </div>

                <!-- FINANCIAL DETAILS -->
                <div class="card h-50 p-3">
                    <h4>Financial Details</h4>
                    <div class="mb-3">
                        <label for="basicsal" class="form-label">Basic Salary</label>
                        <input type="number" name="basicsal" id="basicsal" class="form-control">
                    </div>

                    <!-- Allowances -->
                    <div class="mb-3">
                        <label class="form-label">Allowances</label>
                        <div id="allowances-container" class="d-flex flex-column gap-2">
                            <div class="d-flex gap-2 allowance-row">
                                <select name="allowance[]" class="form-select allowancename">
                                    <option value="homeallowance">Home Allowance</option>
                                    <option value="healthallowance">Health Allowance</option>
                                    <option value="overtimeallowance">OT Allowance</option>
                                    <option value="festiveallowance">Festive Allowance</option>
                                </select>
                                <input type="number" name="allowanceamt[]" class="form-control allowanceamt" placeholder="Amount">
                                <button type="button" class="btn btn-success add-allowance-btn">+</button>
                                <button type="button" class="btn btn-danger remove-allowance-btn">−</button>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div class="mb-3">
                        <label class="form-label">Deductions</label>
                        <div id="deductions-container" class="d-flex flex-column gap-2">
                            <div class="d-flex gap-2 deduction-row">
                                <select name="deduction[]" class="form-select deductionname">
                                    <option value="providentfund">Provident Fund</option>
                                    <option value="leave">Leave</option>
                                </select>
                                <input type="number" name="deductionamt[]" class="form-control deductionamt" placeholder="Amount">
                                <button type="button" class="btn btn-success add-deduction-btn">+</button>
                                <button type="button" class="btn btn-danger remove-deduction-btn">−</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="totalsal" class="form-label">TOTAL Salary</label>
                        <input type="number" name="totalsal" id="totalsal" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- SAVE BUTTON -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-5">Save Employee</button>
        </div>
    </form>
</div>

<script>
// Fetch designations dynamically
const deptSelect = document.getElementById('dept');
const desSelect = document.getElementById('designation');
const nameInput = document.getElementById('name');
const empIdInput = document.getElementById('empid');
const basicSalInput = document.getElementById('basicsal');
const totalSalInput = document.getElementById('totalsal');

deptSelect.addEventListener('change', () => {
    const deptId = deptSelect.selectedOptions[0].dataset.id;
    desSelect.innerHTML = '<option value="">Select Designation</option>';
    if(deptId){
        fetch(`../controller/get_designations.php?dept_id=${deptId}`)
        .then(res=>res.json())
        .then(data=>{
            data.forEach(d=>{
                const opt = document.createElement('option');
                opt.value = d.designation_name;
                opt.textContent = d.designation_name;
                desSelect.appendChild(opt);
            });
        });
    }
    generateEmpId();
});

// Employee ID generator
function generateEmpId(){
    const name = nameInput.value.trim();
    const dept = deptSelect.value;
    const des = desSelect.value;

    if(name && dept && des){
        const nameParts = name.split(' ');
        let initials = nameParts.length>=2 ? nameParts[0][0]+nameParts[nameParts.length-1][0] : name.substring(0,2);
        initials = initials.toUpperCase();
        const deptInit = dept.substring(0,2).toUpperCase();
        const desInit = des.split(' ').map(w=>w[0].toUpperCase()).join('');
        const randomNum = Math.floor(Math.random()*100+1).toString().padStart(3,'0');
        empIdInput.value = `${initials}${randomNum}${deptInit}${desInit}`;
    } else {
        empIdInput.value = '';
    }
}
nameInput.addEventListener('input', generateEmpId);
desSelect.addEventListener('change', generateEmpId);

// Allowances & deductions dynamic add/remove
document.addEventListener('click', e=>{
    if(e.target.classList.contains('add-allowance-btn')){
        const container = e.target.closest('#allowances-container');
        const row = e.target.closest('.allowance-row');
        const clone = row.cloneNode(true);
        clone.querySelector('input').value = '';
        container.appendChild(clone);
    }
    if(e.target.classList.contains('remove-allowance-btn')){
        const row = e.target.closest('.allowance-row');
        if(row.parentNode.children.length>1) row.remove();
    }
    if(e.target.classList.contains('add-deduction-btn')){
        const container = e.target.closest('#deductions-container');
        const row = e.target.closest('.deduction-row');
        const clone = row.cloneNode(true);
        clone.querySelector('input').value = '';
        container.appendChild(clone);
    }
    if(e.target.classList.contains('remove-deduction-btn')){
        const row = e.target.closest('.deduction-row');
        if(row.parentNode.children.length>1) row.remove();
    }

    // recalc total salary whenever add/remove allowance/deduction
    calculateTotalSalary();
});

// Calculate total salary live
function calculateTotalSalary(){
    let basic = parseFloat(basicSalInput.value) || 0;

    let allowanceInputs = document.querySelectorAll('.allowanceamt');
    let totalAllowance = 0;
    allowanceInputs.forEach(a=>{
        totalAllowance += parseFloat(a.value) || 0;
    });

    let deductionInputs = document.querySelectorAll('.deductionamt');
    let totalDeduction = 0;
    deductionInputs.forEach(d=>{
        totalDeduction += parseFloat(d.value) || 0;
    });

    totalSalInput.value = basic + totalAllowance - totalDeduction;
}

// Recalculate when basic salary, allowance, or deduction changes
basicSalInput.addEventListener('input', calculateTotalSalary);
document.addEventListener('input', e=>{
    if(e.target.classList.contains('allowanceamt') || e.target.classList.contains('deductionamt')){
        calculateTotalSalary();
    }
});
</script>


<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
