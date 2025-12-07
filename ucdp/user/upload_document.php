<?php

require_once '../config.php';
require_once '../includes/functions.php';

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';


$upload_dir = __DIR__ . '/../../uploads/';  
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);  
    $error = "Uploads folder created. Try again."; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc_type = sanitizeInput($_POST['doc_type'] ?? '');
    $doc_number = sanitizeInput($_POST['doc_number'] ?? '');
    $expiry_date = $_POST['expiry_date'] ?? null;

    if (empty($doc_type)) {
        $error = "Document type required!";
    } elseif (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['document'];
        $allowed_types = ['application/pdf'];
        $max_size = 5 * 1024 * 1024;  // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            $error = "Only PDF files allowed! Uploaded type: " . $file['type'];
        } elseif ($file['size'] > $max_size) {
            $error = "File too large! Max 5MB. Size: " . round($file['size'] / 1024 / 1024, 2) . " MB";
        } else {
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($file_ext !== 'pdf') {
                $error = "Invalid file extension! Only PDF.";
            } else {
                $file_name = $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $upload_dir . $file_name;  // Absolute target path
                
                
                
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    
                    $relative_path = 'uploads/' . $file_name;
                    $sql = "INSERT INTO documents (user_id, doc_type, file_path, doc_number, expiry_date) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issss", $user_id, $doc_type, $relative_path, $doc_number, $expiry_date);
                    if ($stmt->execute()) {
                        $doc_id = $conn->insert_id;
                        logAudit('upload_document', $user_id, "Uploaded $doc_type (ID: $doc_id, Path: $relative_path)");
                        $success = "Document uploaded successfully! File saved at: $target_path. Awaiting admin verification. <br><a href='../$relative_path' target='_blank'>View Uploaded File</a>";
                    } else {
                        $error = "DB insert failed: " . $conn->error;
                        unlink($target_path);  
                    }
                    $stmt->close();
                } else {
                    $error = "File move failed! Check permission on $target_path. Tmp: " . $file['tmp_name'];
                }
            }
        }
    } else {
        $upload_error_codes = [
            UPLOAD_ERR_INI_SIZE => 'File too large (php.ini: upload_max_filesize)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (form max)',
            UPLOAD_ERR_PARTIAL => 'Partial upload (connection issue)',
            UPLOAD_ERR_NO_FILE => 'No file selected',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temp directory',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk (permission)',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $error_code = $_FILES['document']['error'] ?? 0;
        $error = $upload_error_codes[$error_code] ?? "Unknown upload error ($error_code). Check php.ini.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0; max-width: 500px; margin: 0 auto;">
    <h2 style="color: var(--gov-deep);">Upload Document</h2>
    <?php if ($error): ?>
        <p style="color: red; background: #ffebee; padding: 10px; border-radius: 5px; white-space: pre-wrap;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green; background: #e8f5e8; padding: 10px; border-radius: 5px;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form id="uploadForm" method="POST" action="" enctype="multipart/form-data">
        <select name="doc_type" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
            <option value="">Select Type</option>
            <option value="NID">NID</option>
            <option value="Passport">Passport</option>
            <option value="Driving_License">Driving License</option>
            <option value="Birth_Certificate">Birth Certificate</option>
            <option value="Education_Certificate">Education Certificate</option>
            <option value="Vehicle_Registration">Vehicle Registration</option>
        </select>
        <input type="text" name="doc_number" placeholder="Document Number (optional)" style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <input type="date" name="expiry_date" style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <input type="file" name="document" id="document" accept=".pdf" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
        <div id="preview" style="margin-bottom: 10px; padding: 10px; background: #f0f0f0; border-radius: 5px; min-height: 20px;"></div>
        <button type="submit" class="cta-primary" style="width: 100%; padding: 12px; border-radius: 5px; font-size: 16px;">Upload PDF</button>
    </form>
    <a href="dashboard.php" style="display: block; margin-top: 20px; text-align: center; color: var(--gov-green);">← Back to Dashboard</a>
</div>
<script>
    // Fixed JS: Preview + Always Allow Submit
    function previewFile(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) {
            console.error('Preview elements not found');  
            return;
        }

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            preview.innerHTML = '';  // Clear previous
            if (file) {
                if (file.type !== 'application/pdf') {
                    preview.innerHTML = '<p style="color: red;">Only PDF allowed! Please select a PDF file.</p>';
                    input.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    preview.innerHTML = '<p style="color: red;">File too large (max 5MB)!</p>';
                    input.value = '';
                    return;
                }
                preview.innerHTML = `<p style="color: green;">✅ Selected: <strong>${file.name}</strong> (${(file.size / 1024).toFixed(0)} KB) - Ready!</p>`;
            }
        });
    }

    function validateForm(formId) {
        const form = document.getElementById(formId);
        if (!form) return true;
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
            alert('Please fill all required fields (marked red)!');
        }
        return true;  // Always return true to allow submit
    }

    
    document.addEventListener('DOMContentLoaded', function() {
        previewFile('document', 'preview');
        const form = document.getElementById('uploadForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submitted!');  
                validateForm('uploadForm');  
                
            });
        }
    });
</script>
<?php include '../includes/footer.php'; ?>