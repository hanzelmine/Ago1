        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <?php if (is_logged_in()): ?>
                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <!-- Navbar User Dropdown -->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $gambar ?>"
                                class="user-image img-circle elevation-2"
                                style=" object-fit: cover; border-radius: 50%;"
                                alt="User Image">
                            <span class="d-none d-md-inline"><?= htmlspecialchars($nama) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right rounded shadow border-0">
                            <!-- User image -->
                            <li class="user-header bg-primary rounded-top">
                                <img src="<?= $gambar ?>" class="img-circle elevation-2" alt="User Image" style="object-fit: cover; border-radius: 50%;">
                                <p>
                                    <?= htmlspecialchars($nama) ?> - <?= ucfirst(htmlspecialchars($role)) ?>
                                    <small>Member since <?= $createdFormatted ?></small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer rounded-bottom">
                                <a href="index.php?page=profile" class="btn btn-primary">Profile</a>

                                <form id="logoutForm" action="index.php?page=logout" method="POST" style="display: inline-block;" class="float-right">
                                    <button type="button" id="logoutBtn" class="btn btn-primary">Logout</button>
                                </form>

                            </li>
                        </ul>
                    </li>
                </ul>
            <?php endif; ?>
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