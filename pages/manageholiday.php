<?php
$page_title = "Manage Holiday";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/manageholiday.css",
];

ob_start();
?>

<div id="main-content">
  <div class="manageholiday">
    <h3>Manage Holiday</h3>
  </div>

  <!-- Show entries + Search -->
  <div id="headerformanageholiday">
    <div class="entries">
      <label for="entryCount">Show</label>
      <input type="number" name="entries" id="entryCount" min="1" value="10">
      <label>entries</label>
    </div>
    <div class="searchbox">
      <label for="searchholiday">Search:</label>
      <input type="text" name="Search" id="searchholiday" placeholder="Search holiday">
    </div>
  </div>

  <!-- Holiday Table -->
  <div class="table-responsive">
    <table id="holidaydetails" cellpadding="8" cellspacing="0">
      <thead>
        <tr>
          <th>Date</th>
          <th>Holiday</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>2025-12-25</td>
          <td>Christmas</td>
          <td class="action-buttons">
            <button type="button" class="btn-edit">
              <i class="fa-solid fa-pen-fancy"></i> Edit Holiday
            </button>
            <button type="button" class="btn-delete">
              <i class="fa-solid fa-xmark"></i> Delete
            </button>
          </td>
        </tr>
        <tr>
          <td>2025-01-01</td>
          <td>New Year</td>
          <td class="action-buttons">
            <button type="button" class="btn-edit">
              <i class="fa-solid fa-pen-fancy"></i> Edit Holiday
            </button>
            <button type="button" class="btn-delete">
              <i class="fa-solid fa-xmark"></i> Delete
            </button>
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


<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
