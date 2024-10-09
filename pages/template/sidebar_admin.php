<?php
$page = isset($_GET['page']) ? $_GET['page'] : '';
?>

<aside class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <img src="assets/images/logo.png" alt="profile" width="190" />
        </li>

        <li class="nav-item mt-3 <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=dashboard">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo in_array($page, ['pesanan-masuk', 'riwayat-pesanan', 'transaksi']) ? 'active' : ''; ?>">
            <a class="nav-link" data-bs-toggle="collapse" href="#manajemen-pesanan" aria-expanded="<?php echo in_array($page, ['pesanan-masuk', 'riwayat-pesanan', 'transaksi']) ? 'true' : 'false'; ?>" aria-controls="manajemen-pesanan">
                <span class="menu-title">Manajemen Pesanan</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-cart menu-icon"></i>
            </a>
            <div class="collapse <?php echo in_array($page, ['pesanan-masuk', 'riwayat-pesanan', 'transaksi']) ? 'show' : ''; ?>" id="manajemen-pesanan">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'pesanan-masuk' ? 'active' : ''; ?>" href="?page=pesanan-masuk">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'riwayat-pesanan' ? 'active' : ''; ?>" href="?page=riwayat-pesanan">Riwayat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'transaksi' ? 'active' : ''; ?>" href="?page=transaksi">Transaksi</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item <?php echo $page == 'pelanggan' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=pelanggan">
                <span class="menu-title">Pelanggan</span>
                <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'kategori' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=kategori">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-folder menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'layanan' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=layanan">
                <span class="menu-title">Produk</span>
                <i class="mdi mdi-build menu-icon"></i>
            </a>
        </li>


        <li class="nav-item <?php echo $page == 'laporan' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=laporan">
                <span class="menu-title">Laporan</span>
                <i class="mdi mdi-description menu-icon"></i>
            </a>
        </li>

        <li class="nav-item <?php echo $page == 'pengguna' ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=pengguna">
                <span class="menu-title">Pengguna</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>
    </ul>
</aside>