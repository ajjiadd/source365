<?php
// User Dashboard - Check login
require_once '../config.php';
require_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's documents
$sql = "SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0;">
    <h2 style="color: var(--gov-deep);">Dashboard - Welcome, User ID: <?php echo $user_id; ?></h2>
    
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="upload_document.php" class="cta-primary">Upload Document</a>
        <a href="view_documents.php" class="cta-secondary">View Documents</a>
        <a href="share_document.php" class="cta-secondary">Share Document</a>
        <a href="family_link.php" class="cta-secondary">Family Link</a>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- Mock Govt Data Fetch (via API) -->
    <h3>Your Linked Govt Docs (Mock)</h3>
    <?php
    // Call mock API
    $nid_sql = "SELECT nid FROM users WHERE id = ?";
    $nid_stmt = $conn->prepare($nid_sql);
    $nid_stmt->bind_param("i", $user_id);
    $nid_stmt->execute();
    $nid = $nid_stmt->get_result()->fetch_assoc()['nid'];
    $nid_stmt->close();
    
    $mock_url = "../api/mock_govt_api.php?nid=$nid&doc_type=NID";
    $mock_data = @file_get_contents($mock_url);  // Simple fetch
    if ($mock_data) {
        $mock_json = json_decode($mock_data, true);
        echo "<p>Mock NID Data: " . ($mock_json['name'] ?? 'N/A') . "</p>";
    }
    ?>

    <h3>Uploaded Documents</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr style="background: var(--gov-green); color: white;">
            <th style="padding: 10px; border: 1px solid #ddd;">Type</th>
            <th>Status</th>
            <th>Uploaded</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($documents as $doc): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $doc['doc_type']; ?></td>
            <td><?php echo $doc['status']; ?></td>
            <td><?php echo date('Y-m-d', strtotime($doc['uploaded_at'])); ?></td>
            <td><a href="view_documents.php?id=<?php echo $doc['id']; ?>">View</a> | <a href="share_document.php?id=<?php echo $doc['id']; ?>">Share</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($documents)): ?>
        <tr><td colspan="4" style="padding: 10px; text-align: center;">No documents yet. Upload one!</td></tr>
        <?php endif; ?>
    </table>
</div>
<?php include '../includes/footer.php'; ?>