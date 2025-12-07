<?php
// View and Download Documents 
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../lib/fpdf/fpdf.php';  

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$doc_id = $_GET['id'] ?? 0;
$error = '';

if ($doc_id > 0) {
    // Fetch doc
    $sql = "SELECT * FROM documents WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $doc_id, $user_id);
    $stmt->execute();
    $doc = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$doc) {
        $error = "Document not found.";
    } elseif ($doc['status'] !== 'verified') {
        $error = "Only verified documents can be downloaded.";
    } else {
        
        class PDFWithWatermark extends FPDF {
            function Header() {
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, 'UCDP Verified Document', 0, 1, 'C');
                $this->Ln(5);
            }

            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, '© UCDP - Government of Bangladesh', 0, 0, 'C');
            }

            function AddWatermark($text) {
                $this->SetFont('Arial', 'I', 50);
                $this->SetTextColor(200, 200, 200);  // Light gray
                $this->RotatedText(35, 100, $text, 45);  // Diagonal watermark
                $this->SetTextColor(0);  // Reset
            }

            function RotatedText($x, $y, $txt, $angle) {
                
                $this->Text($x, $y, $txt);
            }
        }

        $pdf = new PDFWithWatermark();
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Arial', '', 12);

        // Add doc info as text 
        $pdf->Cell(0, 10, 'Document Type: ' . $doc['doc_type'], 0, 1);
        $pdf->Cell(0, 10, 'Document Number: ' . ($doc['doc_number'] ?? 'N/A'), 0, 1);
        $pdf->Cell(0, 10, 'Expiry Date: ' . ($doc['expiry_date'] ?? 'N/A'), 0, 1);
        $pdf->Cell(0, 10, 'Status: VERIFIED', 0, 1, 'C', true);  
        $pdf->Cell(0, 10, 'Original File: ' . $doc['file_path'], 0, 1);  
        $pdf->Ln(10);

        // Add watermark text
        $pdf->AddWatermark('VERIFIED - UCDP');

        // Output as download
        $filename = $doc['doc_type'] . '_verified_' . date('Y-m-d') . '.pdf';
        $pdf->Output('D', $filename); 

        logAudit('download_doc', $user_id, "Downloaded verified " . $doc['doc_type']);
        exit();
    }
}

// List all docs 
$sql = "SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$documents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php include '../includes/header.php'; ?>
<div class="container" style="padding: 40px 0;">
    <h2 style="color: var(--gov-deep);">View Documents</h2>
    <?php if ($error): ?>
        <p style="color: red; background: #ffebee; padding: 10px; border-radius: 5px;"><?php echo $error; ?></p>
    <?php endif; ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; background: var(--card);">
        <tr style="background: var(--gov-green); color: white;">
            <th style="padding: 10px; border: 1px solid #ddd;">Type</th>
            <th>Number</th>
            <th>Status</th>
            <th>Uploaded</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($documents as $doc): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $doc['doc_type']; ?></td>
            <td><?php echo $doc['doc_number'] ?? 'N/A'; ?></td>
            <td>
                <span style="color: <?php echo $doc['status'] === 'verified' ? 'green' : 'orange'; ?>;">
                    <?php echo ucfirst($doc['status']); ?>
                </span>
            </td>
            <td><?php echo date('Y-m-d', strtotime($doc['uploaded_at'])); ?></td>
            <td>
                <?php if ($doc['status'] === 'verified'): ?>
                    <a href="?id=<?php echo $doc['id']; ?>" style="color: green; text-decoration: none;">Download Verified PDF</a>
                <?php else: ?>
                    <span style="color: gray;">Pending Admin Verification</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($documents)): ?>
        <tr><td colspan="5" style="padding: 20px; text-align: center; color: var(--muted);">No documents yet. <a href="upload_document.php">Upload one now!</a></td></tr>
        <?php endif; ?>
    </table>
    <a href="dashboard.php" style="display: block; margin-top: 20px; text-align: center; color: var(--gov-green);">← Back to Dashboard</a>
</div>
<?php include '../includes/footer.php'; ?>