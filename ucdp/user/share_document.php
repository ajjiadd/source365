<?php
// Share Document Securely
require_once '../config.php';
require_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$doc_id = $_GET['id'] ?? 0;
$success = '';
$error = '';

if ($doc_id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $shared_with = sanitizeInput($_POST['shared_with']);

    // Check doc belongs to user
    $check_sql = "SELECT id FROM documents WHERE id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $doc_id, $user_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $token = generateShareToken($doc_id, $user_id);
        $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $sql = "INSERT INTO shares (doc_id, sharer_user_id, shared_with, share_token, expiry_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $doc_id, $user_id, $shared_with, $token, $expiry);
        if ($stmt->execute()) {
            $share_url = SITE_URL . "share.php?token=$token";  // Later file for viewing share
            logAudit('share_document', $user_id, "Shared doc $doc_id with $shared_with");
            $success = "Share link: <a href='$share_url' target='_blank'>$share_url</a> (Expires in 24h)";
        } else {
            $error = "Share failed.";
        }
        $stmt->close();
    } else {
        $error = "Invalid document.";
    }
    $check_stmt->close();
}

// Fetch doc details for form
$doc_details = [];
if ($doc_id > 0) {
    $sql = "SELECT doc_type FROM documents WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $doc_id, $user_id);
    $stmt->execute();
    $doc_details = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0; max-width: 500px; margin: 0 auto;">
    <h2 style="color: var(--gov-deep);">Share Document</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (empty($doc_details)): ?>
        <p>Select a document from dashboard to share.</p>
        <a href="dashboard.php">Back</a>
    <?php else: ?>
        <p>Sharing: <?php echo $doc_details['doc_type']; ?></p>
        <form method="POST" action="">
            <input type="hidden" name="doc_id" value="<?php echo $doc_id; ?>">
            <input type="email" name="shared_with" placeholder="Share with (email/institution)" required>
            <button type="submit" class="cta-primary" style="width: 100%; margin-top: 10px;">Generate Share Link</button>
        </form>
    <?php endif; ?>

     <a href="dashboard.php" style="display: block; margin-top: 20px; text-align: center; color: var(--gov-green);">← Back to Dashboard</a>
</div>
<?php include '../includes/footer.php'; ?>