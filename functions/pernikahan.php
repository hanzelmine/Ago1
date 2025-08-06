<?php
// File: functions/pernikahan.php
require_once 'database.php';

function insertPernikahan($data)
{
    global $conn;

    $total = count($data['id_suami']);
    $results = [];

    for ($i = 0; $i < $total; $i++) {
        $fields = [];

        foreach ($data as $key => $value) {
            $val = is_array($value) ? ($value[$i] ?? '') : $value;
            $cleanValue = trim($val);
            $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
        }

        // Cek duplikat: no_surat_nikah harus unik
        $check = mysqli_query($conn, "SELECT 1 FROM pernikahan 
            WHERE no_surat_nikah = '{$fields['no_surat_nikah']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $results[] = 'duplicate';
            continue;
        }

        $query = "INSERT INTO pernikahan (
            id_suami, id_istri, tempat_nikah, tanggal_nikah, no_surat_nikah,
            pendeta, keterangan, created_at
        ) VALUES (
            '{$fields['id_suami']}', '{$fields['id_istri']}', '{$fields['tempat_nikah']}', 
            '{$fields['tanggal_nikah']}', '{$fields['no_surat_nikah']}', 
            '{$fields['pendeta']}', '{$fields['keterangan']}', NOW()
        )";

        $result = mysqli_query($conn, $query);
        $results[] = $result ? true : false;
    }

    return $results;
}

function updatePernikahan($id, $data)
{
    global $conn;
    $id = (int) $id;

    $fields = [];
    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat no_surat_nikah kecuali current id
    $check = mysqli_query($conn, "SELECT 1 FROM pernikahan 
        WHERE no_surat_nikah = '{$fields['no_surat_nikah']}' 
        AND id_pernikahan != $id 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        error_log("Update gagal: No. Surat Nikah sudah digunakan.");
        return 'duplicate';
    }

    $query = "UPDATE pernikahan SET 
        id_suami = '{$fields['id_suami']}',
        id_istri = '{$fields['id_istri']}',
        tempat_nikah = '{$fields['tempat_nikah']}',
        tanggal_nikah = '{$fields['tanggal_nikah']}',
        no_surat_nikah = '{$fields['no_surat_nikah']}',
        pendeta = '{$fields['pendeta']}',
        keterangan = '{$fields['keterangan']}',
        updated_at = NOW()
        WHERE id_pernikahan = $id";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal update pernikahan ID $id: " . mysqli_error($conn));
    }

    return $result;
}

function deletePernikahan($id)
{
    global $conn;
    $id = (int) $id;

    $query = "DELETE FROM pernikahan WHERE id_pernikahan = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal delete pernikahan ID $id: " . mysqli_error($conn));
    }

    return $result;
}
