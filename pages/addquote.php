<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

require_once '../config/db.php';

$page_title = "Add Quote";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/addquote.css"
];

$message = '';
$message_type = '';
$quote_text = '';
$quote_author = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quote_text = trim($_POST['quote_text']);
    $quote_author = trim($_POST['quote_author']);
    if ($quote_text !== '') {
        if ($quote_author === '') $quote_author = 'Anonymous';

        // Check for duplicates (case-insensitive)
        $checkStmt = $conn->prepare("
            SELECT COUNT(*) as cnt FROM daily_quotes 
            WHERE LOWER(quote_text) = LOWER(?) AND LOWER(quote_author) = LOWER(?)
        ");
        $checkStmt->bind_param('ss', $quote_text, $quote_author);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            $message = "This quote by the same author already exists.";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO daily_quotes (quote_text, quote_author)
                VALUES (?, ?)
            ");
            $stmt->bind_param('ss', $quote_text, $quote_author);
            if ($stmt->execute()) {
                $message = "Quote saved successfully.";
                $message_type = "success";
                $quote_text = '';
                $quote_author = '';
            } else {
                $message = "Error saving quote: " . $stmt->error;
                $message_type = "error";
            }
            $stmt->close();
        }
    } else {
        $message = "Quote text cannot be empty.";
        $message_type = "error";
    }
}
?>

<?php ob_start(); ?>
<div id="main-content">
    <div class="quote-card">
        <h2>Add Quote</h2>

        <?php if($message): ?>
            <p class="message <?= $message_type ?>" id="msg"><?= htmlspecialchars($message) ?></p>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('msg');
                    if(msg) msg.style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>

        <form method="post" class="quote-form">
            <label for="quote_text">Quote Text:</label>
            <textarea name="quote_text" id="quote_text" rows="5" placeholder="Enter quote"><?= htmlspecialchars($quote_text) ?></textarea>
            
            <label for="quote_author">Author:</label>
            <input type="text" name="quote_author" id="quote_author" value="<?= htmlspecialchars($quote_author) ?>" placeholder="Enter author name">
            
            <button type="submit" class="btn-save">Save Quote</button>
        </form>
    </div>
</div>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
