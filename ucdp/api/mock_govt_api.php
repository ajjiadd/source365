<?php
// Mock Govt API - Extended for All Document Types
require_once '../config.php';

$nid = $_GET['nid'] ?? '';
$doc_type = $_GET['doc_type'] ?? '';  // Optional: Specific type, or all if empty

if (empty($nid)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing NID']);
    exit();
}

// Query: All types if doc_type empty, else specific
if (empty($doc_type)) {
    $sql = "SELECT doc_type, fake_data, source FROM mock_govt_data WHERE nid = ? ORDER BY doc_type";
} else {
    $sql = "SELECT doc_type, fake_data, source FROM mock_govt_data WHERE nid = ? AND doc_type = ?";
}
$stmt = $conn->prepare($sql);
if (empty($doc_type)) {
    $stmt->bind_param("s", $nid);
} else {
    $stmt->bind_param("ss", $nid, $doc_type);
}
$stmt->execute();
$result = $stmt->get_result();

$docs = [];
while ($row = $result->fetch_assoc()) {
    $docs[] = [
        'type' => $row['doc_type'],
        'data' => json_decode($row['fake_data'], true),
        'source' => $row['source']
    ];
}

header('Content-Type: application/json');
if (empty($docs)) {
    echo json_encode(['error' => 'No documents found for this NID']);
} else {
    echo json_encode($docs);  // Array of all matching docs
}
$stmt->close();
?>