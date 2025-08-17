<?php
$page_title = "Configuration";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/configuration.css"
];

ob_start();
?>

<div id="main-content" class="p-4">
    <h3 class="mb-4">Configuration</h3>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="configTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button"
          role="tab" aria-controls="company" aria-selected="true">
          Company Details
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="logo-tab" data-bs-toggle="tab" data-bs-target="#logo" type="button" role="tab"
          aria-controls="logo" aria-selected="false">
          Logo and Title
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="allowance-tab" data-bs-toggle="tab" data-bs-target="#allowance" type="button"
          role="tab" aria-controls="allowance" aria-selected="false">
          Allowance and Deduction
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content border border-top-0 p-3" id="configTabsContent">
      <div class="tab-pane fade show active" id="company" role="tabpanel" aria-labelledby="company-tab">
        <!-- <h5>Company Details</h5> -->
        <div class="tab-pane fade show active" id="company" role="tabpanel" aria-labelledby="company-tab">
          <!-- <h5 class="mb-4 text-center">Company Details</h5> -->

          <form class="container" style="max-width: 700px;">
            <!-- Company Name -->
            <div class="row mb-3 align-items-center">
              <label for="companyName" class="col-sm-4 col-form-label">Company Name</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="companyName" placeholder="Enter company name">
              </div>
            </div>

            <!-- Phone No -->
            <div class="row mb-3 align-items-center">
              <label for="phone" class="col-sm-4 col-form-label">Phone No</label>
              <div class="col-sm-8">
                <input type="tel" class="form-control" id="phone" placeholder="Enter phone number">
              </div>
            </div>

            <!-- Email -->
            <div class="row mb-3 align-items-center">
              <label for="email" class="col-sm-4 col-form-label">Email</label>
              <div class="col-sm-8">
                <input type="email" class="form-control" id="email" placeholder="Enter email">
              </div>
            </div>

            <!-- Website URL -->
            <div class="row mb-3 align-items-center">
              <label for="website" class="col-sm-4 col-form-label">Website URL</label>
              <div class="col-sm-8">
                <input type="url" class="form-control" id="website" placeholder="Enter website URL">
              </div>
            </div>

            <!-- Company Address -->
            <div class="row mb-3 align-items-center">
              <label for="address" class="col-sm-4 col-form-label">Company Address</label>
              <div class="col-sm-8">
                <textarea class="form-control" id="address" rows="2" placeholder="Enter address"></textarea>
              </div>
            </div>

            <!-- City -->
            <div class="row mb-3 align-items-center">
              <label for="city" class="col-sm-4 col-form-label">City</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="city" placeholder="Enter city">
              </div>
            </div>

            <!-- State -->
            <div class="row mb-3 align-items-center">
              <label for="state" class="col-sm-4 col-form-label">State</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="state" placeholder="Enter state">
              </div>
            </div>

            <!-- Postal Code -->
            <div class="row mb-3 align-items-center">
              <label for="postal" class="col-sm-4 col-form-label">Postal Code</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="postal" placeholder="Enter postal code">
              </div>
            </div>

            <!-- Country -->
            <div class="row mb-3 align-items-center">
              <label for="country" class="col-sm-4 col-form-label">Country</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="country" placeholder="Enter country">
              </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
              <button type="submit" class="btn btn-primary px-4">Save</button>
            </div>
          </form>
        </div>

      </div>


      <div class="tab-pane fade" id="logo" role="tabpanel" aria-labelledby="logo-tab">

        <form class="container" style="max-width: 700px;">
          <!-- Site Logo -->
          <div class="row mb-3 align-items-center">
            <label for="siteLogo" class="col-sm-4 col-form-label">Site Logo</label>
            <div class="col-sm-8">
              <input type="file" class="form-control" id="siteLogo" accept="image/*">
            </div>
          </div>

          <!-- Title -->
          <div class="row mb-3 align-items-center">
            <label for="siteTitle" class="col-sm-4 col-form-label">Title</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="siteTitle" placeholder="Enter site title">
            </div>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="btn btn-primary px-4">Save</button>
          </div>
        </form>
      </div>


<div class="tab-pane fade" id="allowance" role="tabpanel" aria-labelledby="allowance-tab">
  <h5 class="mb-4 text-center">Allowance and Deduction</h5>

  <div id="allow-deduct" class="container mt-4">
    <div class="row g-4">
      <!-- ALLOWANCE -->
      <div class="col-lg-6">
        <div class="form-section border p-3 rounded">
          <h5>Allowances</h5>
          <div id="allowances-container">
            <div class="d-flex gap-2 mb-2 allowance-row">
              <select name="allowance" class="form-select allowancename">
                <option value="homeallowance">Home Allowance</option>
                <option value="healthallowance">Health Allowance</option>
                <option value="overtimeallowance">OT Allowance</option>
                <option value="festiveallowance">Festive Allowance</option>
              </select>
              <input type="number" name="allowanceamt" class="form-control allowanceamt" placeholder="Amount">
              <button type="button" class="btn btn-success add-allowance-btn">+</button>
              <button type="button" class="btn btn-danger remove-allowance-btn">×</button>
            </div>
          </div>

          <hr class="mt-4">

          <h6 class="mt-3">Other Allowance</h6>
          <div class="row mt-2">
            <div class="col-6">
              <input type="text" name="otherallowancetitle" class="form-control" placeholder="Title">
            </div>
            <div class="col-6">
              <input type="number" name="otherallowanceamt" class="form-control" placeholder="Amount">
            </div>
          </div>
        </div>
      </div>

      <!-- DEDUCTIONS -->
      <div class="col-lg-6">
        <div class="form-section border p-3 rounded">
          <h5>Deductions</h5>
          <div id="deductions-container">
            <div class="d-flex gap-2 mb-2 deduction-row">
              <select name="deduction" class="form-select deductionname">
                <option value="providentfund">Provident Fund</option>
                <option value="leave">Leave</option>
              </select>
              <input type="number" name="deductionamt" class="form-control deductionamt" placeholder="Amount">
              <button type="button" class="btn btn-success add-deduction-btn">+</button>
              <button type="button" class="btn btn-danger remove-deduction-btn">×</button>
            </div>
          </div>

          <hr class="mt-4">

          <h6 class="mt-3">Other Deduction</h6>
          <div class="row mt-2">
            <div class="col-6">
              <input type="text" name="otherdeductiontitle" class="form-control" placeholder="Title">
            </div>
            <div class="col-6">
              <input type="number" name="otherdeductionamt" class="form-control" placeholder="Amount">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Save Button -->
    <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary px-4">Save</button>
    </div>
  </div>
</div>

    </div>
  </div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
