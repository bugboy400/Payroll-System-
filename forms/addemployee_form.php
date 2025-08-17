php

<div id="main-content" class="container my-5 p-4 bg-light rounded" 
     style="max-width: 900px; margin-left: 380px;"> <!-- shifted right -->
  
  <!-- Header -->
  <h3 class="mb-4" style="padding-left: 8px;">Add Employee</h3>

  <!-- Centered form content -->
  <div class="d-flex flex-column align-items-center w-100">

    <!-- PERSONAL DETAILS -->
    <div class="card mb-4 w-100" style="max-width: 700px;">
      <div class="card-header text-center"><h4>Personal Details</h4></div>
      <div class="card-body d-flex flex-column gap-3">
        
        <div>
          <label for="name" class="form-label">Name</label>
          <input type="text" name="name" id="name" class="form-control">
        </div>

        <div>
          <label for="fatherName" class="form-label">Father Name</label>
          <input type="text" name="fatherName" id="fatherName" class="form-control">
        </div>

        <div>
          <label for="dob" class="form-label">Date of Birth</label>
          <input type="date" name="dob" id="dob" class="form-control">
        </div>

        <div>
          <label for="gender" class="form-label">Gender</label>
          <select class="form-select" required>
            <option value="" disabled selected hidden>Select</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div>
          <label for="nationality" class="form-label">Nationality</label>
          <input type="text" name="nationality" id="nationality" class="form-control">
        </div>

        <div>
          <label for="phno" class="form-label">Phone No. 1</label>
          <input type="number" name="phonenumber" id="phno" class="form-control">
        </div>

        <div>
          <label for="phno2" class="form-label">Phone No. 2</label>
          <input type="number" name="phonenumber" id="phno2" class="form-control">
        </div>

        <div>
          <label for="localaddress" class="form-label">Local Address</label>
          <input type="text" name="localaddress" id="localaddress" class="form-control">
        </div>

        <div>
          <label for="permanentaddress" class="form-label">Permanent Address</label>
          <input type="text" name="permanentaddress" id="permanentaddress" class="form-control">
        </div>

        <div>
          <label for="maritalstatus" class="form-label">Marital Status</label>
          <select class="form-select" required>
            <option value="" disabled selected hidden>Select</option>
            <option value="married">Married</option>
            <option value="unmarried">Unmarried</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div>
          <label for="imageSelect" class="form-label">Photo</label>
          <input type="file" id="imageSelect" name="imageSelect" class="form-control" accept="image/*">
        </div>

      </div>
    </div>

    <!-- COMPANY DETAILS -->
    <div class="card mb-4 w-100" style="max-width: 700px;">
      <div class="card-header text-center"><h4>Company Details</h4></div>
      <div class="card-body d-flex flex-column gap-3">

        <div>
          <label for="empid" class="form-label">Employee Id</label>
          <input type="number" name="empid" id="empid" class="form-control" placeholder="Auto Generated" readonly>
        </div>

        <div>
          <label for="dept" class="form-label">Department</label>
          <select name="dept" id="dept" class="form-select" onchange="updateDesignations()">
            <option value="">Select Department</option>
            <option value="account">Account</option>
            <option value="hr">HR</option>
            <option value="it">IT</option>
          </select>
        </div>

        <div>
          <label for="designation" class="form-label">Designation</label>
          <select name="designation" id="designation" class="form-select">
            <option value="">Select Designation</option>
          </select>
        </div>

        <div>
          <label for="dateofjoin" class="form-label">Date of Joining</label>
          <input type="date" name="dateofjoin" id="dateofjoin" class="form-control">
        </div>

        <div>
          <label for="dateofleave" class="form-label">Date of Leaving</label>
          <input type="date" name="dateofleave" id="dateofleave" class="form-control">
        </div>

      </div>
    </div>

    <!-- FINANCIAL DETAILS -->
    <div class="card mb-4 w-100" style="max-width: 700px;">
      <div class="card-header text-center"><h4>Financial Details</h4></div>
      <div class="card-body d-flex flex-column gap-3">

        <div>
          <label for="basicsal" class="form-label">Basic Salary</label>
          <input type="number" name="basicsal" id="basicsal" class="form-control">
        </div>

        <!-- ALLOWANCES -->
        <div id="allowances-container" class="d-flex flex-column gap-2">
          <label class="form-label">Allowances</label>
          <div class="d-flex gap-2 allowance-row">
            <select name="allowance" class="form-select allowancename">
              <option value="homeallowance">Home Allowance</option>
              <option value="healthallowance">Health Allowance</option>
              <option value="overtimeallowance">OT Allowance</option>
              <option value="festiveallowance">Festive Allowance</option>
            </select>
            <input type="number" name="allowanceamt" class="form-control allowanceamt">
            <button type="button" class="btn btn-success add-allowance-btn">+</button>
            <button type="button" class="btn btn-danger remove-allowance-btn">−</button>
          </div>
        </div>

        <!-- DEDUCTIONS -->
        <div id="deductions-container" class="d-flex flex-column gap-2">
          <label class="form-label">Deductions</label>
          <div class="d-flex gap-2 deduction-row">
            <select name="deduction" class="form-select deductionname">
              <option value="providentfund">Provident Fund</option>
              <option value="leave">Leave</option>
            </select>
            <input type="number" name="deductionamt" class="form-control deductionamt">
            <button type="button" class="btn btn-success add-deduction-btn">+</button>
            <button type="button" class="btn btn-danger remove-deduction-btn">−</button>
          </div>
        </div>

        <!-- CALCULATE BUTTON -->
        <button type="submit" id="calculate-btn" class="btn btn-primary">Calculate</button>

        <!-- TOTAL SALARY -->
        <div>
          <label for="totalsalary" class="form-label">TOTAL Salary</label>
          <input type="number" name="totalsal" id="totalsal" class="form-control" readonly>
        </div>

      </div>
    </div>

    <!-- SAVE BUTTON -->
    <button id="savedetails" type="submit" class="btn btn-success w-100" style="max-width: 700px;">Save</button>

  </div>
</div>