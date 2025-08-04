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
                                    Data Rayon
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=keluarga" class="nav-link <?= ($page === 'keluarga') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-house-user"></i>
                                <p>
                                    Data Keluarga
                                </p>
                            </a>
                        </li>

                        <li class="nav-item <?= in_array($page, ['createjemaat', 'jemaat', 'baptis', 'sidi', 'nikah']) ? 'menu-open' : '' ?>">
                            <a href="#" class="nav-link <?= in_array($page, ['createjemaat', 'jemaat', 'baptis', 'sidi', 'nikah']) ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-friends"></i>
                                <p>
                                    Data Jemaat
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="index.php?page=jemaat" class="nav-link <?= in_array($page, ['createjemaat', 'jemaat']) ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-user-friends"></i>
                                        <p>Jemaat</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=baptis" class="nav-link <?= $page === 'baptis' ? 'active' : '' ?>">
                                        <!-- <i class="nav-icon fas fa-hands-water"></i> -->
                                        <i class="nav-icon fas fa-droplet"></i>
                                        <p>Baptisan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=sidi" class="nav-link <?= $page === 'sidi' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-book"></i>
                                        <p>Katekisasi Sidi</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=nikah" class="nav-link <?= $page === 'nikah' ? 'active' : '' ?>">
                                        <i class="nav-icon fas fa-ring"></i>
                                        <p>Pernikahan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>



                        <li class="nav-item">
                            <a href="index.php?page=meninggal" class="nav-link <?= in_array($page, ['meninggal']) ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-dove"></i>
                                <p>
                                    Data Meninggal
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=astestasi" class="nav-link <?= in_array($page, ['astestasi']) ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-people-arrows"></i>
                                <p>
                                    Data Astestasi
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=pencarian" class="nav-link <?= ($page === 'pencarian') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-search"></i>
                                <p>
                                    Pencarian & Filter Data
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