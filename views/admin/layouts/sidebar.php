<div class="sidebar-header">
    <h4>Menu Admin</h4>
</div>

<nav class="sidebar-nav">
    <ul class="nav-list">
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/admin' || $_SERVER['REQUEST_URI'] == '/admin/') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-section">
            <span class="section-title">Manajemen</span>
        </li>
        
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/rooms" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/rooms') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-bed"></i>
                <span>Kamar</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/bookings" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/bookings') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                <span>Booking</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/users" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Pengguna</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/categories" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/categories') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span>Kategori</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/facilities" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/facilities') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-concierge-bell"></i>
                <span>Fasilitas</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/payments" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/payments') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                <span>Pembayaran</span>
            </a>
        </li>
        <li class="nav-section">
            <span class="section-title">Laporan</span>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/reports/bookings" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/reports/bookings') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Laporan Booking</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/reports/revenue" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/reports/revenue') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Laporan Pendapatan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/admin/reports/rooms" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/reports/rooms') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-bed"></i>
                <span>Laporan Kamar</span>
            </a>
        </li>
        <li class="nav-section">
            <span class="section-title">Akun</span>
        </li>
        <li class="nav-item">
            <a href="<?= APP_URL ?>/logout" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>