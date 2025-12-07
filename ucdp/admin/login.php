<?php
// Admin Login
require_once '../config.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password_hash FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($admin = $result->fetch_assoc()) {
        // Debug: echo "Admin Hash: " . $admin['password_hash']; // Remove
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            logAudit('admin_login', $admin['id'], "Admin logged in");
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid username or password! (Debug: Check DB)";
        }
    } else {
        $error = "No admin found.";
    }
    $stmt->close();
}
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0; max-width: 400px; margin: 0 auto;">
    <h2 style="text-align: center; color: var(--gov-deep);">Admin Login</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form id="adminLogin" method="POST" action="">
        <input type="text" name="username" placeholder="Username" required value="admin1">  <!-- Pre-fill test -->
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="cta-primary" style="width: 100%; margin-top: 10px;">Login</button>
    </form>
</div>
<script>
    document.getElementById('adminLogin').onsubmit = function(e) {
        validateForm('adminLogin');  // Non-blocking
    };
</script>
<?php include '../includes/footer.php'; ?>