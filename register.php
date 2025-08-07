<?php
session_start();
require_once 'database.php';
require_once 'helpers.php';
require_once 'functions/auth.php';
block_if_logged_in();


if (isset($_POST['register'])) {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'user';
    $gambar = $_FILES['gambar'] ?? null;


    // Call register function
    $result = register($nama, $username, $password, $role, $gambar);

    if ($result === true) {
        set_alert('success', 'Berhasil', 'Registrasi berhasil! Silakan login.');
        header('Location: login.php');
        exit;
    } elseif ($result === 'exists') {
        set_alert('error', 'Gagal', 'Username sudah digunakan!');
        header('Location: register.php');
        exit;
    } else {
        set_alert('error', 'Error', 'Terjadi kesalahan.');
        header('Location: register.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'layouts/header.php' ?>

<body class="hold-transition register-page">
    <div class="register-box w-100" style="max-width: 800px;">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h2>GEREJA TAMARISKA MAULAFA</h2>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register a new account</p>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column: Form Inputs -->
                        <div class="col-md-7 border-right pr-4">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                            </div>

                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" data-target="#password" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="role" value="user">

                            <div class="mt-4 d-flex justify-content-between">
                                <div>
                                    <small>
                                        Already have an account? <a href="login.php">Login here</a>
                                    </small>
                                </div>
                                <button type="submit" name="register" class="btn btn-primary">Register</button>
                            </div>
                        </div>

                        <!-- Right Column: Image Upload + Preview -->
                        <div class="col-md-5 pl-4">
                            <div class="form-group">
                                <label>Upload Gambar</label>
                                <input type="file" id="gambarInput" name="gambar" class="form-control-file" accept="image/*">
                            </div>
                            <div class="mt-3 text-center">
                                <a href="#" id="gambarPreviewLink" data-fancybox="preview" style="display: none;">
                                    <img id="gambarPreview" src="#" style="max-width: 100%; border-radius: 10px; border: 1px solid #ccc;" />
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.register-box -->
    <?php include 'layouts/allScripts.php' ?>
    <script>
        $(document).ready(function() {
            previewImage('#gambarInput', '#gambarPreview', '#gambarPreviewLink');
        });
    </script>
    <?php show_alert(); ?>
</body>

</html>