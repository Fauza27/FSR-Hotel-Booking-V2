<?php
// views/admin/reports/index.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-4">Dashboard Laporan</h2>
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="fs-4 fw-bold"><?= $summary['totalBookings'] ?? 0 ?></div>
                    <div>Total Booking</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="fs-4 fw-bold">Rp <?= number_format($summary['totalRevenue'] ?? 0, 0, ',', '.') ?></div>
                    <div>Total Pendapatan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="fs-4 fw-bold"><?= $summary['roomOccupancy']['occupancy_rate'] ?? '0%' ?></div>
                    <div>Rata-rata Okupansi Kamar</div>
                </div>
            </div>
        </div>
        <div class="dashboard-row mb-4">
            <div class="dashboard-card">
                <a href="/admin/reports/bookings" class="text-decoration-none text-dark">
                    <div class="fw-bold">Laporan Booking</div>
                    <div class="text-muted">Grafik & detail booking</div>
                </a>
            </div>
            <div class="dashboard-card">
                <a href="/admin/reports/revenue" class="text-decoration-none text-dark">
                    <div class="fw-bold">Laporan Pendapatan</div>
                    <div class="text-muted">Grafik & breakdown</div>
                </a>
            </div>
            <div class="dashboard-card">
                <a href="/admin/reports/rooms" class="text-decoration-none text-dark">
                    <div class="fw-bold">Laporan Kamar</div>
                    <div class="text-muted">Okupansi & performa</div>
                </a>
            </div>
            <div class="dashboard-card">
                <a href="/admin/payments" class="text-decoration-none text-dark">
                    <div class="fw-bold">Pembayaran</div>
                    <div class="text-muted">Data pembayaran</div>
                </a>
            </div>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
