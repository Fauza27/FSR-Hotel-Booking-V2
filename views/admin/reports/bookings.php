<?php
// views/admin/reports/bookings.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
    </div>

    <div class="admin-content">
        <h2 class="mb-4">Laporan Booking</h2>
        <form method="get" class="row g-3 mb-3">
            <div class="col-md-3">
                <label>Periode</label>
                <select name="period" class="form-select">
                    <option value="daily" <?= ($_GET['period'] ?? 'daily')==='daily'?'selected':'' ?>>Harian</option>
                    <option value="monthly" <?= ($_GET['period'] ?? '')==='monthly'?'selected':'' ?>>Bulanan</option>
                    <option value="yearly" <?= ($_GET['period'] ?? '')==='yearly'?'selected':'' ?>>Tahunan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Dari</label>
                <input type="date" name="start" class="form-control" value="<?= htmlspecialchars($_GET['start'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label>Sampai</label>
                <input type="date" name="end" class="form-control" value="<?= htmlspecialchars($_GET['end'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Tampilkan</button>
                <!-- <a href="/admin/reports/export/excel/bookings?<?= http_build_query($_GET) ?>" class="btn btn-success me-2">Export Excel</a>
                <a href="/admin/reports/export/pdf/bookings?<?= http_build_query($_GET) ?>" class="btn btn-danger">Export PDF</a> -->
            </div>
        </form>
        <div class="mb-4">
            <h5>Grafik Booking</h5>
            <canvas id="bookingChart" height="80"></canvas>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah Booking</th>
                        <th>Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php $no=1; foreach ($data as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row->label ?? $row->date ?? $row->month ?? $row->year) ?></td>
                                <td><?= $row->total_bookings ?? 0 ?></td>
                                <td>Rp <?= number_format($row->total_revenue ?? 0, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = {
    labels: <?= json_encode(array_map(fn($r) => $r->label ?? $r->date ?? $r->month ?? $r->year, $data ?? [])) ?>,
    datasets: [{
        label: 'Jumlah Booking',
        data: <?= json_encode(array_map(fn($r) => $r->total_bookings ?? 0, $data ?? [])) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2
    }]
};
const ctx = document.getElementById('bookingChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
