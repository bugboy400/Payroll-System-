<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: ../layouts/login.php");
  exit();
}
require_once '../config/db.php';

$page_title = "Manage Holiday";
$page_css = [
  "/payrollself/includes/dashboard.css",
];

ob_start();

// Handle AJAX edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  $id = $_POST['id'] ?? '';
  $holiday_name = $_POST['holiday_name'] ?? '';
  $description = $_POST['description'] ?? '';
  $holiday_date = $_POST['holiday_date'] ?? '';

  if ($action === 'edit') {
    if (!$id || !$holiday_name) {
      echo "Holiday Name required";
      exit;
    }
    // Check duplicate
    $stmt = $conn->prepare("SELECT id FROM holidays WHERE holiday_date=? AND holiday_name=? AND id<>?");
    $stmt->bind_param("ssi", $holiday_date, $holiday_name, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      echo "Holiday already exists for this date";
      exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE holidays SET holiday_name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $holiday_name, $description, $id);
    if ($stmt->execute())
      echo "success";
    else
      echo $conn->error;
    $stmt->close();
    exit;
  }

  if ($action === 'delete') {
    if (!$id) {
      echo "Invalid ID";
      exit;
    }
    $stmt = $conn->prepare("SELECT holiday_name FROM holidays WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($holiday_name_deleted);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM holidays WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute())
      echo $holiday_name_deleted;
    else
      echo $conn->error;
    $stmt->close();
    exit;
  }
}
?>

<div id="main-content">
  <div class="manageholiday-header">
    <h3>Manage Holiday</h3>
  </div>

  <!-- Controls -->
  <div class="filters">
    <div class="entries">
      <label for="entryCount">Show</label>
      <input type="number" id="entryCount" min="1" value="7">
      <label>entries</label>
    </div>
    <div class="searchbox">
      <label for="searchholiday">Search:</label>
      <input type="text" id="searchholiday" placeholder="Search holiday">
    </div>
  </div>

  <div id="message"></div>

  <div class="table-responsive">
    <table id="holidaydetails">
      <thead>
        <tr>
          <th>Date</th>
          <th>Holiday</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="holidayBody">
        <?php
        $result = $conn->query("SELECT id, holiday_date, holiday_name, description FROM holidays ORDER BY holiday_date ASC");
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<tr data-id="' . $row['id'] . '">';
            echo '<td>' . $row['holiday_date'] . '</td>';
            echo '<td>
                    <div class="holiday-name" contenteditable="false" data-field="holiday_name">' . htmlspecialchars($row['holiday_name']) . '</div>
                    <div class="holiday-desc" contenteditable="false" data-field="description">' . htmlspecialchars($row['description']) . '</div>
                  </td>';
            echo '<td class="action-buttons">
                    <button class="btn-edit">✏️ Edit</button>
                    <button class="btn-delete">🗑 Delete</button>
                  </td>';
            echo '</tr>';
          }
        } else {
          echo '<tr><td colspan="3" style="text-align:center;">No holidays found</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="buttonscontrol">
    <button type="button" id="previouspage">Previous</button>
    <button type="button" id="nextpage">Next</button>
  </div>
</div>

<script>
  const holidayBody = document.getElementById('holidayBody');
  const entryInput = document.getElementById('entryCount');
  const searchInput = document.getElementById('searchholiday');
  const messageDiv = document.getElementById('message');
  let rows = Array.from(holidayBody.querySelectorAll('tr'));
  let currentPage = 1;
  let rowsPerPage = parseInt(entryInput.value);

  function displayTable() {
    const searchValue = searchInput.value.toLowerCase();
    const filteredRows = rows.filter(row => {
      const name = row.querySelector('[data-field="holiday_name"]')?.innerText.toLowerCase() || '';
      const desc = row.querySelector('[data-field="description"]')?.innerText.toLowerCase() || '';
      return name.includes(searchValue) || desc.includes(searchValue);
    });
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    holidayBody.innerHTML = '';
    filteredRows.slice(start, end).forEach(r => holidayBody.appendChild(r));
  }

  entryInput.addEventListener('input', () => { rowsPerPage = parseInt(entryInput.value) || 7; currentPage = 1; displayTable(); });
  searchInput.addEventListener('input', () => { currentPage = 1; displayTable(); });

  holidayBody.addEventListener('click', function (e) {
    const tr = e.target.closest('tr');
    const id = tr.dataset.id;
    const nameDiv = tr.querySelector('[data-field="holiday_name"]');
    const descDiv = tr.querySelector('[data-field="description"]');

    // Edit toggle
    if (e.target.classList.contains('btn-edit')) {
      if (e.target.innerText === '✏️ Edit') {
        nameDiv.contentEditable = true;
        descDiv.contentEditable = true;
        nameDiv.focus();
        e.target.innerText = '💾 Save';
      } else {
        nameDiv.contentEditable = false;
        descDiv.contentEditable = false;
        const name = nameDiv.innerText.trim();
        const desc = descDiv.innerText.trim();
        const date = tr.children[0].innerText;
        fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=edit&id=${id}&holiday_name=${encodeURIComponent(name)}&description=${encodeURIComponent(desc)}&holiday_date=${encodeURIComponent(date)}`
        }).then(res => res.text()).then(res => {
          if (res === 'success') {
            messageDiv.innerText = 'Saved successfully';
            setTimeout(() => { messageDiv.innerText = ''; }, 3000);
            e.target.innerText = '✏️ Edit';
          } else { alert(res); e.target.innerText = '✏️ Edit'; }
        });
      }
    }

    if (e.target.classList.contains('btn-delete')) {
      if (confirm("Are you sure to delete this holiday?")) {
        fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=delete&id=${id}`
        }).then(res => res.text()).then(res => {
          if (res) {
            messageDiv.innerText = `"${res}" deleted successfully`;
            tr.remove();
            rows = Array.from(holidayBody.querySelectorAll('tr'));
            displayTable();
            setTimeout(() => { messageDiv.innerText = ''; }, 3000);
          } else alert('Error deleting holiday');
        });
      }
    }
  });

  document.getElementById('nextpage').addEventListener('click', () => {
    const totalRows = rows.filter(row => {
      const name = row.querySelector('[data-field="holiday_name"]')?.innerText.toLowerCase() || '';
      const desc = row.querySelector('[data-field="description"]')?.innerText.toLowerCase() || '';
      return name.includes(searchInput.value.toLowerCase()) || desc.includes(searchInput.value.toLowerCase());
    }).length;
    if (currentPage * rowsPerPage < totalRows) { currentPage++; displayTable(); }
  });

  document.getElementById('previouspage').addEventListener('click', () => {
    if (currentPage > 1) { currentPage--; displayTable(); }
  });

  displayTable();
