<?php
// Admin Verify Documents
require_once '../config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$success = '';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc_id = $_POST['doc_id'];
    $action = $_POST['action'];  // 'approve' or 'reject'
    $status = ($action === 'approve') ? 'verified' : 'rejected';
    $verified_at = ($action === 'approve') ? date('Y-m-d H:i:s') : null;

    $sql = "UPDATE documents SET status = ?, verified_at = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $verified_at, $doc_id);
    if ($stmt->execute()) {
        $desc = "Admin $action doc $doc_id";
        logAudit('verify_doc', $admin_id, $desc);
        $success = "Document $action successfully!";
    } else {
        $error = "Update failed.";
    }
    $stmt->close();
}

// Fetch pending docs
$sql = "SELECT d.*, u.full_name FROM documents d JOIN users u ON d.user_id = u.id WHERE d.status = 'pending' ORDER BY d.uploaded_at DESC";
$pending_docs = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/admin_header.php'; ?>
<div class="container" style="padding: 40px 0;">
    <h2 style="color: var(--gov-deep);">Verify Documents (Pending: <?php echo count($pending_docs); ?>)</h2>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!empty($pending_docs)): ?>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr style="background: var(--gov-green); color: white;">
            <th style="padding: 10px; border: 1px solid #ddd;">User</th>
            <th>Doc Type</th>
            <th>Uploaded</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($pending_docs as $doc): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $doc['full_name']; ?></td>
            <td><?php echo $doc['doc_type']; ?></td>
            <td><?php echo date('Y-m-d', strtotime($doc['uploaded_at'])); ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                    <button type="submit" name="action" value="approve" style="background: green; color: white; border: none; padding: 5px;">Approve</button>
                    <button type="submit" name="action" value="reject" style="background: red; color: white; border: none; padding: 5px;">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No pending documents.</p>
    <?php endif; ?>
    <a href="dashboard.php">Back to Dashboard</a>
</div>
<?php include '../includes/admin_footer.php'; ?>