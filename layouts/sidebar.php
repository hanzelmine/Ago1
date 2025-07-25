        <aside class="main-sidebar sidebar-light-primary elevation-1">
            <a href="index.php" class="brand-link bg-white">
                <img src="dist/img/logo.png" alt="GTM" class="brand-image img-circle elevation-2">
                <span class="brand-text">GMIT TAMARISKA MAULAFA</span>
            </a>

            <div class="sidebar">

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                        <li class="nav-item">
                            <a href="index.php?page=dashboard" class="nav-link <?= ($page === 'dashboard') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=rayon" class="nav-link <?= ($page === 'rayon') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-location-dot"></i>
                                <p>
                                    Rayon
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=keluarga" class="nav-link <?= ($page === 'keluarga') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-house-user"></i>
                                <p>
                                    Keluarga
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=jemaat" class="nav-link <?= in_array($page, ['createjemaat', 'jemaat']) ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-friends"></i>
                                <p>
                                    Jemaat
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=pencarian" class="nav-link <?= ($page === 'pencarian') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-search"></i>
                                <p>
                                    Pencarian
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=profile" class="nav-link <?= ($page === 'profile') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-circle-user"></i>
                                <p>
                                    Profile
                                </p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>