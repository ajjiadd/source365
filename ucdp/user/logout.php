<?php
// User Logout
require_once '../config.php';
require_once '../includes/functions.php';

logAudit('user_logout', $_SESSION['user_id'] ?? null, "User logged out");
session_destroy();
header('Location: ../index.php');
exit();
?>