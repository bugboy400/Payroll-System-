<?php
$page_title = "Add Department";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/adddepartment.css"
];

ob_start();
?>

<div id="main-content">
  <h3>Add Department</h3>
  <div class="form-wrapper">
    <div id="addition">
      <section class="department">
        <label for="department">Department</label>
        <input type="text" placeholder="Department Name" id="department" name="department" />
      </section>

      <section class="designation">
        <label for="designation">Designation</label>
        <div class="input-group">
          <input type="text" placeholder="Designation" id="designation" name="designation" />
          <button type="button" id="addDesignationBtn">+</button>
        </div>
      </section>
    </div>

    <button type="submit" id="save" class="btn btn-primary d-block mx-auto mt-4">Save</button>
  </div>
</div>

 

<script>
  document.addEventListener('DOMContentLoaded', () => {
  const additionDiv = document.getElementById('addition');
  const addButton = document.querySelector('.designation button');

  addButton.addEventListener('click', () => {
    const container = document.createElement('div');
    container.className = 'designation-container';

    const newInput = document.createElement('input');
    newInput.type = 'text';
    newInput.placeholder = 'Designation';

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'delete-btn';
    deleteBtn.type = 'button';
    deleteBtn.textContent = '×';

    deleteBtn.addEventListener('click', () => {
      container.remove();
    });

    container.appendChild(newInput);
    container.appendChild(deleteBtn);

    additionDiv.appendChild(container);
  });
});

</script>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
