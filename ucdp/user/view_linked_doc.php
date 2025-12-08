<?php
// View Linked Govt Doc - Simulate PDF from Mock Data
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../lib/fpdf/fpdf.php';

if (!isUserLoggedIn()) {
    header('Location: login.php');
    exit();
}

$doc_type = $_GET['type'] ?? '';
$nid = $_GET['nid'] ?? '';  // From session or GET

if (empty($doc_type) || empty($nid)) {
    die("Invalid doc type or NID.");
}

// Fetch mock data
$sql = "SELECT fake_data FROM mock_govt_data WHERE nid = ? AND doc_type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nid, $doc_type);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $data = json_decode($row['fake_data'], true);
} else {
    die("No linked data found.");
}
$stmt->close();

// Generate PDF with data
class LinkedDocPDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'UCDP Linked Govt Document', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Auto-Linked from Govt DB - © UCDP', 0, 0, 'C');
    }
}

$pdf = new LinkedDocPDF();
$pdf->AddPage('P', 'A4');
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, 'Document Type: ' . $doc_type, 0, 1);
$pdf->Ln(5);
foreach ($data as $key => $value) {
    $pdf->Cell(0, 10, strtoupper($key) . ': ' . $value, 0, 1);
}
$pdf->Ln(10);

// Watermark
$pdf->SetFont('Arial', 'I', 30);
$pdf->SetTextColor(200, 200, 200);
$pdf->Cell(0, 50, 'LINKED FROM GOVT', 0, 1, 'C');

$filename = $doc_type . '_linked_' . date('Y-m-d') . '.pdf';
$pdf->Output('D', $filename);

logAudit('view_linked_doc', $_SESSION['user_id'], "Viewed linked $doc_type");
?>