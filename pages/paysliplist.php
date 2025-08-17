<?php
$page_title = "Payslip List";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/paysliplist.css"
];

ob_start();
?>

<div id="main-content">
    <h3>Payslip List</h3>
    <div class="year-month-container">
        <!-- Year section -->
        <div class="year-section">
            <label for="year-input">Year:</label>
            <input type="text" id="year-input">
        </div>

        <!-- Month section -->
        <div class="month-section">
            <label for="month-input">Month:</label>
            <div class="month-input-container">
                <input type="text" id="month-input" placeholder="Select a month" readonly>
                <div id="month-dropdown" class="month-dropdown" style="display: none;"></div>
            </div>
        </div>

        <!-- Find button -->
        <button id="find-btn">Find</button>
    </div>

    <div class="listhere">
        <div id="headerformanageemployee">
            <div class="entries">
                <label for="Show">Show</label>
                <input type="number" name="entries" id="entryCount" min="1" value="10">
                <label>entries</label>
            </div>
            <div class="searchbox">
                <label for="Search">Search:</label>
                <input type="text" name="Search" id="searchemployee" placeholder="Search employee">
            </div>
        </div>

        <!-- Employee details table -->
        <div class="table-responsive">
            <table id="empdetails" border="3" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Demo name</td>
                        <td>$1,500</td>
                        <td>2025-08-14</td>
                        <td>Month Name</td>
                        <td>
  <select class="status-dropdown">
    <option value="paid">Paid</option>
    <option value="pending" selected>Pending</option>
  </select>
</td>
                        <td class="action-buttons">
                            <button id="download-payslip" type="button">
  <i class="fa-solid fa-download"></i> Download Payslip
</button>
                   <button id="delete" type="submit"><i class="fa-solid fa-xmark"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Buttons -->
        <div class="buttonscontrol">
            <button type="submit" id="previouspage">Previous</button>
            <button type="submit" id="nextpage">Next</button>
        </div>
    </div>
</div>

    <script>
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonthIndex = currentDate.getMonth(); // 0-based index

        // Generate month names dynamically
        function getMonthNames(upToIndex) {
            const formatter = new Intl.DateTimeFormat('en', { month: 'long' });
            let months = [];
            for (let i = 0; i <= upToIndex; i++) {
                let tempDate = new Date(currentYear, i, 1);
                months.push(formatter.format(tempDate));
            }
            return months;
        }

        // Generate dropdown options
        function generateMonthOptions() {
            const monthDropdown = document.getElementById('month-dropdown');
            monthDropdown.innerHTML = '';

            const months = getMonthNames(currentMonthIndex);
            months.forEach(month => {
                const option = document.createElement('div');
                option.textContent = month;
                option.onclick = () => {
                    document.getElementById('month-input').value = month;
                    monthDropdown.style.display = 'none';
                };
                monthDropdown.appendChild(option);
            });
        }

        // Toggle dropdown
        function toggleMonthDropdown() {
            const dropdown = document.getElementById('month-dropdown');
            const input = document.getElementById('month-input');
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                generateMonthOptions();
                dropdown.style.top = `${input.offsetHeight + 5}px`;
                dropdown.style.left = '0';
            }
        }

        // Set default values
        function setDefaultValues() {
            document.getElementById('year-input').value = currentYear;

            const formatter = new Intl.DateTimeFormat('en', { month: 'long' });
            document.getElementById('month-input').value = formatter.format(currentDate);
        }

        // Close dropdown on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.month-input-container')) {
                document.getElementById('month-dropdown').style.display = 'none';
            }
        });

        // Init
        setDefaultValues();
        document.getElementById('month-input').addEventListener('click', toggleMonthDropdown);
    </script>
<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