</script>

<style>
  #main-content {
    padding: 20px;
    font-family: Arial, sans-serif;
  }

  .manageholiday-header h3 {
    margin: 0;
    padding-bottom: 5px;
    border-bottom: 2px solid #333;
    font-size: 1.5rem;
    text-align: left;
  }

  .filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 15px 0;
  }

  input[type="number"],
  input[type="text"] {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
  }

  #message {
    color: green;
    text-align: center;
    margin-bottom: 10px;
  }

  .table-responsive {
    display: flex;
    justify-content: center;
  }

  #holidaydetails {
    border-collapse: collapse;
    width: 100%;
    max-width: 950px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  #holidaydetails th,
  #holidaydetails td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    vertical-align: top;
  }

  #holidaydetails th {
    background: #007bff;
    color: #fff;
    font-weight: 600;
  }

  .holiday-name {
    font-weight: bold;
  }

  .holiday-desc {
    font-size: 0.85rem;
    color: #555;
    margin-top: 2px;
  }

  .action-buttons {
    display: flex;
    gap: 8px;
  }

  .btn-edit,
  .btn-delete {
    padding: 5px 10px;
    font-size: 0.85rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn-edit {
    background: #17a2b8;
    color: #fff;
  }

  .btn-delete {
    background: #dc3545;
    color: #fff;
  }

  .buttonscontrol {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    gap: 10px;
  }

  .buttonscontrol button {
    padding: 5px 12px;
    border: 1px solid #999;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
  }
</style>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
