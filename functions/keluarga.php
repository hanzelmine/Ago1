<?php
require_once 'database.php';

function isKodeKKExists($kode_kk, $exclude_id = null)
{
    global $conn;
    $kode_kk = mysqli_real_escape_string($conn, trim($kode_kk));

    $query = "SELECT id_keluarga FROM keluarga WHERE kode_kk = '$kode_kk'";
    if ($exclude_id !== null) {
        $exclude_id = (int)$exclude_id;
        $query .= " AND id_keluarga != $exclude_id";
    }

    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

function insertKeluarga($data)
{
    global $conn;

    $total = count($data['kode_kk']);
    $results = [];

    for ($i = 0; $i < $total; $i++) {
        $fields = [];

        foreach ($data as $key => $value) {
            $val = is_array($value) ? ($value[$i] ?? '') : $value;
            $cleanValue = trim($val);
            $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
        }

        // Cek duplikat kode_kk
        $check = mysqli_query($conn, "SELECT 1 FROM keluarga 
            WHERE kode_kk = '{$fields['kode_kk']}' 
            LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $results[] = 'duplicate_kode_kk';
            continue;
        }

        $query = "INSERT INTO keluarga (
            kode_kk, nama_keluarga, alamat, id_rayon, tempat_tinggal, created_at
        ) VALUES (
            '{$fields['kode_kk']}', '{$fields['nama_keluarga']}', '{$fields['alamat']}',
            '{$fields['id_rayon']}', '{$fields['tempat_tinggal']}', NOW()
        )";

        $result = mysqli_query($conn, $query);

        if ($result) {
            $results[] = true;
        } else {
            error_log("Gagal insert keluarga: " . mysqli_error($conn));
            $results[] = false;
        }
    }

    return $results;
}



function updateKeluarga($id, $data)
{
    global $conn;
    $id = (int)$id;
    $fields = [];

    foreach ($data as $key => $value) {
        if (is_array($value)) continue; // Lewati jika array
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    // Cek duplikat kode_kk
    $check = mysqli_query($conn, "SELECT 1 FROM keluarga 
            WHERE kode_kk = '{$fields['kode_kk']}' 
            LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        $results[] = 'duplicate_kode_kk';
    }

    $query = "UPDATE keluarga SET 
        kode_kk = '{$fields['kode_kk']}',
        nama_keluarga = '{$fields['nama_keluarga']}',
        alamat = '{$fields['alamat']}',
        id_rayon = '{$fields['id_rayon']}',
        tempat_tinggal = '{$fields['tempat_tinggal']}',
        updated_at = NOW()
        WHERE id_keluarga = $id";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        error_log("Gagal update keluarga ID $id: " . mysqli_error($conn));
    }

    return $result ? true : false;
}




function deleteKeluarga($data)
{
    global $conn;
    $id = (int)$data['id_keluarga'];
    return mysqli_query($conn, "DELETE FROM keluarga WHERE id_keluarga = $id");
}
