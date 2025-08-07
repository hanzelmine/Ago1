<?php
require_once 'database.php';
function register($nama_user, $username, $password, $role, $gambar)
{
    global $conn;

    $nama_user = mysqli_real_escape_string($conn, trim($nama_user));
    $username  = mysqli_real_escape_string($conn, trim($username));
    $role      = mysqli_real_escape_string($conn, trim($role));
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username sudah dipakai
    $cek = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        return 'exists';
    }

    // Handle gambar
    $gambar_name = 'upload/default.png';
    if ($gambar && $gambar['error'] === 0) {
        $upload_dir = 'upload/users/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext = pathinfo($gambar['name'], PATHINFO_EXTENSION);
        $gambar_name = uniqid('user_') . '.' . $ext;
        move_uploaded_file($gambar['tmp_name'], $upload_dir . $gambar_name);
    }

    // INSERT dengan created_at dan updated_at
    $query = "INSERT INTO user (nama, username, password, role, gambar, created_at, updated_at)
              VALUES ('$nama_user', '$username', '$password_hash', '$role', '$gambar_name', NOW(), NOW())";

    mysqli_query($conn, $query) or die(mysqli_error($conn));
    return true;
}

function login($username, $password)
{
    global $conn;

    $username = mysqli_real_escape_string($conn, trim($username));

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id_user'       => $user['id_user'],
                'nama'     => $user['nama'],
                'username' => $user['username'],
                'role'     => $user['role'],
                'gambar'   => $user['gambar'],
                'created_at'   => $user['created_at']
            ];

            return true;
        }
    }

    return false;
}

function logout()
{
    session_start();
    session_unset();
    session_destroy();

    session_start(); // untuk alert berikutnya
    require_once 'helpers.php';
    set_alert('info', 'Logout', 'Anda telah logout.', [
        'timer' => 1000,
        'redirect' => 'login.php',
        'toast' => true,
        'position' => 'top-end'
    ]);
    header("Location: login.php");
    exit;
}

function update_profile()
{
    global $conn;

    if (!isset($_SESSION['user']['id_user'])) {
        return 'unauthorized';
    }

    $id_user  = $_SESSION['user']['id_user'];
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $gambar   = $_FILES['gambar'] ?? null;

    if (empty($nama) || empty($username)) {
        return 'error';
    }

    // Cek apakah username sudah digunakan user lain
    $cek = mysqli_query($conn, "SELECT id_user FROM user WHERE username = '$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($cek) > 0) {
        return 'exists';
    }

    // Ambil nama gambar lama dari DB, bukan session
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM user WHERE id_user = '$id_user'"));
    $gambar_lama = $user['gambar'] ?? 'default.png';

    $upload_dir = 'upload/users/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $gambar_name = $gambar_lama; // Default: gambar lama tetap

    if ($gambar && $gambar['error'] === 0) {
        $ext       = pathinfo($gambar['name'], PATHINFO_EXTENSION);
        $new_name  = uniqid('user_') . '.' . strtolower($ext);
        $gambar_path = $upload_dir . $new_name;

        if (move_uploaded_file($gambar['tmp_name'], $gambar_path)) {
            // Hapus gambar lama jika bukan default
            if ($gambar_lama !== 'default.png' && file_exists($upload_dir . $gambar_lama)) {
                unlink($upload_dir . $gambar_lama);
            }

            $gambar_name = $new_name;
        } else {
            return 'fail';
        }
    }

    // Update DB
    $query = empty($password)
        ? "UPDATE user SET nama='$nama', username='$username', gambar='$gambar_name', updated_at=NOW() WHERE id_user='$id_user'"
        : "UPDATE user SET nama='$nama', username='$username', password='" . password_hash($password, PASSWORD_DEFAULT) . "', gambar='$gambar_name', updated_at=NOW() WHERE id_user='$id_user'";

    $update = mysqli_query($conn, $query);

    if ($update) {
        // Get the fresh full user data from DB
        $result = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id_user'");
        if ($result && $newUser = mysqli_fetch_assoc($result)) {
            $_SESSION['user'] = $newUser;
        }

        return true;
    }


    return 'fail';
}
