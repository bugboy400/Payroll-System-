<?php
session_start();
if(!isset($_SESSION['email'])){
    header("Location: ../layouts/login.php");
    exit();
}

$page_title = "Employee";
$page_css = [
    "/payrollself/includes/employeedetails.css",
];

include("../config/db.php");
ob_start();

// Get employee ID
$emp_id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
if(empty($emp_id)){
    die("Invalid employee ID");
}

// Fetch employee personal, company, financial details
$stmt = $conn->prepare("
SELECT ep.*, 
       ec.dateofjoin, ec.dateofleave, ec.dept_id, ec.designation_id,
       d.department_name, des.designation_name,
       ef.basicsal, ef.total_sal
FROM employees_personal ep
LEFT JOIN employees_company ec ON ep.emp_id = ec.emp_id
LEFT JOIN departments d ON ec.dept_id = d.dept_id
LEFT JOIN designations des ON ec.designation_id = des.designation_id
LEFT JOIN employees_financial ef ON ep.emp_id = ef.emp_id
WHERE ep.emp_id = ?
");
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
if(!$employee){
    die("Employee not found");
}

// Fetch allowances & deductions
$allowances = [];
$allow_res = $conn->query("SELECT id, allowance_name, allowance_amt FROM employees_allowances WHERE emp_id='$emp_id'");
while($row = $allow_res->fetch_assoc()){
    $allowances[] = $row;
}

$deductions = [];
$ded_res = $conn->query("SELECT id, deduction_name, deduction_amt FROM employees_deductions WHERE emp_id='$emp_id'");
while($row = $ded_res->fetch_assoc()){
    $deductions[] = $row;
}

// Fetch departments & designations
$departments = $conn->query("SELECT * FROM departments");
$designations_res = $conn->query("SELECT * FROM designations");

// Fetch distinct allowance and deduction types
$allowance_types_res = $conn->query("SELECT DISTINCT allowance_name FROM employees_allowances");
$deduction_types_res = $conn->query("SELECT DISTINCT deduction_name FROM employees_deductions");
?>

<div id="main-content">
    <h3 class="page-heading">Edit Employee Details</h3>
    <form action="../controller/update_employee.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="emp_id" value="<?php echo $employee['emp_id']; ?>">

        <div class="container">

            <!-- Left Box -->
            <div class="left-box">
                <div class="photo-container">
    <img src="../<?php echo htmlspecialchars($employee['photo']); ?>" alt="Employee Photo" class="employee-photo">
    <div class="overlay">
        <label for="photo" class="change-photo-label">
            <i class="fa fa-camera"></i> Change Image
        </label>
    </div>
    <input type="file" name="photo" id="photo" accept="image/*" style="display:none;">
</div>

                <h2>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
                </h2>

                <div class="sub-tabs">
                    <button type="button" class="tab-btn active" data-tab="personal">Personal Details</button>
                    <button type="button" class="tab-btn" data-tab="company">Company Details</button>
                    <button type="button" class="tab-btn" data-tab="financial">Financial Details</button>
                    <button type="button" class="tab-btn" data-tab="allowances">Allowances</button>
                    <button type="button" class="tab-btn" data-tab="deductions">Deductions</button>
                </div>
            </div>

            <!-- Right Box -->
            <div class="right-box">

                <!-- Personal Details -->
                <div class="tab-content active" id="personal">
                    <h4>Personal Details</h4>
                    <div class="detail-grid">
                        <p><strong>Father Name:</strong> <input type="text" name="fatherName" value="<?php echo $employee['fatherName']; ?>"></p>
                        <p><strong>Date of Birth:</strong> <input type="date" name="dob" value="<?php echo $employee['dob']; ?>"></p>
                        <p><strong>Gender:</strong> 
                            <select name="gender">
                                <option value="male" <?php if($employee['gender']=='male') echo 'selected';?>>Male</option>
                                <option value="female" <?php if($employee['gender']=='female') echo 'selected';?>>Female</option>
                                <option value="other" <?php if($employee['gender']=='other') echo 'selected';?>>Other</option>
                            </select>
                        </p>
                        <p><strong>Nationality:</strong> <input type="text" name="nationality" value="<?php echo $employee['nationality']; ?>"></p>
                        <p><strong>Marital Status:</strong>
                            <select name="maritalstatus">
                                <option value="married" <?php if($employee['maritalstatus']=='married') echo 'selected';?>>Married</option>
                                <option value="unmarried" <?php if($employee['maritalstatus']=='unmarried') echo 'selected';?>>Unmarried</option>
                                <option value="other" <?php if($employee['maritalstatus']=='other') echo 'selected';?>>Other</option>
                            </select>
                        </p>
                        <p><strong>Phone 1:</strong> <input type="text" name="phone1" value="<?php echo $employee['phone1']; ?>"></p>
                        <p><strong>Phone 2:</strong> <input type="text" name="phone2" value="<?php echo $employee['phone2']; ?>"></p>
                        <p><strong>Local Address:</strong> <textarea name="localaddress"><?php echo $employee['localaddress']; ?></textarea></p>
                        <p><strong>Permanent Address:</strong> <textarea name="permanentaddress"><?php echo $employee['permanentaddress']; ?></textarea></p>
                    </div>
                </div>

                <!-- Company Details -->
                <div class="tab-content" id="company">
                    <h4>Company Details</h4>
                    <div class="detail-grid">
                        <p><strong>Department:</strong> 
                            <select name="dept_id" id="dept-select">
                                <?php while($row=$departments->fetch_assoc()): ?>
                                    <option value="<?php echo $row['dept_id']; ?>" <?php if($employee['dept_id']==$row['dept_id']) echo 'selected'; ?>>
                                        <?php echo $row['department_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </p>
                        <p><strong>Designation:</strong> 
                            <select name="designation_id" id="designation-select">
                                <?php 
                                $designations_res->data_seek(0);
                                while($row=$designations_res->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $row['designation_id']; ?>" 
                                            data-dept="<?php echo $row['dept_id']; ?>"
                                            <?php if($employee['designation_id']==$row['designation_id']) echo 'selected'; ?>>
                                        <?php echo $row['designation_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </p>
                        <p><strong>Date of Joining:</strong> <input type="date" name="dateofjoin" value="<?php echo $employee['dateofjoin']; ?>"></p>
                        <p><strong>Date of Leaving:</strong> <input type="date" name="dateofleave" value="<?php echo $employee['dateofleave']; ?>"></p>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="tab-content" id="financial">
                    <h4>Financial Details</h4>
                    <div class="detail-grid">
                        <p><strong>Basic Salary:</strong> <input type="number" step="0.01" name="basicsal" value="<?php echo $employee['basicsal']; ?>"></p>
                        <p><strong>Total Salary:</strong> <input type="number" step="0.01" name="total_sal" value="<?php echo $employee['total_sal']; ?>"></p>
                    </div>
                </div>

                <!-- Allowances -->
                <div class="tab-content" id="allowances">
                    <h4>Allowances</h4>
                    <div id="allowances-wrapper">
                        <?php foreach($allowances as $i=>$a): ?>
                        <div class="allowance-item">
                            <select name="allowance_name[]">
                                <?php $allowance_types_res->data_seek(0); while($type=$allowance_types_res->fetch_assoc()): ?>
                                    <option value="<?php echo $type['allowance_name']; ?>" <?php if($type['allowance_name']==$a['allowance_name']) echo 'selected';?>><?php echo $type['allowance_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <input type="number" step="0.01" name="allowance_amt[]" value="<?php echo $a['allowance_amt']; ?>">
                            <input type="hidden" name="allowance_id[]" value="<?php echo $a['id']; ?>">
                            <button type="button" class="remove-item">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-allowance">Add Allowance</button>
                </div>

                <!-- Deductions -->
                <div class="tab-content" id="deductions">
                    <h4>Deductions</h4>
                    <div id="deductions-wrapper">
                        <?php foreach($deductions as $i=>$d): ?>
                        <div class="deduction-item">
                            <select name="deduction_name[]">
                                <?php $deduction_types_res->data_seek(0); while($type=$deduction_types_res->fetch_assoc()): ?>
                                    <option value="<?php echo $type['deduction_name']; ?>" <?php if($type['deduction_name']==$d['deduction_name']) echo 'selected';?>><?php echo $type['deduction_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <input type="number" step="0.01" name="deduction_amt[]" value="<?php echo $d['deduction_amt']; ?>">
                            <input type="hidden" name="deduction_id[]" value="<?php echo $d['id']; ?>">
                            <button type="button" class="remove-item">Remove</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-deduction">Add Deduction</button>
                </div>

                <div style="text-align:center;margin-top:20px;">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>

    const photoInput = document.getElementById('photo');
const employeePhoto = document.querySelector('.employee-photo');

photoInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            employeePhoto.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Tab switching
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');
tabButtons.forEach(btn=>{
    btn.addEventListener('click',()=>{
        const target = btn.dataset.tab;
        tabButtons.forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        tabContents.forEach(tc=>tc.classList.remove('active'));
        document.getElementById(target).classList.add('active');
    });
});

// Filter Designations based on Department
const deptSelect = document.getElementById('dept-select');
const designationSelect = document.getElementById('designation-select');

function filterDesignations() {
    const selectedDept = deptSelect.value;
    const options = designationSelect.querySelectorAll('option');
    options.forEach(opt => {
        if(opt.dataset.dept === selectedDept){
                        opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });

    // Automatically select the first visible option
    const firstVisible = Array.from(options).find(o => o.style.display === 'block');
    if(firstVisible){
        designationSelect.value = firstVisible.value;
    }
}

// Initial filtering on page load
filterDesignations();

// Filter whenever department changes
deptSelect.addEventListener('change', filterDesignations);

// Allowances
document.getElementById('add-allowance').addEventListener('click',()=>{
    const wrapper = document.getElementById('allowances-wrapper');
    const newItem = document.createElement('div');
    newItem.classList.add('allowance-item');
    let options = `<?php $allowance_types_res->data_seek(0); while($type=$allowance_types_res->fetch_assoc()): ?>
                    <option value="<?php echo $type['allowance_name'];?>"><?php echo $type['allowance_name'];?></option>
                <?php endwhile; ?>`;
    newItem.innerHTML = `<select name="allowance_name[]">${options}</select>
                         <input type="number" step="0.01" name="allowance_amt[]" value="">
                         <input type="hidden" name="allowance_id[]" value="">
                         <button type="button" class="remove-item">Remove</button>`;
    wrapper.appendChild(newItem);
});

// Deductions
document.getElementById('add-deduction').addEventListener('click',()=>{
    const wrapper = document.getElementById('deductions-wrapper');
    const newItem = document.createElement('div');
    newItem.classList.add('deduction-item');
    let options = `<?php $deduction_types_res->data_seek(0); while($type=$deduction_types_res->fetch_assoc()): ?>
                    <option value="<?php echo $type['deduction_name'];?>"><?php echo $type['deduction_name'];?></option>
                <?php endwhile; ?>`;
    newItem.innerHTML = `<select name="deduction_name[]">${options}</select>
                         <input type="number" step="0.01" name="deduction_amt[]" value="">
                         <input type="hidden" name="deduction_id[]" value="">
                         <button type="button" class="remove-item">Remove</button>`;
    wrapper.appendChild(newItem);
});

// Remove item
document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-item')){
        e.target.parentElement.remove();
    }
});
</script>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>

