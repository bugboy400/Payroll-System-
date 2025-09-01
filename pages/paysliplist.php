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

$page_title = "Payslip List";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/paysliplist.css"
];

ob_start();
?>
<style>
.btn-download, .btn-delete {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    margin: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: 0.2s ease-in-out;
}
.btn-download {
    background: #28a745;
    color: #fff;
}
.btn-download:hover { background: #218838; transform: scale(1.05); }
.btn-delete {
    background: #dc3545;
    color: #fff;
}
.btn-delete:hover { background: #c82333; transform: scale(1.05); }

.message {
    margin: 10px 0;
    padding: 10px;
    border-radius: 6px;
    display: none;
}
.message.success { background: #d4edda; color: #155724; }
.message.error { background: #f8d7da; color: #721c24; }
</style>

<div id="main-content">
    <h3>Payslip List</h3>

    <!-- Alert Message -->
    <div id="alertMessage" class="message"></div>

    <div class="year-month-container">
        <div class="year-section">
            <label for="year-input">Year:</label>
            <input type="text" id="year-input" readonly style="width:80px;">
        </div>

        <div class="month-section">
            <label for="month-input">Month:</label>
            <div class="month-input-container" style="width:150px; position:relative;">
                <input type="text" id="month-input" placeholder="Select a month" readonly>
                <div id="month-dropdown" class="month-dropdown" 
                     style="display:none; position:absolute; background:#fff; border:1px solid #ccc; width:100%; max-height:200px; overflow-y:auto; z-index:10;">
                </div>
            </div>
        </div>

        <div class="dept-section">
            <label for="dept-select">Department:</label>
            <select id="dept-select" style="width:180px;">
                <option value="">All Departments</option>
            </select>
        </div>

        <button id="find-btn">Find</button>
    </div>

    <div class="listhere">
        <div id="headerformanageemployee">
            <div class="entries">
                <label>Show</label>
                <input type="number" id="entryCount" min="1" value="10">
                <label>entries</label>
            </div>
            <div class="searchbox">
                <label for="searchemployee">Search:</label>
                <input type="text" id="searchemployee" placeholder="Search employee">
            </div>
        </div>

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
                <tbody id="payslip-body"></tbody>
            </table>
        </div>

        <div class="buttonscontrol">
            <button id="previouspage">Previous</button>
            <button id="nextpage">Next</button>
        </div>
    </div>
</div>

<script>
const currentDate = new Date();
const currentYear = currentDate.getFullYear();

// ===== Month Dropdown =====
function getMonthNames() {
    return Array.from({length:12}, (_,i)=>new Intl.DateTimeFormat('en',{month:'long'}).format(new Date(currentYear,i,1)));
}
function generateMonthOptions() {
    const monthDropdown = document.getElementById('month-dropdown');
    monthDropdown.innerHTML = '';
    getMonthNames().forEach(month=>{
        const div = document.createElement('div');
        div.textContent = month;
        div.style.padding = "5px";
        div.style.cursor = "pointer";
        div.onmouseover = ()=>div.style.background="#eee";
        div.onmouseout = ()=>div.style.background="";
        div.onclick = ()=>{
            document.getElementById('month-input').value = month;
            monthDropdown.style.display='none';
        };
        monthDropdown.appendChild(div);
    });
}
document.getElementById('month-input').addEventListener('click', ()=>{
    const dd = document.getElementById('month-dropdown');
    dd.style.display = dd.style.display==='block'?'none':'block';
});
document.addEventListener('click',(e)=>{
    if(!e.target.closest('.month-input-container')) document.getElementById('month-dropdown').style.display='none';
});
document.getElementById('year-input').value = currentYear;
document.getElementById('month-input').value = new Intl.DateTimeFormat('en',{month:'long'}).format(currentDate);
generateMonthOptions();

// ===== Load Departments =====
async function loadDepartments(){
    const res = await fetch("../controller/departments_api.php");
    const data = await res.json();
    const select = document.getElementById('dept-select');
    data.departments.forEach(d=>{
        const opt = document.createElement("option");
        opt.value = d.dept_id;
        opt.textContent = d.department_name;
        select.appendChild(opt);
    });
}
loadDepartments();

// ===== Load Payslips =====
let currentPage=1;
async function loadPayslips(){
    const year=document.getElementById('year-input').value;
    const month=document.getElementById('month-input').value;
    const dept=document.getElementById('dept-select').value;
    const search=document.getElementById('searchemployee').value;
    const entries=document.getElementById('entryCount').value;

    let url=`../controller/payslip_api.php?year=${year}&month=${month}&search=${encodeURIComponent(search)}&page=${currentPage}&entries=${entries}`;
    if(dept) url+=`&dept=${dept}`;

    const res = await fetch(url);
    const data = await res.json();
    const tbody = document.getElementById('payslip-body');
    tbody.innerHTML='';

    data.forEach(row=>{
        const tr=document.createElement('tr');
        tr.innerHTML=`
            <td>${row.employee_id}</td>
            <td>${row.employee_name}</td>
            <td>Rs. ${Number(row.net_salary).toLocaleString()}</td>
            <td>${row.created_at}</td>
            <td>${row.month}</td>
            <td>
                <select class="status-dropdown" readonly disabled>
                    <option value="paid" ${row.status==='paid'?'selected':''}>Paid</option>
                    <option value="pending" ${row.status==='pending'?'selected':''}>Pending</option>
                </select>
            </td>
            <td>
                <button class="btn-download" 
                    data-employee="${row.employee_id}" 
                    data-year="${row.year}" 
                    data-month="${row.month}">
                    <i class="fa-solid fa-download"></i> Download
                </button>
                <button class="btn-delete" 
                    data-employee="${row.employee_id}" 
                    data-year="${row.year}" 
                    data-month="${row.month}">
                    <i class="fa-solid fa-xmark"></i> Delete
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    attachRowEvents();
}

// ===== Show Alert =====
function showMessage(type,text){
    const msg=document.getElementById("alertMessage");
    msg.className="message "+type;
    msg.textContent=text;
    msg.style.display="block";
    setTimeout(()=>msg.style.display="none",3000);
}

// ===== Attach Events to Buttons =====
function attachRowEvents(){
    document.querySelectorAll(".btn-download").forEach(btn => {
    btn.addEventListener("click", async e => {
        const button = e.target.closest("button");
        const empId = button.dataset.employee;
        const year = button.dataset.year;
        const month = button.dataset.month;

        // Build URL
        const url = `../controller/download_payslip.php?employee_id=${empId}&year=${year}&month=${month}`;

        // Fetch PDF as blob
        const response = await fetch(url);
        const blob = await response.blob();
        const fileURL = URL.createObjectURL(blob);

        // Open in new tab
        window.open(fileURL, "_blank");

        // Trigger automatic download
        const a = document.createElement("a");
        a.href = fileURL;
        a.download = `payslip_${empId}_${month}_${year}.pdf`;
        document.body.appendChild(a);
        a.click();
        a.remove();

        // Release blob memory
        URL.revokeObjectURL(fileURL);
    });
});


    document.querySelectorAll(".btn-delete").forEach(btn=>{
        btn.addEventListener("click", async e=>{
            if(!confirm("Are you sure you want to delete this payslip?")) return;

            const button = e.target.closest("button");
            const empId = button.dataset.employee;
            const year  = button.dataset.year;
            const month = button.dataset.month;

            const fd=new FormData();
            fd.append("employee_id",empId);
            fd.append("year",year);
            fd.append("month",month);

            const res=await fetch("../controller/delete_payslip.php",{method:"POST",body:fd});
            const result=await res.json();
            showMessage(result.success?"success":"error",result.message);
            loadPayslips();
        });
    });
}

// ===== Event Listeners =====
document.getElementById('find-btn').addEventListener('click',()=>{ currentPage=1; loadPayslips(); });
document.getElementById('searchemployee').addEventListener('input',()=>{ currentPage=1; loadPayslips(); });
document.getElementById('entryCount').addEventListener('change',()=>{ currentPage=1; loadPayslips(); });
document.getElementById('nextpage').addEventListener('click',()=>{ currentPage++; loadPayslips(); });
document.getElementById('previouspage').addEventListener('click',()=>{ if(currentPage>1) currentPage--; loadPayslips(); });

// ===== Init =====
loadPayslips();
</script>


<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
