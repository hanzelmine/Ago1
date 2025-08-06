<?php
ob_start();
session_start();
// LOAD FILE HELPER 
//===============================================================
require_once 'database.php';
require_once 'helpers.php';
require_login();
//===============================================================

//ROUTING PAGES
//===============================================================
$page = $_GET['page'] ?? 'dashboard'; // default halaman
$pagesDir = __DIR__ . '/pages/';
// Routing manual (alias → file path)
$routes = [
    'dashboard' => 'dashboard.php',
    'contact'   => 'contact/contact.php',
    'profile'   => 'profile/profile.php',
    'rayon'   => 'rayon/rayon.php',
    'keluarga'   => 'keluarga/keluarga.php',
    'pencarian'   => 'pencarian.php',
    'jemaat'   => 'jemaat/jemaat.php',
    'createjemaat'  => 'jemaat/createJemaat.php', // add this
    'baptisan'   => 'baptisan/baptisan.php',
    'createbaptisan'  => 'baptisan/createbaptisan.php', // add this
    'sidi'   => 'sidi/sidi.php',
    'createsidi'  => 'sidi/createsidi.php', // add this
    'pernikahan'   => 'pernikahan/pernikahan.php',
    'createpernikahan'  => 'pernikahan/createpernikahan.php', // add this
    'atestasi'   => 'atestasi/atestasi.php',
    'createatestasi'  => 'atestasi/createatestasi.php', // add this
    'meninggal'   => 'meninggal/meninggal.php',
    'createmeninggal'  => 'meninggal/createmeninggal.php', // add this
    'logout'    => 'functions/auth.php'
];
//===============================================================

//LOGOUT
//===============================================================
if ($page === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'functions/auth.php';
    logout();
    exit;
}
// Hindari path traversal (keamanan)
$page = str_replace(['..', './', '//'], '', $page);
//===============================================================

//SESSION USER
//===============================================================
$user = $_SESSION['user'] ?? null;

if ($user) {
    $id_user       = $user['id_user'];
    $nama     = $user['nama'];
    $username = $user['username'];
    $role     = $user['role'];
    $gambar   = $user['gambar'] ? 'upload/users/' . $user['gambar'] : 'assets/default.png';
    $createdAt = $user['created_at'] ?? null;
    $createdFormatted = $createdAt ? date('d M. Y', strtotime($createdAt)) : '-';
}
//===============================================================
?>


<!DOCTYPE html>
<html lang="en">

<?php include 'layouts/header.php' ?>

<body class="hold-transition sidebar-mini layout-navbar-fixed layout-fixed layout-footer-fixed text-sm">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include 'layouts/navbar.php' ?>

        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'layouts/sidebar.php' ?>


        <div class="content-wrapper">
            <div class="content">
                <div class="container-fluid pt-3">
                    <?php
                    // Cek dalam routing
                    if (isset($routes[$page]) && file_exists($pagesDir . $routes[$page])) {
                        include $pagesDir . $routes[$page];
                    } else {
                        include $pagesDir . '404.php';
                    }
                    ?>
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <strong><a href="https://gmittamariska.or.id/" target="_blank">GEREJA TAMARISKA MAULAFA</a>.</strong>
        </footer>
    </div>

    <?php include 'layouts/allScripts.php' ?>
    <?php show_alert(); ?>
</body>

</html>
<?php ob_end_flush(); ?>