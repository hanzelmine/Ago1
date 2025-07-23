<?php
require_once 'database.php';

function isNamaRayonExists($nama_rayon, $exclude_id = null)
{
    global $conn;
    $nama_rayon = mysqli_real_escape_string($conn, trim($nama_rayon));

    $query = "SELECT COUNT(*) as total FROM rayon WHERE nama_rayon = '$nama_rayon'";
    if ($exclude_id !== null) {
        $query .= " AND id_rayon != " . (int)$exclude_id;
    }

    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] > 0;
}

function insertRayon($data)
{
    global $conn;
    $fields = [];

    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat
    if (isNamaRayonExists($fields['nama_rayon'])) {
        return 'duplicate_nama_rayon';
    }

    $query = "INSERT INTO rayon (nama_rayon, keterangan, created_at) VALUES (
        '{$fields['nama_rayon']}', 
        '{$fields['keterangan']}', 
        NOW()
    )";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal insert rayon: " . mysqli_error($conn));
    }

    return $result ? true : false;
}

function updateRayon($id, $data)
{
    global $conn;
    $id = (int)$id;
    $fields = [];

    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat selain ID ini
    if (isNamaRayonExists($fields['nama_rayon'], $id)) {
        return 'duplicate_nama_rayon';
    }

    $query = "UPDATE rayon SET 
        nama_rayon = '{$fields['nama_rayon']}', 
        keterangan = '{$fields['keterangan']}', 
        updated_at = NOW() 
        WHERE id_rayon = $id";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal update rayon ID $id: " . mysqli_error($conn));
    }

    return $result ? true : false;
}

function deleteRayon($data)
{
    global $conn;

    $id = (int) $data['id_rayon'];
    $query = "DELETE FROM rayon WHERE id_rayon = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal delete rayon ID $id: " . mysqli_error($conn));
    }

    return $result;
}
