<nav id="sidebar" class="expanded">
  <ul class="nav flex-column">

    <li class="nav-item mb-2">
      <a href="dashboard.php" class="nav-link">
        <i class="bi bi-speedometer2 me-2"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#employeeSubmenu" aria-expanded="false">
        <i class="bi bi-people me-2"></i>
        <span>Employee</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="employeeSubmenu">
        <li class="nav-item"><a href="addemployee.php" class="nav-link">Add Employee</a></li>
        <li class="nav-item"><a href="manageemployee.php" class="nav-link">Manage Employee</a></li>
      </ul>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#departmentSubmenu" aria-expanded="false">
        <i class="bi bi-building me-2"></i>
        <span>Department</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="departmentSubmenu">
        <li class="nav-item"><a href="adddepartment.php" class="nav-link">Add Department</a></li>
        <li class="nav-item"><a href="managedepartment.php" class="nav-link">Manage Department</a></li>
      </ul>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#payslipSubmenu" aria-expanded="false">
        <i class="bi bi-receipt me-2"></i>
        <span>Payslip</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="payslipSubmenu">
        <li class="nav-item"><a href="createpayslip.php" class="nav-link">Create Payslip</a></li>
        <li class="nav-item"><a href="paysliplist.php" class="nav-link">Payslip List</a></li>
      </ul>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#attendanceSubmenu" aria-expanded="false">
        <i class="bi bi-calendar-check me-2"></i>
        <span>Attendance</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="attendanceSubmenu">
        <li class="nav-item"><a href="manageattendance.php" class="nav-link">Manage Attendance</a></li>
      </ul>
    </li>

    <!-- <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#leaveSubmenu" aria-expanded="false">
        <i class="bi bi-calendar-minus me-2"></i>
        <span>Leave</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="leaveSubmenu">
        <li class="nav-item"><a href="addleave.php" class="nav-link">Add Leave</a></li>
        <li class="nav-item"><a href="manageleave.php" class="nav-link">Manage Leave</a></li>
        <li class="nav-item"><a href="addleavetype.php" class="nav-link">Add Leave Type</a></li>
        <li class="nav-item"><a href="manageleavetype.php" class="nav-link">Manage Leave Type</a></li>
      </ul>
    </li> -->

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#holidaySubmenu" aria-expanded="false">
        <i class="bi bi-calendar-event me-2"></i>
        <span>Holiday</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="holidaySubmenu">
        <li class="nav-item"><a href="addholiday.php" class="nav-link">Add Holiday</a></li>
        <li class="nav-item"><a href="manageholiday.php" class="nav-link">Manage Holiday</a></li>
      </ul>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#dailySubmenu" aria-expanded="false">
        <i class="bi bi-journal-text me-2"></i>
        <span>Daily</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="dailySubmenu">
        <!-- <li class="nav-item"><a href="addnotice.php" class="nav-link">Add Notice</a></li>
        <li class="nav-item"><a href="managenotice.php" class="nav-link">Manage Notice</a></li> -->
        <li class="nav-item"><a href="addquote.php" class="nav-link">Add Quote</a></li>
        <li class="nav-item"><a href="managequote.php" class="nav-link">Manage Quote</a></li>
      </ul>
    </li>

    <li class="nav-item mb-2">
      <a href="activitylog.php" class="nav-link">
        <i class="bi bi-activity me-2"></i>
        <span>Activity Log</span>
      </a>
    </li>

    <li class="nav-item mb-2">
      <a class="nav-link submenu-toggle d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" data-bs-target="#settingSubmenu" aria-expanded="false">
        <i class="bi bi-gear me-2"></i>
        <span>Setting</span>
        <i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul class="collapse nav flex-column ms-3" id="settingSubmenu">
        <li class="nav-item"><a href="configuration.php" class="nav-link">Configuration</a></li>
        <li class="nav-item"><a href="changepassword.php" class="nav-link">Change Password</a></li>
      </ul>
    </li>

    <li class="nav-item mt-auto">
  <a href="../controller/logout.php" class="nav-link">
    <i class="bi bi-box-arrow-right me-2"></i>
    <span>Log Out</span>
  </a>
</li>

  </ul>
</nav>
