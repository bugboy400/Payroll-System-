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

require_once '../config/db.php';

$page_title = "Add Holiday";
$page_css = [
    "/payrollself/includes/dashboard.css",
];

$message = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $holiday_name = trim($_POST['holiday_name'] ?? '');
    $holiday_date = $_POST['holiday_date'] ?? '';
    $description  = trim($_POST['description'] ?? '');

    if ($holiday_name && $holiday_date) {
        $stmt = $conn->prepare("INSERT INTO holidays (holiday_name, holiday_date, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $holiday_name, $holiday_date, $description);

        if ($stmt->execute()) {
            $message = "<p class='success-msg'>Holiday added successfully!</p>";
        } else {
            if ($conn->errno == 1062) {
                $message = "<p class='warning-msg'>The holiday <b>$holiday_name</b> already exists on <b>$holiday_date</b>.</p>";
            } else {
                $message = "<p class='error-msg'>Error: " . $conn->error . "</p>";
            }
        }
        $stmt->close();
    } else {
        $message = "<p class='error-msg'>Please fill all required fields.</p>";
    }
}

ob_start();
?>

<h2 class="page-heading">Add Holiday</h2>

<div class="holiday-container">
    <?= $message ?>

    <div class="holiday-form">
        <form method="post">
            <div class="form-group">
                <label for="holiday_name">Holiday Name:</label>
                <input type="text" id="holiday_name" name="holiday_name" required>
            </div>

            <div class="form-group">
                <label for="holiday_date">Date:</label>
                <input type="date" id="holiday_date" name="holiday_date" required>
            </div>

            <div class="form-group">
                <label for="description">Description (optional):</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <button type="submit" class="btn-save">➕ Save Holiday</button>
        </form>
    </div>
</div>

<style>
    .page-heading {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 2px solid #444;
        padding-bottom: 5px;
        text-align: left;
    }

    .holiday-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 20px;
    }

    .holiday-form {
        width: 100%;
        max-width: 500px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    textarea {
        min-height: 80px;
        resize: vertical;
    }

    .btn-save {
        background: #4CAF50;
        color: white;
        font-size: 15px;
        font-weight: 600;
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .btn-save:hover {
        background: #45a049;
    }

    .success-msg {
        color: green;
        font-weight: 600;
        margin-bottom: 15px;
        text-align: center;
    }

    .warning-msg {
        color: #e69500;
        font-weight: 600;
        margin-bottom: 15px;
        text-align: center;
    }

    .error-msg {
        color: red;
        font-weight: 600;
        margin-bottom: 15px;
        text-align: center;
    }
</style>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
