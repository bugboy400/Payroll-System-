<?php
$page_title = "Add Department";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/adddepartment.css"
];

include("../config/db.php");
ob_start();

// Fetch all departments
$departments = [];
$result = $conn->query("SELECT dept_id, department_name FROM departments ORDER BY department_name");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<div id="main-content">
  <h3>Add Department</h3>
  <div class="form-wrapper">
    <form action="../controller/add_department.php" method="POST">
      <div id="addition">

        <!-- Department Select -->
        <section class="department">
          <label for="department">Department</label>
          <select id="department" name="department" class="form-select" required>
            <option value="" disabled selected>Select Department</option>
            <option value="new">+ Add New Department</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept['department_name']) ?>" data-id="<?= $dept['dept_id'] ?>">
                    <?= htmlspecialchars($dept['department_name']) ?>
                </option>
            <?php endforeach; ?>
          </select>

          <!-- New Department Input -->
          <input type="text" id="newDepartment" name="newDepartment" placeholder="New Department Name" class="form-input" style="display:none; margin-top:8px;" />
        </section>

        <!-- Designations -->
        <section class="designation">
          <label for="designation">Designation(s)</label>
          <div id="designation-container" class="designation-list">
            <div class="designation-item d-flex gap-2">
              <input type="text" placeholder="Designation" name="designations[]" required class="form-input" />
              <button type="button" class="btn btn-danger btn-sm remove-btn">×</button>
            </div>
          </div>
          <button type="button" id="addDesignationBtn" class="btn btn-primary mt-2">+ Add Designation</button>
        </section>

      </div>

      <button type="submit" id="save" class="btn btn-primary d-block mx-auto mt-4">Save</button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const departmentSelect = document.getElementById('department');
    const newDeptInput = document.getElementById('newDepartment');
    const addBtn = document.getElementById('addDesignationBtn');
    const designationContainer = document.getElementById('designation-container');

    // Toggle new department input
    departmentSelect.addEventListener('change', () => {
        const deptName = departmentSelect.value;
        if (deptName === 'new') {
            newDeptInput.style.display = 'block';
            newDeptInput.focus();
            loadDesignations(0); // clear previous designations
        } else {
            newDeptInput.style.display = 'none';
            newDeptInput.value = '';
            const deptId = departmentSelect.selectedOptions[0].dataset.id;
            loadDesignations(deptId);
        }
    });

    // Add new designation input
    addBtn.addEventListener('click', () => {
        const container = document.createElement('div');
        container.className = 'designation-item d-flex gap-2 mt-2';

        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'designations[]';
        newInput.placeholder = 'Designation';
        newInput.required = true;
        newInput.className = 'form-input';

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.textContent = '×';
        deleteBtn.className = 'btn btn-danger btn-sm remove-btn';
        deleteBtn.addEventListener('click', () => container.remove());

        container.appendChild(newInput);
        container.appendChild(deleteBtn);
        designationContainer.appendChild(container);
    });

    // Load existing designations via AJAX
    function loadDesignations(deptId) {
        designationContainer.innerHTML = ''; // clear container

        if (deptId > 0) {
            fetch('../controller/get_designations.php?dept_id=' + deptId)
            .then(res => res.json())
            .then(data => {
                if (data.length) {
                    data.forEach(des => {
                        const container = document.createElement('div');
                        container.className = 'designation-item d-flex gap-2 mt-2';

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.name = 'designations[]';
                        input.value = des.designation_name;
                        input.required = true;
                        input.className = 'form-input';

                        const deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.textContent = '×';
                        deleteBtn.className = 'btn btn-danger btn-sm remove-btn';
                        deleteBtn.addEventListener('click', () => container.remove());

                        container.appendChild(input);
                        container.appendChild(deleteBtn);
                        designationContainer.appendChild(container);
                    });
                } else {
                    // If no designation exists, show one empty input
                    const emptyContainer = document.createElement('div');
                    emptyContainer.className = 'designation-item d-flex gap-2 mt-2';
                    const emptyInput = document.createElement('input');
                    emptyInput.type = 'text';
                    emptyInput.name = 'designations[]';
                    emptyInput.placeholder = 'Designation';
                    emptyInput.required = true;
                    emptyInput.className = 'form-input';
                    emptyContainer.appendChild(emptyInput);
                    designationContainer.appendChild(emptyContainer);
                }
            });
        } else {
            // New department - show one empty input
            const emptyContainer = document.createElement('div');
            emptyContainer.className = 'designation-item d-flex gap-2 mt-2';
            const emptyInput = document.createElement('input');
            emptyInput.type = 'text';
            emptyInput.name = 'designations[]';
            emptyInput.placeholder = 'Designation';
            emptyInput.required = true;
            emptyInput.className = 'form-input';
            emptyContainer.appendChild(emptyInput);
            designationContainer.appendChild(emptyContainer);
        }
    }

    // Initial remove button functionality
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.target.closest('.designation-item').remove();
        });
    });
});
</script>


<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
