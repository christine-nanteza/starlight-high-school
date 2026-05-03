<?php
// ============================================
// STARLIGHT HIGH SCHOOL — DATABASE CONFIG
// ============================================
// Change these values to match your server.
// On XAMPP/WAMP: host=localhost, user=root, pass=""
// On a live server: use the credentials from your hosting panel
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'starlight_school');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>
