<?php
// Refresh from session
$gambarRaw = $_SESSION['user']['gambar'] ?? 'default.png';
$nama = $_SESSION['user']['nama'] ?? 'User';
$role = $_SESSION['user']['role'] ?? 'User';
$createdAt = $_SESSION['user']['created_at'] ?? null;
$createdFormatted = $createdAt ? date('d M. Y', strtotime($createdAt)) : '-';

// Safe fallback logic
$gambarPathDisk = __DIR__ . '/../upload/users/' . $gambarRaw;
$defaultPathDisk = __DIR__ . '/../upload/default.png';

$gambarUrl = file_exists($gambarPathDisk) ? 'upload/users/' . $gambarRaw : 'upload/default.png';
$gambarPathDisk = file_exists($gambarPathDisk) ? $gambarPathDisk : $defaultPathDisk;
$gambarUrl .= '?t=' . filemtime($gambarPathDisk); // cache buster
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="<?= htmlspecialchars($gambarUrl) ?>" class="user-image img-circle elevation-2" alt="User Image" style="object-fit: cover; border-radius: 50%;">
                <span class="d-none d-md-inline"><?= htmlspecialchars($nama) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-primary">
                    <a href="<?= htmlspecialchars($gambarUrl) ?>" data-fancybox="navbar">
                        <img
                            src="<?= htmlspecialchars($gambarUrl) ?>"
                            class="img-circle elevation-2"
                            alt="User Image"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    </a>

                    <p><?= htmlspecialchars($nama) ?> - <?= ucfirst(htmlspecialchars($role)) ?><small>Member since <?= $createdFormatted ?></small></p>
                </li>
                <li class="user-footer">
                    <a href="index.php?page=profile" class="btn btn-primary">Profile</a>
                    <form id="logoutForm" action="index.php?page=logout" method="POST" class="float-right">
                        <button type="button" id="logoutBtn" class="btn btn-primary">Logout</button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function() {
        confirmAction({
            title: 'Yakin ingin logout?',
            text: 'Sesi Anda akan diakhiri.',
            icon: 'warning',
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal',
            formId: 'logoutForm'
        });
    });
</script>