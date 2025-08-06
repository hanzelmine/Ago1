<?php
// File: functions/meninggal.php
require_once 'database.php';

function insertMeninggal($data)
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

        // Cek duplikat untuk id_jemaat
        $check = mysqli_query($conn, "SELECT 1 FROM meninggal 
            WHERE id_jemaat = '{$fields['id_jemaat']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $results[] = 'duplicate';
            continue;
        }

        $query = "INSERT INTO meninggal (
            id_jemaat, tanggal_meninggal, tempat_meninggal, sebab_meninggal, 
            keterangan, created_at
        ) VALUES (
            '{$fields['id_jemaat']}', '{$fields['tanggal_meninggal']}', '{$fields['tempat_meninggal']}',
            '{$fields['sebab_meninggal']}', '{$fields['keterangan']}', NOW()
        )";

        $result = mysqli_query($conn, $query);

        if ($result) {
            // Set status_jemaat = 'Meninggal'
            mysqli_query($conn, "UPDATE jemaat SET status_jemaat = 'Meninggal' 
                WHERE id_jemaat = '{$fields['id_jemaat']}'");
            $results[] = true;
        } else {
            error_log("Gagal insert meninggal: " . mysqli_error($conn));
            $results[] = false;
        }
    }

    return $results;
}

function updateMeninggal($id, $data)
{
    global $conn;
    $id = (int) $id;

    // Ambil data lama
    $old = mysqli_query($conn, "SELECT id_jemaat FROM meninggal WHERE id_meninggal = $id LIMIT 1");
    $oldData = mysqli_fetch_assoc($old);
    $old_id_jemaat = $oldData['id_jemaat'] ?? null;

    $fields = [];
    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat untuk id_jemaat selain baris ini
    $check = mysqli_query($conn, "SELECT 1 FROM meninggal 
        WHERE id_jemaat = '{$fields['id_jemaat']}' 
        AND id_meninggal != $id 
        LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        return 'duplicate';
    }

    $query = "UPDATE meninggal SET 
        id_jemaat = '{$fields['id_jemaat']}',
        tanggal_meninggal = '{$fields['tanggal_meninggal']}',
        tempat_meninggal = '{$fields['tempat_meninggal']}',
        sebab_meninggal = '{$fields['sebab_meninggal']}',
        keterangan = '{$fields['keterangan']}',
        updated_at = NOW()
        WHERE id_meninggal = $id";

    $result = mysqli_query($conn, $query);

    if ($result) {
        // Jika id_jemaat berubah, update status jemaat
        if ($old_id_jemaat && $old_id_jemaat !== $fields['id_jemaat']) {
            // Set jemaat lama kembali Aktif
            mysqli_query($conn, "UPDATE jemaat SET status_jemaat = 'Aktif' 
                WHERE id_jemaat = '$old_id_jemaat'");
        }

        // Set jemaat baru menjadi Meninggal
        mysqli_query($conn, "UPDATE jemaat SET status_jemaat = 'Meninggal' 
            WHERE id_jemaat = '{$fields['id_jemaat']}'");
    } else {
        error_log("Gagal update meninggal ID $id: " . mysqli_error($conn));
    }

    return $result;
}


function deleteMeninggal($id)
{
    global $conn;
    $id = (int) $id;

    // Ambil id_jemaat dulu
    $resultJemaat = mysqli_query($conn, "SELECT id_jemaat FROM meninggal WHERE id_meninggal = $id LIMIT 1");
    $row = mysqli_fetch_assoc($resultJemaat);
    $id_jemaat = $row['id_jemaat'] ?? null;

    $query = "DELETE FROM meninggal WHERE id_meninggal = $id";
    $result = mysqli_query($conn, $query);

    if ($result && $id_jemaat) {
        // Set status jemaat = 'Aktif'
        mysqli_query($conn, "UPDATE jemaat SET status_jemaat = 'Aktif' 
            WHERE id_jemaat = '$id_jemaat'");
    } elseif (!$result) {
        error_log("Gagal delete meninggal ID $id: " . mysqli_error($conn));
    }

    return $result;
}
