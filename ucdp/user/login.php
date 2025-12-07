<?php
// User Login Page
require_once '../config.php';
require_once '../includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nid = sanitizeInput($_POST['nid']);
    $password = $_POST['password'];

    $sql = "SELECT id, password_hash, is_verified FROM users WHERE nid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        
        if (password_verify($password, $user['password_hash'])) {
            if ($user['is_verified'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                logAudit('user_login', $user['id'], "User logged in");
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Account not verified. Complete OTP during registration.";
            }
        } else {
            $error = "Invalid NID or password! (Debug: Check DB hash)";
        }
    } else {
        $error = "No user found with this NID.";
    }
    $stmt->close();
}
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0; max-width: 500px; margin: 0 auto;">
    <h2 style="text-align: center; color: var(--gov-deep);">Login / লগইন</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="">
        <input type="text" name="nid" placeholder="NID" required value="12345678901234567">  <!-- Pre-fill for test -->
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="cta-primary" style="width: 100%; margin-top: 10px;">Login</button>
    </form>
    <p style="text-align: center; margin-top: 10px;"><a href="register.php">Not registered? Register here</a></p>
</div>
<script>
    
    document.getElementById('loginForm').onsubmit = function(e) {
        validateForm('loginForm');  
    };
</script>
<?php include '../includes/footer.php';?>