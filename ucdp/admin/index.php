<?php
// Admin Index - Redirect to login or dashboard
require_once '../config.php';
require_once '../includes/functions.php';

session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>
