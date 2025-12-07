<?php
// Family Linking - Fixed JS Form ID & Validation Block
require_once '../config.php';
require_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];  // Current user as guardian
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_nid = sanitizeInput($_POST['member_nid'] ?? '');
    $relation_type = sanitizeInput($_POST['relation_type'] ?? '');

    if (empty($member_nid) || empty($relation_type)) {
        $error = "Member NID and Relation type required!";
    } else {
        // Find member (verified user only)
        $sql = "SELECT id FROM users WHERE nid = ? AND id != ? AND is_verified = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $member_nid, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($member = $result->fetch_assoc()) {
            $member_id = $member['id'];
            // Check if already linked
            $check_sql = "SELECT id FROM family_relations WHERE guardian_user_id = ? AND member_user_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $user_id, $member_id);
            $check_stmt->execute();
            if ($check_stmt->get_result()->num_rows === 0) {
                // Insert link
                $insert_sql = "INSERT INTO family_relations (guardian_user_id, member_user_id, relation_type) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iis", $user_id, $member_id, $relation_type);
                if ($insert_stmt->execute()) {
                    logAudit('family_link', $user_id, "Linked to $member_nid as $relation_type");
                    $success = "Family member '$member_nid' linked as '$relation_type' successfully!";
                } else {
                    $error = "Link failed: " . $conn->error;  // Debug DB error
                }
                $insert_stmt->close();
            } else {
                $error = "Already linked with this member!";
            }
            $check_stmt->close();
        } else {
            $error = "Member NID '$member_nid' not found or not verified. Use a registered user.";
        }
        $stmt->close();
    }
}

// Fetch existing links (with names)
$links_sql = "SELECT u.full_name, u.nid, fr.relation_type FROM family_relations fr JOIN users u ON fr.member_user_id = u.id WHERE fr.guardian_user_id = ? AND fr.is_active = 1";
$links_stmt = $conn->prepare($links_sql);
$links_stmt->bind_param("i", $user_id);
$links_stmt->execute();
$links = $links_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$links_stmt->close();
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0; max-width: 600px; margin: 0 auto;">
    <h2 style="color: var(--gov-deep);">Family Linking</h2>
    <?php if ($error): ?>
        <p style="color: red; background: #ffebee; padding: 10px; border-radius: 5px;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green; background: #e8f5e8; padding: 10px; border-radius: 5px;"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Fixed Form with ID -->
    <form id="linkForm" method="POST" action="" enctype="multipart/form-data" onsubmit="return handleSubmit(event)">
        <input type="text" name="member_nid" placeholder="Member NID (e.g., 98765432109876543)" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <select name="relation_type" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
            <option value="">Select Relation</option>
            <option value="spouse">Spouse</option>
            <option value="child">Child</option>
            <option value="parent">Parent</option>
            <option value="guardian">Guardian</option>
            <option value="sibling">Sibling</option>
        </select>
        <button type="submit" class="cta-primary" style="width: 100%; padding: 12px; border-radius: 5px; font-size: 16px;">Link Member</button>
    </form>

    <h3 style="margin-top: 30px; color: var(--gov-deep);">Linked Members (<?php echo count($links); ?>)</h3>
    <?php if (!empty($links)): ?>
        <table style="width: 100%; border-collapse: collapse; background: var(--card); margin-top: 10px;">
            <tr style="background: var(--gov-green); color: white;">
                <th style="padding: 10px; border: 1px solid #ddd;">Member NID</th>
                <th>Full Name</th>
                <th>Relation</th>
            </tr>
            <?php foreach ($links as $link): ?>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $link['nid']; ?></td>
                <td><?php echo htmlspecialchars($link['full_name']); ?></td>
                <td style="color: var(--gov-green);"><?php echo ucfirst($link['relation_type']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="color: var(--muted);">No family members linked yet. Link one above!</p>
    <?php endif; ?>
    <a href="dashboard.php" style="display: block; margin-top: 20px; text-align: center; color: var(--gov-green);">← Back to Dashboard</a>
</div>
<script>
    // Fixed JS: Non-blocking validation + Debug Log
    function validateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) {
            console.error('Form ID not found: ' + formId);  // Debug
            return true;  // Allow submit if no form
        }
        const inputs = form.querySelectorAll('input[required], select[required]');
        let hasError = false;
        inputs.forEach(input => {
            if (!input.value.trim()) {
                hasError = true;
                input.style.borderColor = 'red';
                input.style.backgroundColor = '#ffebee';
            } else {
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        });
        if (hasError) {
            alert('Please fill all required fields (highlighted red)!');
        }
        return true;  // Always allow submit (non-blocking)
    }

    function handleSubmit(e) {
        console.log('Family link form submitted!');  // Debug in console F12
        validateForm('linkForm');  // Just highlight
        return true;  // Always return true
    }

    // Init on load
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('linkForm');
        if (form) {
            form.addEventListener('submit', handleSubmit);
        } else {
            console.error('Link form not found!');
        }
    });
</script>
<?php include '../includes/footer.php'; ?>