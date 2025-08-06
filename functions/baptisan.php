<?php
// File: functions/baptisan.php
require_once 'database.php';

function insertBaptisan($data)
{
    global $conn;

    $total = count($data['id_jemaat']);
    $results = [];

    for ($i = 0; $i < $total; $i++) {
        $fields = [];

        foreach ($data as $key => $value) {
            $val = is_array($value) ? ($value[$i] ?? '') : $value;
            $cleanValue = trim($val);
            $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
        }

        // Cek duplikat untuk id_jemaat atau no_surat_baptis
        $check = mysqli_query($conn, "SELECT 1 FROM baptisan 
            WHERE id_jemaat = '{$fields['id_jemaat']}' 
               OR no_surat_baptis = '{$fields['no_surat_baptis']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $results[] = 'duplicate';
            continue;
        }

        $query = "INSERT INTO baptisan (
            id_jemaat, tempat_baptis, tanggal_baptis, no_surat_baptis,
            pendeta, keterangan, created_at
        ) VALUES (
            '{$fields['id_jemaat']}', '{$fields['tempat_baptis']}', '{$fields['tanggal_baptis']}', '{$fields['no_surat_baptis']}',
            '{$fields['pendeta']}', '{$fields['keterangan']}', NOW()
        )";

        $result = mysqli_query($conn, $query);
        $results[] = $result ? true : false;
    }

    return $results;
}


function updateBaptisan($id, $data)
{
    global $conn;
    $id = (int) $id;

    $fields = [];
    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Check for duplicate no_surat_baptis or id_jemaat, excluding current row
    $check = mysqli_query($conn, "SELECT 1 FROM baptisan WHERE (id_jemaat = '{$fields['id_jemaat']}' OR no_surat_baptis = '{$fields['no_surat_baptis']}') AND id_baptisan != $id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        error_log("Update gagal: id_jemaat atau no_surat_baptis sudah digunakan.");
        return 'duplicate';
    }

    $query = "UPDATE baptisan SET 
        id_jemaat = '{$fields['id_jemaat']}',
        tempat_baptis = '{$fields['tempat_baptis']}',
        tanggal_baptis = '{$fields['tanggal_baptis']}',
        no_surat_baptis = '{$fields['no_surat_baptis']}',
        pendeta = '{$fields['pendeta']}',
        keterangan = '{$fields['keterangan']}',
        updated_at = NOW()
        WHERE id_baptisan = $id";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal update baptisan ID $id: " . mysqli_error($conn));
    }

    return $result;
}


function deleteBaptisan($id)
{
    global $conn;
    $id = (int) $id;

    $query = "DELETE FROM baptisan WHERE id_baptisan = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal delete baptisan ID $id: " . mysqli_error($conn));
    }

    return $result;
}
