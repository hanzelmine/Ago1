<?php
require_once 'functions/auth.php';

if (isset($_POST['update_profile'])) {
    $result = update_profile($_POST);

    if ($result === true) {
        // 🟢 Refresh session with updated data
        $id_user = $_SESSION['user']['id_user'];
        $updatedUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id_user'"));
        $_SESSION['user'] = $updatedUser;

        set_alert('success', 'Berhasil', 'Profil berhasil diperbarui');
    } elseif ($result === 'error') {
        set_alert('error', 'Validasi Gagal', 'Nama dan Username tidak boleh kosong!');
    } elseif ($result === 'exists') {
        set_alert('error', 'Username Digunakan', 'Username sudah digunakan oleh pengguna lain!');
    } elseif ($result === 'fail') {
        set_alert('error', 'Gagal', 'Gagal menyimpan perubahan!');
    } elseif ($result === 'unauthorized') {
        set_alert('error', 'Akses Ditolak', 'Silakan login kembali.');
        header('Location: login.php');
        exit;
    }

    header('Location: index.php?page=profile');
    exit;
}

// Ambil data user terbaru
$id_user = $_SESSION['user']['id_user'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id_user'"));

$nama     = $user['nama'] ?? '';
$username = $user['username'] ?? '';
$role     = $user['role'] ?? 'User';
$gambar   = $user['gambar'] ?? 'default.png';
$gambar_path = "upload/users/$gambar";
$gambar_url = file_exists($gambar_path) ? "$gambar_path?t=" . filemtime($gambar_path) : 'upload/users/default.png';

?>

<div class="row">
    <!-- LEFT PROFILE PREVIEW -->
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <!-- Wrap image in anchor tag for FancyBox -->
                <div class="text-center mb-3">
                    <a href="<?= htmlspecialchars($gambar_url) ?>" data-fancybox="profile">
                        <img
                            id="imgPreviewProfile"
                            class="img-fluid img-circle elevation-3"
                            src="<?= htmlspecialchars($gambar_url) ?>"
                            alt="User profile picture"
                            style="width: 250px; height: 250px; object-fit: cover;">
                    </a>
                </div>


                <h3 class="profile-username text-center"><?= htmlspecialchars($nama) ?></h3>
                <p class="text-muted text-center"><?= htmlspecialchars(ucfirst($role)) ?></p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Username</b> <span class="float-right"><?= htmlspecialchars($username) ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Role</b> <span class="float-right"><?= htmlspecialchars(ucfirst($role)) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT FORM FOR UPDATE -->
    <div class="col-md-7">
        <div class="card card-green card-outline">
            <div class="card-header p-3">
                <h4>Update Information</h4>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="username" class="col-sm-2 col-form-label">Username</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label">New Password</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak berubah">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-target="#password" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="gambar" class="col-sm-2 col-form-label">Gambar</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control-file" id="gambarProfile" name="gambar" accept="image/*">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                            <button type="submit" name="update_profile" class="btn btn-success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        previewImage('#gambarProfile', '#imgPreviewProfile');
    });
</script>