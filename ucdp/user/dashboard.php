<?php
// User Dashboard - Updated with Auto-Linked Govt Docs Section
require_once '../config.php';
require_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch uploaded documents
$sql = "SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Auto-linked docs from session (fetched on login)
$linked_docs = $_SESSION['linked_docs'] ?? [];  // Array from mock API
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0;">
    <h2 style="color: var(--gov-deep);">Dashboard - Welcome, User ID: <?php echo $user_id; ?></h2>
    
    <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="upload_document.php" class="cta-primary">Upload Document</a>
        <a href="view_documents.php" class="cta-secondary">View Uploaded</a>
        <a href="share_document.php" class="cta-secondary">Share Document</a>
        <a href="family_link.php" class="cta-secondary">Family Link</a>
        <a href="../logout.php">Logout</a>
    </div>

    <!-- New Section: Auto-Linked Govt Documents -->
    <h3 style="color: var(--gov-deep); margin-top: 30px;">Auto-Linked Govt Documents (From NID Match)</h3>
    <p class="small" style="color: var(--muted);">Documents fetched from govt databases (e.g., EC, BRTA). View details below.</p>
    <?php if (!empty($linked_docs)): ?>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background: var(--card);">
        <tr style="background: var(--gov-green); color: white;">
            <th style="padding: 10px; border: 1px solid #ddd;">Type</th>
            <th>Key Details</th>
            <th>Source</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($linked_docs as $doc): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;"><?php echo $doc['type']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <?php 
                $data = $doc['data'];
                echo "<ul style='margin: 0; padding-left: 20px;'>";
                foreach ($data as $key => $value) {
                    echo "<li><strong>$key:</strong> $value</li>";
                }
                echo "</ul>";
                ?>
            </td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $doc['source']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <a href="view_linked_doc.php?type=<?php echo $doc['type']; ?>&nid=<?php echo $_SESSION['nid'] ?? '12345678901234567'; ?>">View PDF</a>  <!-- Simulate view -->
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p style="color: var(--muted); padding: 20px; background: #f0f0f0; border-radius: 5px;">No govt-linked documents found for your NID. Upload manually or check later.</p>
    <?php endif; ?>

    <!-- Existing Uploaded Section -->
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
        <tr><td colspan="4" style="padding: 10px; text-align: center;">No uploaded documents yet.</td></tr>
        <?php endif; ?>
    </table>
</div>
<?php include '../includes/footer.php'; ?>