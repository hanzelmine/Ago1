<?php
require_once 'database.php';

function insertRayon($data)
{
    global $conn;

    $total = count($data['nama_rayon']);
    $results = [];

    for ($i = 0; $i < $total; $i++) {
        $fields = [];

        foreach ($data as $key => $value) {
            $val = is_array($value) ? ($value[$i] ?? '') : $value;
            $cleanValue = trim($val);
            $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
        }

        // Cek duplikat nama_rayon
        $check = mysqli_query($conn, "SELECT 1 FROM rayon 
            WHERE nama_rayon = '{$fields['nama_rayon']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $results[] = 'duplicate';
            continue;
        }

        $query = "INSERT INTO rayon (nama_rayon, keterangan, created_at) VALUES (
            '{$fields['nama_rayon']}', 
            '{$fields['keterangan']}', 
            NOW()
        )";

        $result = mysqli_query($conn, $query);

        if ($result) {
            $results[] = true;
        } else {
            error_log("Gagal insert rayon: " . mysqli_error($conn));
            $results[] = false;
        }
    }

    return $results;
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

    // Cek duplikat nama_rayon, tapi abaikan record dengan ID yang sedang diupdate
    $check = mysqli_query($conn, "SELECT 1 FROM rayon 
        WHERE nama_rayon = '{$fields['nama_rayon']}' 
        AND id_rayon != $id 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        return 'duplicate';
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
