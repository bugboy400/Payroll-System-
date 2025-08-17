
const designations = {
  account: ['Accountant', 'Account Manager', 'Auditor'],
  hr: ['HR Executive', 'Recruiter', 'HR Manager'],
  it: ['Developer', 'System Admin', 'IT Support']
};

function updateDesignations() {
  const deptSelect = document.getElementById('dept');
  const designationSelect = document.getElementById('designation');
  const selectedDept = deptSelect.value;

  designationSelect.innerHTML = '<option value="">Select Designation</option>';

  if (selectedDept && designations[selectedDept]) {
    designations[selectedDept].forEach(function (desig) {
      const option = document.createElement('option');
      option.value = desig.toLowerCase().replace(/\s+/g, '-');
      option.textContent = desig;
      designationSelect.appendChild(option);
    });
  }
}

document.addEventListener('DOMContentLoaded', function () {
  // Allowances
  const allowancesContainer = document.getElementById('allowances-container');
  allowancesContainer.addEventListener('click', function (e) {
    const target = e.target;

    if (target.classList.contains('add-allowance-btn')) {
      e.preventDefault();
      const currentRow = target.closest('.allowance-row');
      const newRow = currentRow.cloneNode(true);

      newRow.querySelector('select.allowancename').selectedIndex = 0;
      newRow.querySelector('input.allowanceamt').value = '';
      newRow.classList.add('mt-2');

      allowancesContainer.appendChild(newRow);
    }

    if (target.classList.contains('remove-allowance-btn')) {
      e.preventDefault();
      const rows = allowancesContainer.querySelectorAll('.allowance-row');
      if (rows.length > 1) {
        target.closest('.allowance-row').remove();
      } else {
        alert('At least one allowance row is required.');
      }
    }
  });

  // Deductions
  const deductionsContainer = document.getElementById('deductions-container');
  deductionsContainer.addEventListener('click', function (e) {
    const target = e.target;

    if (target.classList.contains('add-deduction-btn')) {
      e.preventDefault();
      const currentRow = target.closest('.deduction-row');
      const newRow = currentRow.cloneNode(true);

      newRow.querySelector('select.deductionname').selectedIndex = 0;
      newRow.querySelector('input.deductionamt').value = '';
      newRow.classList.add('mt-2');

      deductionsContainer.appendChild(newRow);
    }

    if (target.classList.contains('remove-deduction-btn')) {
      e.preventDefault();
      const rows = deductionsContainer.querySelectorAll('.deduction-row');
      if (rows.length > 1) {
        target.closest('.deduction-row').remove();
      } else {
        alert('At least one deduction row is required.');
      }
    }
  });

  // Calculate total salary
  document.getElementById('calculate-btn').addEventListener('click', function (e) {
    e.preventDefault();

    let basicSalary = parseFloat(document.getElementById('basicsal').value) || 0;

    // Sum allowances
    let totalAllowances = 0;
    document.querySelectorAll('#allowances-container .allowanceamt').forEach(function (input) {
      totalAllowances += parseFloat(input.value) || 0;
    });

    // Sum deductions
    let totalDeductions = 0;
    document.querySelectorAll('#deductions-container .deductionamt').forEach(function (input) {
      totalDeductions += parseFloat(input.value) || 0;
    });

    let totalSalary = basicSalary + totalAllowances - totalDeductions;

    document.getElementById('totalsal').value = totalSalary.toFixed(2);
  });
});
