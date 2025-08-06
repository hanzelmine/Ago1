<?php
// File: functions/atestasi.php
require_once 'database.php';

function insertAtestasi($data)
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

        // Cek duplikat id_jemaat
        $check = mysqli_query($conn, "SELECT 1 FROM atestasi 
            WHERE id_jemaat = '{$fields['id_jemaat']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $results[] = 'duplicate';
            continue;
        }

        $query = "INSERT INTO atestasi (
            id_jemaat, jenis_atestasi, gereja_asal_tujuan, keterangan, created_at
        ) VALUES (
            '{$fields['id_jemaat']}', '{$fields['jenis_atestasi']}', '{$fields['gereja_asal_tujuan']}',
            '{$fields['keterangan']}', NOW()
        )";

        $result = mysqli_query($conn, $query);

        if ($result) {
            // Set status jemaat berdasarkan jenis atestasi
            $status = ($fields['jenis_atestasi'] === 'Masuk') ? 'Aktif' : 'Pindah';
            mysqli_query($conn, "UPDATE jemaat SET status_jemaat = '$status' 
                WHERE id_jemaat = '{$fields['id_jemaat']}'");
            $results[] = true;
        } else {
            error_log("Gagal insert atestasi: " . mysqli_error($conn));
            $results[] = false;
        }
    }

    return $results;
}
function updateAtestasi($id, $data)
{
    global $conn;
    $id = (int) $id;

    // Ambil data lama
    $old = mysqli_query($conn, "SELECT id_jemaat FROM atestasi WHERE id_atestasi = $id LIMIT 1");
    $oldData = mysqli_fetch_assoc($old);
    $old_id_jemaat = $oldData['id_jemaat'] ?? null;

    $fields = [];
    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat id_jemaat selain baris ini
    $check = mysqli_query($conn, "SELECT 1 FROM atestasi 
        WHERE id_jemaat = '{$fields['id_jemaat']}' 
        AND id_atestasi != $id 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        return 'duplicate';
    }

    $query = "UPDATE atestasi SET 
        id_jemaat = '{$fields['id_jemaat']}',
        jenis_atestasi = '{$fields['jenis_atestasi']}',
        gereja_asal_tujuan = '{$fields['gereja_asal_tujuan']}',
        keterangan = '{$fields['keterangan']}',
        updated_at = NOW()
        WHERE id_atestasi = $id";

    $result = mysqli_query($conn, $query);

    if ($result) {
        // Jika id_jemaat berubah, kembalikan jemaat lama ke status 'Aktif'
        if ($old_id_jemaat && $old_id_jemaat !== $fields['id_jemaat']) {
            mysqli_query($conn, "UPDATE jemaat SET status_jemaat = 'Aktif' 
                WHERE id_jemaat = '$old_id_jemaat'");
        }

        // Set status jemaat baru berdasarkan jenis atestasi
        $status = ($fields['jenis_atestasi'] === 'Masuk') ? 'Aktif' : 'Pindah';
        mysqli_query($conn, "UPDATE jemaat SET status_jemaat = '$status' 
            WHERE id_jemaat = '{$fields['id_jemaat']}'");
    } else {
        error_log("Gagal update atestasi ID $id: " . mysqli_error($conn));
    }

    return $result;
}


function deleteAtestasi($id)
{
    global $conn;
    $id = (int) $id;

    // Ambil id_jemaat dulu
    $resultJemaat = mysqli_query($conn, "SELECT id_jemaat FROM atestasi WHERE id_atestasi = $id LIMIT 1");
    $row = mysqli_fetch_assoc($resultJemaat);
    $id_jemaat = $row['id_jemaat'] ?? null;

    $query = "DELETE FROM atestasi WHERE id_atestasi = $id";
    $result = mysqli_query($conn, $query);

    if ($result && $id_jemaat) {
        // Set status jemaat = 'Aktif'
        mysqli_query($conn, "UPDATE jemaat SET status_jemaat = 'Aktif' 
            WHERE id_jemaat = '$id_jemaat'");
    } elseif (!$result) {
        error_log("Gagal delete atestasi ID $id: " . mysqli_error($conn));
    }

    return $result;
}
