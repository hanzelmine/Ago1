<?php
require_once 'database.php';

function isKodeKKExists($kode_kk, $exclude_id = null)
{
    global $conn;
    $kode_kk = mysqli_real_escape_string($conn, trim($kode_kk));
    $query = "SELECT id_keluarga FROM keluarga WHERE kode_kk = '$kode_kk'";
    if ($exclude_id) {
        $exclude_id = (int)$exclude_id;
        $query .= " AND id_keluarga != $exclude_id";
    }
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

function insertKeluarga($data)
{
    global $conn;
    $fields = [];

    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    if (isKodeKKExists($fields['kode_kk'])) {
        return 'duplicate_kode_kk';
    }

    $query = "INSERT INTO keluarga (kode_kk, nama_keluarga, alamat, id_rayon, tempat_tinggal, created_at) VALUES (
        '{$fields['kode_kk']}',
        '{$fields['nama_keluarga']}',
        '{$fields['alamat']}',
        '{$fields['id_rayon']}',
        '{$fields['tempat_tinggal']}',
        NOW()
    )";

    $result = mysqli_query($conn, $query);
    return $result ? true : false;
}

function updateKeluarga($id, $data)
{
    global $conn;
    $id = (int)$id;
    $fields = [];

    foreach ($data as $key => $value) {
        $cleanValue = trim($value);
        $fields[$key] = mysqli_real_escape_string($conn, $cleanValue === '' ? '-' : $cleanValue);
    }

    if (isKodeKKExists($fields['kode_kk'], $id)) {
        return 'duplicate_kode_kk';
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
    return $result ? true : false;
}


function deleteKeluarga($data)
{
    global $conn;
    $id = (int)$data['id_keluarga'];
    return mysqli_query($conn, "DELETE FROM keluarga WHERE id_keluarga = $id");
}
