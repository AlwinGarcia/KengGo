<?php
// db_connect.php
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "kenggo";
$port       = 3307;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $database, $port);
    $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
    // Log error in real app; show minimal message in dev
    error_log('DB connect error: ' . $e->getMessage());
    http_response_code(500);
    die('Database connection error.');
}