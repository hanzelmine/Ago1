<?php
$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'gtm';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

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
