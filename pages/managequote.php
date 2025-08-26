<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../layouts/login.php");
    exit();
}

require_once '../config/db.php';

$page_title = "Manage Quote";
$page_css = [
    "/payrollself/includes/dashboard.css",
    "/payrollself/includes/managequote.css"
];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $quote_text = trim($_POST['quote_text'] ?? '');
    $quote_author = trim($_POST['quote_author'] ?? 'Anonymous');

    if ($action === 'delete' && $quote_text !== '') {
        $stmt = $conn->prepare("DELETE FROM daily_quotes WHERE quote_text=? AND quote_author=?");
        $stmt->bind_param('ss', $quote_text, $quote_author);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>true]);
        exit;
    }

    if ($action === 'edit' && $quote_text !== '') {
        // Duplicate check
        $stmtDup = $conn->prepare("SELECT COUNT(*) FROM daily_quotes WHERE LOWER(quote_text)=LOWER(?) AND LOWER(quote_author)=LOWER(?)");
        $stmtDup->bind_param('ss', $quote_text, $quote_author);
        $stmtDup->execute();
        $stmtDup->bind_result($countDup);
        $stmtDup->fetch();
        $stmtDup->close();

        if ($countDup > 1) {
            echo json_encode(['success'=>false,'error'=>'Duplicate quote exists.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE daily_quotes SET quote_text=?, quote_author=? WHERE quote_text=? AND quote_author=?");
        $stmt->bind_param('ssss', $quote_text, $quote_author, $quote_text, $quote_author);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>true]);
        exit;
    }

    if ($action === 'implement' && $quote_text !== '') {
        // Clear previous implemented
        $conn->query("UPDATE daily_quotes SET status=NULL");
        // Set selected quote as implemented
        $stmt = $conn->prepare("UPDATE daily_quotes SET status='implemented' WHERE quote_text=? AND quote_author=?");
        $stmt->bind_param('ss', $quote_text, $quote_author);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>true]);
        exit;
    }
}

// Fetch all quotes
$result = $conn->query("SELECT quote_text, quote_author, status FROM daily_quotes ORDER BY created_at DESC");
$quotes = [];
while ($row = $result->fetch_assoc()) {
    $quotes[] = $row;
}
?>

<?php ob_start(); ?>
<div id="main-content">
    <h2>Manage Quotes</h2>

    <div style="display:flex; justify-content: space-between; margin-bottom:10px;">
        <div>
            Show 
            <select id="entryCount">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
            entries
        </div>
        <div>
            Search by Author: <input type="text" id="searchAuthor" placeholder="Enter author">
        </div>
    </div>

    <div class="table-responsive">
        <table id="quoteTable" border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Quote</th>
                    <th>Quote Author</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                    <tr data-status="<?= $q['status'] ?>" class="<?= $q['status']=='implemented'?'highlight':'' ?>">
                        <td class="quote-text"><?= htmlspecialchars($q['quote_text']) ?></td>
                        <td class="quote-author"><?= htmlspecialchars($q['quote_author']) ?></td>
                        <td>
                            <button class="implement-btn">Implement</button>
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const table = document.getElementById('quoteTable');
const entryCount = document.getElementById('entryCount');
const searchAuthor = document.getElementById('searchAuthor');

// Delete
table.addEventListener('click', e => {
    if(e.target.classList.contains('delete-btn')){
        const row = e.target.closest('tr');
        const quote_text = row.querySelector('.quote-text').textContent;
        const quote_author = row.querySelector('.quote-author').textContent;
        if(confirm('Delete this quote?')){
            fetch('',{
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`action=delete&quote_text=${encodeURIComponent(quote_text)}&quote_author=${encodeURIComponent(quote_author)}`
            }).then(res=>res.json())
            .then(data=>{ if(data.success) row.remove(); });
        }
    }
});

// Edit with inline duplicate check
table.addEventListener('click', e => {
    if(e.target.classList.contains('edit-btn')){
        const row = e.target.closest('tr');
        const textCell = row.querySelector('.quote-text');
        const authorCell = row.querySelector('.quote-author');

        if(e.target.textContent === 'Edit'){
            const txt = textCell.textContent;
            const auth = authorCell.textContent;
            textCell.innerHTML = `<textarea rows="2">${txt}</textarea>`;
            authorCell.innerHTML = `<input type="text" value="${auth}">`;
            e.target.textContent = 'Save';
        } else {
            const quote_text = textCell.querySelector('textarea').value.trim();
            const quote_author = authorCell.querySelector('input').value.trim() || 'Anonymous';
            if(!quote_text){ alert('Quote cannot be empty'); return; }

            fetch('',{
                method:'POST',
                headers:{'Content-Type':'application/x-www-form-urlencoded'},
                body:`action=edit&quote_text=${encodeURIComponent(quote_text)}&quote_author=${encodeURIComponent(quote_author)}`
            }).then(res=>res.json())
            .then(data=>{
                if(data.success){
                    textCell.textContent = quote_text;
                    authorCell.textContent = quote_author;
                    e.target.textContent = 'Edit';
                } else {
                    alert(data.error || 'Error editing');
                }
            });
        }
    }
});

// Implement
table.addEventListener('click', e=>{
    if(e.target.classList.contains('implement-btn')){
        const row = e.target.closest('tr');
        const quote_text = row.querySelector('.quote-text').textContent;
        const quote_author = row.querySelector('.quote-author').textContent;

        fetch('',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:`action=implement&quote_text=${encodeURIComponent(quote_text)}&quote_author=${encodeURIComponent(quote_author)}`
        }).then(res=>res.json())
        .then(data=>{
            if(data.success){
                table.querySelectorAll('tr').forEach(r=>r.classList.remove('highlight'));
                row.classList.add('highlight');
            }
        });
    }
});

// Search
searchAuthor.addEventListener('input', ()=>{
    const filter = searchAuthor.value.toLowerCase();
    table.querySelectorAll('tbody tr').forEach(row=>{
        const author = row.querySelector('.quote-author').textContent.toLowerCase();
        row.style.display = author.includes(filter)? '' : 'none';
    });
});

// Entries
entryCount.addEventListener('change', ()=>{
    const perPage = parseInt(entryCount.value);
    table.querySelectorAll('tbody tr').forEach((row,i)=>{
        row.style.display = i<perPage ? '' : 'none';
    });
});
entryCount.dispatchEvent(new Event('change'));
</script>

<style>
.highlight { 
    background-color: #d1e7dd !important;  /* Ensure overriding any odd/even row striping */ 
}
</style>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>
