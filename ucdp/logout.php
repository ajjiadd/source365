<?php
// Simple logout 
require_once 'config.php';
require_once 'includes/functions.php';  // For session

session_start();
session_unset();
session_destroy();

header('Location: index.php');
exit();
?>