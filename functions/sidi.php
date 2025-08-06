<?php
// File: functions/sidi.php
require_once 'database.php';

function insertSidi($data)
{
    global $conn;

    $total = count($data['id_jemaat']); // Pastikan semua input array memiliki jumlah yang sama
    $inserted = 0;

    for ($i = 0; $i < $total; $i++) {
        $fields = [];

        foreach ($data as $key => $value) {
            $input = is_array($value) ? ($value[$i] ?? '') : $value;
            $cleanValue = trim($input);
            $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
        }

        // Cek duplikat id_jemaat atau no_surat_sidi
        $check = mysqli_query($conn, "SELECT 1 FROM sidi 
            WHERE id_jemaat = '{$fields['id_jemaat']}' 
            OR no_surat_sidi = '{$fields['no_surat_sidi']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            continue; // Lewati baris ini jika duplikat
        }

        // Query insert
        $query = "INSERT INTO sidi (
            id_jemaat, tempat_sidi, tanggal_sidi, no_surat_sidi,
            pendeta, keterangan, created_at
        ) VALUES (
            '{$fields['id_jemaat']}', '{$fields['tempat_sidi']}', '{$fields['tanggal_sidi']}', 
            '{$fields['no_surat_sidi']}', '{$fields['pendeta']}', '{$fields['keterangan']}', NOW()
        )";

        $result = mysqli_query($conn, $query);

        if ($result) {
            $inserted++;
        } else {
            error_log("Gagal insert sidi: " . mysqli_error($conn));
        }
    }

    // Hasil akhir
    if ($inserted === 0) {
        return 'duplicate'; // atau bisa 'none_inserted'
    }

    return true;
}


function updateSidi($id, $data)
{
    global $conn;
    $id = (int) $id;

    $fields = [];
    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat selain baris saat ini
    $check = mysqli_query($conn, "SELECT 1 FROM sidi WHERE (id_jemaat = '{$fields['id_jemaat']}' OR no_surat_sidi = '{$fields['no_surat_sidi']}') AND id_sidi != $id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        error_log("Update gagal: id_jemaat atau no_surat_sidi sudah digunakan.");
        return 'duplicate';
    }

    $query = "UPDATE sidi SET 
        id_jemaat = '{$fields['id_jemaat']}',
        tempat_sidi = '{$fields['tempat_sidi']}',
        tanggal_sidi = '{$fields['tanggal_sidi']}',
        no_surat_sidi = '{$fields['no_surat_sidi']}',
        pendeta = '{$fields['pendeta']}',
        keterangan = '{$fields['keterangan']}',
        updated_at = NOW()
        WHERE id_sidi = $id";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal update sidi ID $id: " . mysqli_error($conn));
    }

    return $result;
}

function deleteSidi($id)
{
    global $conn;
    $id = (int) $id;

    $query = "DELETE FROM sidi WHERE id_sidi = $id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal delete sidi ID $id: " . mysqli_error($conn));
    }

    return $result;
}
