<?php
// views/admin/reports/rooms.php
require __DIR__ . '/../layouts/header.php';
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    </div>

    <div class="container mt-4 admin-content">
        <h2 class="mb-4">Laporan Kamar</h2>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-bg-info mb-3">
                    <div class="card-body">
                        <div class="fs-4 fw-bold"><?= htmlspecialchars($data['average_occupancy_rate'] ?? '0%') ?></div>
                        <div>Tingkat Okupansi Rata-rata Hotel</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <div class="fs-4 fw-bold"><?= htmlspecialchars($data['most_popular_room']['room_number'] ?? '-') ?></div>
                        <div>Kamar Paling Populer</div>
                        <div class="text-muted">(<?= htmlspecialchars($data['most_popular_room']['total_bookings'] ?? 0) ?> booking)</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <div class="fs-4 fw-bold"><?= htmlspecialchars($data['best_performance']['room_number'] ?? '-') ?></div>
                        <div>Performa Terbaik</div>
                        <div class="text-muted">Pendapatan: Rp <?= number_format($data['best_performance']['total_revenue'] ?? 0, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <h5>Analisis Okupansi Kamar (30 Hari Terakhir)</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No Kamar</th>
                            <th>Kategori</th>
                            <th>Okupansi (%)</th>
                            <th>Total Booking</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['rooms'])): ?>
                            <?php $no=1; foreach ($data['rooms'] as $room): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($room['room_number']) ?></td>
                                    <td><?= htmlspecialchars($room['category_name']) ?></td>
                                    <!-- Baris yang mungkin juga perlu ?? 0 -->
                                    <td><?= htmlspecialchars(number_format($room['occupancy_rate'] ?? 0, 2)) ?>%</td>
                                    <td><?= htmlspecialchars($room['total_bookings'] ?? 0) ?></td>
                                    <!-- Baris 70, tambahkan ?? 0 -->
                                    <td>Rp <?= number_format($room['total_revenue'] ?? 0, 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">Tidak ada data laporan kamar untuk periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>