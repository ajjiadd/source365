<?php
// Admin Logout
require_once '../config.php';
require_once '../includes/functions.php';

logAudit('admin_logout', $_SESSION['admin_id'] ?? null, "Admin logged out");
session_destroy();
header('Location: ../index.php');
exit();
?>
