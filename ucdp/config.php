<?php
// Database configuration
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "ucdp";          

// Create connection using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8 for Bengali support
$conn->set_charset("utf8mb4");

// Site constants 
define('SITE_URL', 'http://localhost/UCDP/');
define('UPLOAD_PATH', 'uploads/');


error_reporting(E_ALL);
ini_set('display_errors', 1);
?>