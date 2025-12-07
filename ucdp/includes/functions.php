<?php

require_once __DIR__ . '/../config.php';  


function logAudit($action, $user_id = null, $description = '') {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $sql = "INSERT INTO audit_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $action, $description, $ip);
    $stmt->execute();
    $stmt->close();
}


function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}


function generateShareToken($doc_id, $user_id) {
    return md5($doc_id . $user_id . time());  // Basic, secure enough for demo
}

// Function: Sanitize input 
function sanitizeInput($data) {
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
}


function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>