<?php
// Admin Monitor Logs
require_once '../config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch logs (simple, no filter for beginner)
$sql = "SELECT al.*, u.full_name as user_name, ad.full_name as admin_name FROM audit_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        LEFT JOIN admins ad ON al.admin_id = ad.id 
        ORDER BY al.created_at DESC LIMIT 50";
$logs = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<?php include '../includes/admin_header.php'; ?>
<div class="container" style="padding: 40px 0;">
    <h2 style="color: var(--gov-deep);">Audit Logs (Last 50)</h2>

    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr style="background: var(--gov-green); color: white;">
            <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
            <th>User/Admin</th>
            <th>Description</th>
            <th>IP</th>
            <th>Date</th>
        </tr>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $log['action']; ?></td>
            <td><?php echo $log['user_name'] ?? $log['admin_name'] ?? 'N/A'; ?></td>
            <td><?php echo htmlspecialchars($log['description']); ?></td>
            <td><?php echo $log['ip_address']; ?></td>
            <td><?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
        <tr><td colspan="5" style="padding: 10px; text-align: center;">No logs yet.</td></tr>
        <?php endif; ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
</div>
<?php include '../includes/admin_footer.php'; ?>