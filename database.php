<?php
$host     = '127.0.0.1'; // safer and more reliable on Windows than 'localhost'
$port     = ini_get("mysqli.default_port") ?: 3306;
$username = 'root';
$password = '';
$database = 'gtm';

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection errors
if ($conn->connect_errno) {
    error_log("MySQL connection failed: " . $conn->connect_error);
    die("Koneksi database gagal. Silakan hubungi administrator.");
}

// Set charset safely
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    die("Koneksi database gagal (charset).");
}

function query($sql)
{
    global $conn;
    $result = $conn->query($sql);

    if ($result === false) {
        // Query error
        die("Query error: " . $conn->error);
    }

    // For SELECT queries
    if ($result instanceof mysqli_result) {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    // For INSERT/UPDATE/DELETE: return true/false
    return $result;
}
