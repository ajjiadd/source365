<?php
// User Registration Page
require_once '../config.php';
require_once '../includes/functions.php';

$success = '';
$error = '';
$show_otp = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step']) && $_POST['step'] === 'register') {
        // Step 1: Basic registration
        $nid = sanitizeInput($_POST['nid']);
        $full_name = sanitizeInput($_POST['full_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Secure hash

        // Check if NID exists
        $check_sql = "SELECT id FROM users WHERE nid = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $nid);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = "NID already registered!";
        } else {
            // Insert user (unverified)
            $sql = "INSERT INTO users (nid, full_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nid, $full_name, $email, $phone, $password);
            if ($stmt->execute()) {
                $user_id = $conn->insert_id;
                // Generate OTP
                $otp = generateOTP();
                $update_sql = "UPDATE users SET otp_code = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $otp, $user_id);
                $update_stmt->execute();
                // Simulate send OTP (in real: SMS API) - Store in session for resend
                $_SESSION['temp_user_id'] = $user_id;
                $_SESSION['otp_sent'] = $otp;  // Temp store for resend simulation
                $show_otp = true;
                $success = "Registration successful! Enter OTP sent to $phone. (Demo OTP: $otp)";  // Show for testing, remove in prod
            } else {
                $error = "Registration failed. Try again.";
            }
            $stmt->close();
        }
        $check_stmt->close();
    } elseif (isset($_POST['step']) && $_POST['step'] === 'verify_otp') {
        // Step 2: Verify OTP
        $otp_entered = sanitizeInput($_POST['otp']);
        $user_id = $_SESSION['temp_user_id'] ?? 0;
        if ($user_id > 0) {
            $sql = "SELECT otp_code FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if ($user && $otp_entered === $user['otp_code']) {  // Direct match
                // Verify user
                $verify_sql = "UPDATE users SET is_verified = 1, otp_code = NULL WHERE id = ?";
                $verify_stmt = $conn->prepare($verify_sql);
                $verify_stmt->bind_param("i", $user_id);
                $verify_stmt->execute();
                unset($_SESSION['temp_user_id']);
                unset($_SESSION['otp_sent']);
                logAudit('register_user', $user_id, "User registered and verified");
                $success = "OTP verified! You can now login.";
                $show_otp = false;
            } else {
                $error = "Invalid OTP! Try again or resend.";
            }
            $stmt->close();
        } else {
            $error = "Session expired. Register again.";
            $show_otp = false;
        }
    } elseif (isset($_POST['step']) && $_POST['step'] === 'resend_otp') {
        // Resend OTP simulation
        $user_id = $_SESSION['temp_user_id'] ?? 0;
        if ($user_id > 0) {
            $otp = generateOTP();
            $update_sql = "UPDATE users SET otp_code = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $otp, $user_id);
            $update_stmt->execute();
            $_SESSION['otp_sent'] = $otp;
            $success = "OTP resent! (Demo: $otp)";
        } else {
            $error = "Cannot resend. Register again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0; max-width: 500px; margin: 0 auto;">
    <h2 style="text-align: center; color: var(--gov-deep);">Register / রেজিস্টার</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!$show_otp): ?>
        <!-- Registration Form -->
        <form id="registerForm" method="POST" action="">
            <input type="hidden" name="step" value="register">
            <input type="text" name="nid" placeholder="NID (17 digits)" required maxlength="17">
            <input type="text" name="full_name" placeholder="Full Name / নাম" required>
            <input type="email" name="email" placeholder="Email (optional)">
            <input type="tel" name="phone" placeholder="Phone (+880...)" required>
            <input type="password" name="password" placeholder="Password" required minlength="6">
            <button type="submit" class="cta-primary" style="width: 100%; margin-top: 10px;">Register</button>
        </form>
    <?php else: ?>
        <!-- OTP Form -->
        <form id="otpForm" method="POST" action="">
            <input type="hidden" name="step" value="verify_otp">
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6">
            <button type="submit" class="cta-primary" style="width: 100%; margin-top: 10px;">Verify OTP</button>
        </form>
        <button type="button" onclick="resendOTP()" class="cta-secondary" style="width: 100%; margin-top: 5px;">Resend OTP</button>
        <form id="resendForm" method="POST" action="" style="display: none;">
            <input type="hidden" name="step" value="resend_otp">
        </form>
    <?php endif; ?>
    <p style="text-align: center; margin-top: 10px;"><a href="login.php">Already registered? Login here</a></p>
</div>
<script>
    // JS for this page - Make validation optional if JS fails
    function validateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return true;  // If no form, allow submit
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        let valid = true;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.style.borderColor = 'red';
            } else {
                input.style.borderColor = '';
            }
        });
        return valid;
    }

    // Register form validation
    if (document.getElementById('registerForm')) {
        document.getElementById('registerForm').onsubmit = function(e) {
            if (!validateForm('registerForm')) {
                e.preventDefault();  // Block if invalid
            }
        };
    }

    // OTP form validation
    if (document.getElementById('otpForm')) {
        document.getElementById('otpForm').onsubmit = function(e) {
            if (!validateForm('otpForm')) {
                e.preventDefault();
            }
        };
    }

    // Resend OTP function
    function resendOTP() {
        document.getElementById('resendForm').submit();
    }

    // OTP timer simulation (optional)
    function startOTPTimer(buttonId) {
        const button = document.getElementById(buttonId);
        if (!button) return;
        let timeLeft = 60;
        button.disabled = true;
        const originalText = button.textContent;
        const timer = setInterval(() => {
            timeLeft--;
            button.textContent = `Resend (${timeLeft}s)`;
            if (timeLeft <= 0) {
                clearInterval(timer);
                button.disabled = false;
                button.textContent = originalText;
            }
        }, 1000);
    }
    // Start timer on load for resend button if exists
    if (document.querySelector('.cta-secondary')) {
        startOTPTimer('resendBtn');  // Add id="resendBtn" to button if needed
    }
</script>
<?php include '../includes/footer.php'; ?>