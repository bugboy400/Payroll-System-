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
    <form action="../controller/add_employee.php" method="POST" enctype="multipart/form-data" novalidate>
        <div class="row g-4">

            <!-- PERSONAL DETAILS -->
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 p-3">
                    <h4>Personal Details</h4>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback">Only letters and spaces allowed.</div>
                    </div>
                    <div class="mb-3">
                        <label for="fatherName" class="form-label">Father Name</label>
                        <input type="text" name="fatherName" id="fatherName" class="form-control" required>
                        <div class="invalid-feedback">Only letters and spaces allowed.</div>
                    </div>
                    <div class="mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control" required>
                        <div class="invalid-feedback">DOB cannot be in the future.</div>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select" required>
                            <option value="" disabled selected hidden>Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="invalid-feedback">Please select a gender.</div>
                    </div>
                    <div class="mb-3">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" name="nationality" id="nationality" class="form-control">
                        <div class="invalid-feedback">Only letters and spaces allowed.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phno" class="form-label">Phone No. 1</label>
                        <input type="text" name="phno" id="phno" class="form-control">
                        <div class="invalid-feedback">Invalid Nepali number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phno2" class="form-label">Phone No. 2</label>
                        <input type="text" name="phno2" id="phno2" class="form-control">
                        <div class="invalid-feedback">Invalid Nepali number or same as Phone 1.</div>
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
                        <div class="invalid-feedback">Please select marital status.</div>
                    </div>
                    <div class="mb-3">
                        <label for="imageSelect" class="form-label">Photo</label>
                        <input type="file" name="imageSelect" id="imageSelect" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- COMPANY + FINANCIAL DETAILS -->
            <div class="col-lg-6 col-md-12">
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
                        <div class="invalid-feedback">Please select a department.</div>
                    </div>
                    <div class="mb-3">
                        <label for="designation" class="form-label">Designation</label>
                        <select name="designation" id="designation" class="form-select" required>
                            <option value="">Select Designation</option>
                        </select>
                        <div class="invalid-feedback">Please select a designation.</div>
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

                <div class="card h-50 p-3">
                    <h4>Financial Details</h4>
                    <div class="mb-3">
                        <label for="basicsal" class="form-label">Basic Salary</label>
                        <input type="number" name="basicsal" id="basicsal" class="form-control">
                        <div class="invalid-feedback">Basic salary must be non-negative.</div>
                    </div>

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
                                <div class="invalid-feedback">Amount must be non-negative.</div>
                                <button type="button" class="btn btn-success add-allowance-btn">+</button>
                                <button type="button" class="btn btn-danger remove-allowance-btn">−</button>
                            </div>
                        </div>
                    </div>

                   <div class="mb-3">
    <label class="form-label">Deductions</label>
    <div id="deductions-container" class="d-flex flex-column gap-2">
        <div class="d-flex gap-2 deduction-row align-items-center">
            <input type="text" class="form-control" value="Provident Fund" readonly style="flex: 1; pointer-events: none; background-color: #e9ecef;">
            <input type="number" name="deductionamt" id="providentfund" class="form-control" placeholder="Amount" readonly style="flex: 1;">
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

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-5">Save Employee</button>
        </div>
    </form>
</div>

<style>
input.is-valid, select.is-valid {
    border-color: #28a745 !important;
}
input.is-invalid, select.is-invalid {
    border-color: #dc3545 !important;
}
.invalid-feedback {
    display: none;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    color: #dc3545;
}
input.is-invalid + .invalid-feedback,
select.is-invalid + .invalid-feedback {
    display: block;
}
</style>

<script>
// --- Elements ---
const deptSelect = document.getElementById('dept');
const desSelect = document.getElementById('designation');
const nameInput = document.getElementById('name');
const fatherInput = document.getElementById('fatherName');
const nationalityInput = document.getElementById('nationality');
const phone1Input = document.getElementById('phno');
const phone2Input = document.getElementById('phno2');
const empIdInput = document.getElementById('empid');
const basicSalInput = document.getElementById('basicsal');
const totalSalInput = document.getElementById('totalsal');
const form = document.querySelector('form');

const nameRegex = /^[a-zA-Z\s]+$/;
const phoneRegex = /^(98|97|96|94)\d{8}$/;

// --- Validation ---
function validateField(input, regex=null, required=true){
    const val = input.value.trim();
    const invalidFeedback = input.nextElementSibling;

    if(!val && required){
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        if(invalidFeedback) invalidFeedback.textContent = "This field is required.";
        return false;
    }

    if(val && regex && !regex.test(val)){
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        return false;
    }

    input.classList.remove('is-invalid');
    if(val) input.classList.add('is-valid');
    return true;
}

function validateAmount(input){
    const val = parseFloat(input.value);
    if(isNaN(val) || val < 0){
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        return false;
    } else {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
        return true;
    }
}

// --- Event Listeners ---
[nameInput, fatherInput, nationalityInput].forEach(input=>{
    input.addEventListener('input', ()=> validateField(input, nameRegex));
});
[phone1Input, phone2Input].forEach(input=>{
    input.addEventListener('input', ()=> validateField(input, phoneRegex));
});
basicSalInput.addEventListener('input', ()=>{
    validateAmount(basicSalInput);
    calculateTotalSalary();
});
document.addEventListener('input', e=>{
    if(e.target.classList.contains('allowanceamt') || e.target.classList.contains('deductionamt')){
        validateAmount(e.target);
        calculateTotalSalary();
    }
});

// --- Department / Designation ---
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
desSelect.addEventListener('change', generateEmpId);
nameInput.addEventListener('input', generateEmpId);

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

// --- Allowances & Deductions Add/Remove ---
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
});

// --- Total Salary ---
function calculateTotalSalary(){
    let basic = parseFloat(basicSalInput.value) || 0;

    // Auto calculate Provident Fund as 10% of Basic Salary
    const pfInput = document.querySelector('.deductionamt');
    pfInput.value = (basic * 0.1).toFixed(2);

    // Sum allowances
    let totalAllowance = 0;
    document.querySelectorAll('.allowanceamt').forEach(a => totalAllowance += parseFloat(a.value)||0);

    // Provident Fund is the only deduction
    let totalDeduction = parseFloat(pfInput.value) || 0;

    totalSalInput.value = basic + totalAllowance - totalDeduction;
}


// --- Form submit ---
form.addEventListener('submit', e=>{
    let valid = true;
    valid &= validateField(nameInput, nameRegex);
    valid &= validateField(fatherInput, nameRegex);
    if(nationalityInput.value) valid &= validateField(nationalityInput, nameRegex);
    valid &= validateField(phone1Input, phoneRegex);
    if(phone2Input.value) valid &= validateField(phone2Input, phoneRegex);
    valid &= validateAmount(basicSalInput);
    document.querySelectorAll('.allowanceamt').forEach(a=>{ if(!validateAmount(a)) valid=false; });
    document.querySelectorAll('.deductionamt').forEach(d=>{ if(!validateAmount(d)) valid=false; });
    if(!valid){
        e.preventDefault();
        alert("Please fix validation errors before submitting.");
    }
});
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
