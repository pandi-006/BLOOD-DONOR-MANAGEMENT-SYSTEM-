<?php
// Database Configuration - SAMPLE FILE
// Copy this as config.php and update with your credentials

define('DB_HOST', 'your_database_host');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'your_database_name');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function format_date($date) {
    return date('d-M-Y', strtotime($date));
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
