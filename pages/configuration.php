<?php
session_start();
require_once '../config/db.php'; // <-- make sure db connection is included

// If no active session, redirect to login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../layouts/login.php");
    exit();
}

// Prevent browser from caching this page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$page_title = "Configuration";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/configuration.css"
];

// Fetch company details for this admin
$stmt = $conn->prepare("SELECT * FROM companydetails WHERE admin_id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();

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
    </ul>

    <!-- Tab Content -->
    <div class="tab-content border border-top-0 p-3" id="configTabsContent">
      <!-- Company Details -->
      <div class="tab-pane fade show active" id="company" role="tabpanel" aria-labelledby="company-tab">

          <form class="container" style="max-width: 700px;" action="../controller/save_company.php" method="POST">
            <!-- Company Name -->
            <div class="row mb-3 align-items-center">
              <label for="companyName" class="col-sm-4 col-form-label">Company Name</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="companyName" name="company_name"
                       value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" placeholder="Enter company name">
              </div>
            </div>

            <!-- Phone No -->
            <div class="row mb-3 align-items-center">
              <label for="phone" class="col-sm-4 col-form-label">Phone No</label>
              <div class="col-sm-8">
                <input type="tel" class="form-control" id="phone" name="phone"
                       value="<?= htmlspecialchars($company['phone'] ?? '') ?>" placeholder="Enter phone number">
              </div>
            </div>

            <!-- Email -->
            <div class="row mb-3 align-items-center">
              <label for="email" class="col-sm-4 col-form-label">Email</label>
              <div class="col-sm-8">
                <input type="email" class="form-control" id="email" name="email"
                       value="<?= htmlspecialchars($company['email'] ?? '') ?>" placeholder="Enter email">
              </div>
            </div>

            <!-- Company Address -->
            <div class="row mb-3 align-items-center">
              <label for="address" class="col-sm-4 col-form-label">Company Address</label>
              <div class="col-sm-8">
                <textarea class="form-control" id="address" name="address" rows="2"
                          placeholder="Enter address"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- City -->
            <div class="row mb-3 align-items-center">
              <label for="city" class="col-sm-4 col-form-label">City</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="city" name="city"
                       value="<?= htmlspecialchars($company['city'] ?? '') ?>" placeholder="Enter city">
              </div>
            </div>

            <!-- State -->
            <div class="row mb-3 align-items-center">
              <label for="state" class="col-sm-4 col-form-label">State</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="state" name="state"
                       value="<?= htmlspecialchars($company['state'] ?? '') ?>" placeholder="Enter state">
              </div>
            </div>

            <!-- Postal Code -->
            <div class="row mb-3 align-items-center">
              <label for="postal" class="col-sm-4 col-form-label">Postal Code</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="postal" name="postal"
                       value="<?= htmlspecialchars($company['postal'] ?? '') ?>" placeholder="Enter postal code">
              </div>
            </div>

            <!-- Country -->
            <div class="row mb-3 align-items-center">
              <label for="country" class="col-sm-4 col-form-label">Country</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="country" name="country"
                       value="<?= htmlspecialchars($company['country'] ?? '') ?>" placeholder="Enter country">
              </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
              <button type="submit" class="btn btn-primary px-4">Save</button>
            </div>
          </form>
      </div>

      <!-- Logo and Title -->
      <div class="tab-pane fade" id="logo" role="tabpanel" aria-labelledby="logo-tab">
        <form class="container" style="max-width: 700px;" action="../controller/save_logo.php" method="POST" enctype="multipart/form-data">
          <!-- Site Logo -->
          <div class="row mb-3 align-items-center">
            <label for="siteLogo" class="col-sm-4 col-form-label">Site Logo</label>
            <div class="col-sm-8">
              <input type="file" class="form-control" id="siteLogo" name="siteLogo" accept="image/*">
            </div>
          </div>

          <!-- Title -->
          <div class="row mb-3 align-items-center">
            <label for="siteTitle" class="col-sm-4 col-form-label">Title</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="siteTitle" name="siteTitle"
                     value="<?= htmlspecialchars($company['title'] ?? '') ?>" placeholder="Enter site title">
            </div>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="btn btn-primary px-4">Save</button>
          </div>
        </form>
      </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
