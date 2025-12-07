<?php
// Admin Dashboard - User-Friendly with Cards
require_once '../config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Stats queries
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_verified=1")->fetch_assoc()['count'];
$pending_docs = $conn->query("SELECT COUNT(*) as count FROM documents WHERE status='pending'")->fetch_assoc()['count'];
$total_logs = $conn->query("SELECT COUNT(*) as count FROM audit_logs")->fetch_assoc()['count'];
$total_shares = $conn->query("SELECT COUNT(*) as count FROM shares")->fetch_assoc()['count'];
?>

<?php include '../includes/admin_header.php'; ?>
<div style="padding: 20px 0;">
    <h2 style="color: var(--gov-deep); margin-bottom: 20px;">Admin Dashboard - Welcome, Admin ID: <?php echo $admin_id; ?></h2>
    
    <!-- User-Friendly Stats Cards - Grid Layout -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="stat-card" style="background: var(--card); padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; border-left: 5px solid var(--gov-green);">
            <h3 style="color: var(--gov-green); margin: 0; font-size: 2em;"><?php echo $total_users; ?></h3>
            <p style="color: var(--muted); margin: 5px 0 0 0;">Verified Users</p>
        </div>
        <div class="stat-card" style="background: var(--card); padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; border-left: 5px solid orange;">
            <h3 style="color: orange; margin: 0; font-size: 2em;"><?php echo $pending_docs; ?></h3>
            <p style="color: var(--muted); margin: 5px 0 0 0;">Pending Documents</p>
        </div>
        <div class="stat-card" style="background: var(--card); padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; border-left: 5px solid blue;">
            <h3 style="color: blue; margin: 0; font-size: 2em;"><?php echo $total_logs; ?></h3>
            <p style="color: var(--muted); margin: 5px 0 0 0;">Audit Logs</p>
        </div>
        <div class="stat-card" style="background: var(--card); padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; border-left: 5px solid purple;">
            <h3 style="color: purple; margin: 0; font-size: 2em;"><?php echo $total_shares; ?></h3>
            <p style="color: var(--muted); margin: 5px 0 0 0;">Shared Documents</p>
        </div>
    </div>

    <!-- Quick Actions - Buttons Row -->
    <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 30px; justify-content: center;">
        <a href="verify_documents.php" class="cta-primary" style="padding: 12px 24px; font-size: 16px; border-radius: 8px; text-decoration: none;">Verify Pending Documents</a>
        <a href="monitor_logs.php" class="cta-secondary" style="padding: 12px 24px; font-size: 16px; border-radius: 8px; text-decoration: none; border: 1px solid var(--gov-green);">View Audit Logs</a>
        <a href="../user/dashboard.php" style="padding: 12px 24px; background: #6c757d; color: white; border-radius: 8px; text-decoration: none;">Switch to User View</a>
    </div>

    <!-- Recent Activity Table - Responsive -->
    <h3 style="color: var(--gov-deep); margin-bottom: 15px;">Recent Activity (Last 10 Logs)</h3>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: var(--card); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <tr style="background: var(--gov-green); color: white;">
                <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">Action</th>
                <th>User/Admin</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
            <?php
            // Fetch recent logs
            $recent_sql = "SELECT al.action, al.description, al.created_at, u.full_name as user_name 
                           FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id 
                           ORDER BY al.created_at DESC LIMIT 10";
            $recent_logs = $conn->query($recent_sql)->fetch_all(MYSQLI_ASSOC);
            foreach ($recent_logs as $log): 
            ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px; border: 1px solid #ddd;"><?php echo htmlspecialchars($log['action']); ?></td>
                <td style="padding: 12px; border: 1px solid #ddd;"><?php echo htmlspecialchars($log['user_name'] ?? 'Admin'); ?></td>
                <td style="padding: 12px; border: 1px solid #ddd;"><?php echo htmlspecialchars($log['description']); ?></td>
                <td style="padding: 12px; border: 1px solid #ddd;"><?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recent_logs)): ?>
            <tr><td colspan="4" style="padding: 20px; text-align: center; color: var(--muted);">No recent activity.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php include '../includes/admin_footer.php'; ?>  