<?php
// File: functions/jemaat.php
require_once 'database.php';

function insertJemaat($data)
{
    global $conn;

    // Pastikan semua field array memiliki panjang sama
    $jumlah = count($data['nama_lengkap']);
    $success = true;

    for ($i = 0; $i < $jumlah; $i++) {
        $fields = [];

        // Ambil & sanitasi masing-masing field pada indeks ke-i
        foreach ($data as $key => $value) {
            $clean = trim($value[$i]);
            $fields[$key] = mysqli_real_escape_string($conn, $clean === '' ? '-' : $clean);
        }

        $query = "INSERT INTO jemaat (
            id_keluarga, nama_lengkap, jenis_kelamin,
            tempat_lahir, tanggal_lahir, status_perkawinan, status_dlm_keluarga,
            status_baptis, status_sidi, pendidikan_terakhir, pekerjaan,
            status_jemaat, created_at
        ) VALUES (
            '{$fields['id_keluarga']}', '{$fields['nama_lengkap']}', '{$fields['jenis_kelamin']}',
            '{$fields['tempat_lahir']}', '{$fields['tanggal_lahir']}', '{$fields['status_perkawinan']}', '{$fields['status_dlm_keluarga']}',
            '{$fields['status_baptis']}', '{$fields['status_sidi']}', '{$fields['pendidikan_terakhir']}', '{$fields['pekerjaan']}',
            '{$fields['status_jemaat']}', NOW()
        )";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            error_log("Gagal insert jemaat ke-$i: " . mysqli_error($conn));
            $success = false;
        }
    }

    return $success;
}


function updateJemaat($id, $data)
{
    global $conn;
    $id = (int) $id;
    $fields = [];

    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    $query = "UPDATE jemaat SET 
        id_keluarga = '{$fields['id_keluarga']}',
        nama_lengkap = '{$fields['nama_lengkap']}',
        jenis_kelamin = '{$fields['jenis_kelamin']}',
        tempat_lahir = '{$fields['tempat_lahir']}',
        tanggal_lahir = '{$fields['tanggal_lahir']}',
        status_perkawinan = '{$fields['status_perkawinan']}',
        status_dlm_keluarga = '{$fields['status_dlm_keluarga']}',
        status_baptis = '{$fields['status_baptis']}',
        status_sidi = '{$fields['status_sidi']}',
        pendidikan_terakhir = '{$fields['pendidikan_terakhir']}',
        pekerjaan = '{$fields['pekerjaan']}',
        status_jemaat = '{$fields['status_jemaat']}',
        updated_at = NOW()
        WHERE id_jemaat = $id";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal update jemaat ID $id: " . mysqli_error($conn));
    }

    return $result;
}

function deleteJemaat($data)
{
    global $conn;

    $id = (int) $data['id_jemaat'];
    $query = "DELETE FROM jemaat WHERE id_jemaat = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal delete jemaat ID $id: " . mysqli_error($conn));
    }

    return $result;
}
