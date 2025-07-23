<?php
require_once 'database.php';

/**
 * set_alert()
 * Menyimpan konfigurasi SweetAlert ke session agar bisa ditampilkan di halaman berikutnya.
 *
 * @param string $type    - Jenis alert: success, error, warning, info, question
 * @param string $title   - Judul alert
 * @param string $message - Pesan alert
 * @param array  $options - Opsi tambahan: timer, redirect, position, toast, showCloseButton
 *
 * Contoh:
 * set_alert('success', 'Berhasil', 'Data berhasil disimpan');
 * set_alert('error', 'Gagal', 'Username sudah digunakan', ['redirect' => 'index.php?page=register']);
 */
function set_alert($type = 'success', $title = '', $message = '', $options = [])
{
    $_SESSION['alert'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message,
        'options' => $options
    ];
}

/**
 * show_alert()
 * Menampilkan SweetAlert2 jika ada session alert
 *
 * Ditempatkan di file layout seperti: layout/footer.php
 *
 * Contoh penggunaan:
 * <?php show_alert(); ?>
 *
 * SweetAlert akan otomatis redirect jika 'redirect' diset di set_alert()
 */
function show_alert()
{
    if (!isset($_SESSION['alert'])) return;

    $alert = $_SESSION['alert'];
    $type = $alert['type'];
    $title = $alert['title'];
    $message = $alert['message'];
    $options = $alert['options'] ?? [];

    $timer     = $options['timer']     ?? 2000;
    $redirect  = $options['redirect']  ?? null;
    $position  = $options['position']  ?? 'center'; // contoh lain: 'top-end'
    $toast     = $options['toast']     ?? false;
    $showClose = $options['showCloseButton'] ?? false;

    echo "<script>
        Swal.fire({
            icon: '$type',
            title: `$title`,
            text: `$message`,"
        . ($toast ? "toast: true," : "") . "
            position: '$position',"
        . ($timer ? "timer: $timer, timerProgressBar: true," : "") . "
            showConfirmButton: " . ($toast ? 'false' : 'true') . ",
            showCloseButton: " . ($showClose ? 'true' : 'false') . "
        }).then(() => {
            " . ($redirect ? "window.location.href = '$redirect';" : "") . "
        });
    </script>";

    unset($_SESSION['alert']);
}


/**
 * Require user to be logged in
 */
function require_login()
{
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect if user is already logged in (for login/register pages)
 */
function block_if_logged_in()
{
    if (isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Check if current user has a specific role (no redirect)
 *
 * @param string|array $roles - Role(s) to check
 * @return bool
 */
function has_role($roles)
{
    if (!isset($_SESSION['user'])) return false;
    $userRole = $_SESSION['user']['role'] ?? null;
    return in_array($userRole, (array)$roles);
}

// Cek apakah user sedang login (true/false)
function is_logged_in()
{
    return isset($_SESSION['user']);
}
