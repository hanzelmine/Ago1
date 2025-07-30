<?php
session_start();
require_once 'database.php';
require_once 'helpers.php';
require_once 'functions/auth.php';
block_if_logged_in();


if (isset($_POST['login'])) {
    // Ambil input dan sanitasi
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Proses login
    if (login($username, $password)) {
        set_alert('success', 'Berhasil', 'Login berhasil. Selamat datang!');
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        set_alert('error', 'Gagal', 'Username atau password salah.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include 'layouts/header.php' ?>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h2>GEREJA TAMARISKA MAULAFA</h2>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Silahkan Login</p>

                <form action="" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Username" autofocus required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="toggle-password" data-target="#password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8 d-flex align-items-center">
                            <!-- <small>Don't have account? Click <a href="register.php">here</a></small> -->
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" name="login" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
    <?php include 'layouts/allScripts.php' ?>
    <?php show_alert(); ?>
</body>

</html>