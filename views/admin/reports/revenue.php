<?php
// views/admin/reports/revenue.php
require VIEW_PATH . 'admin/layouts/header.php';
// sidebar.php sudah di-include di dalam <div class="admin-container"> di bawah
?>

<div class="admin-container d-flex">
    <div class="admin-sidebar">      
    <?php require VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    </div>

    <div class="admin-content p-4 flex-grow-1">
            <h2 class="mb-4">Laporan Pendapatan</h2>
            
            <form method="get" action="<?= APP_URL ?>/admin/reports/revenue" class="row g-3 mb-4 align-items-end">
                <div class="col-md-2">
                    <label for="period" class="form-label">Grup Grafik</label>
                    <select name="period" id="period" class="form-select">
                        <option value="daily" <?= ($_GET['period'] ?? $data->periodLabel ?? 'monthly') === 'daily' ? 'selected' : '' ?>>Harian</option>
                        <option value="monthly" <?= ($_GET['period'] ?? $data->periodLabel ?? 'monthly') === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                        <option value="yearly" <?= ($_GET['period'] ?? $data->periodLabel ?? 'monthly') === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" name="start" id="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start'] ?? $data->actualStartDate ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end" id="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end'] ?? $data->actualEndDate ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
                <!-- <div class="col-md-2 d-flex flex-column">
                    <a href="<?= APP_URL ?>/admin/reports/export/excel/revenue?<?= http_build_query(array_merge($_GET, ['period' => $_GET['period'] ?? 'all'])) ?>" class="btn btn-success mb-1 btn-sm">Export Excel</a>
                    <a href="<?= APP_URL ?>/admin/reports/export/pdf/revenue?<?= http_build_query(array_merge($_GET, ['period' => $_GET['period'] ?? 'all'])) ?>" class="btn btn-danger btn-sm">Export PDF</a>
                </div> -->
            </form>

            <?php
            // Untuk debugging, Anda bisa uncomment baris ini:
            // echo "<pre>Data di View: "; print_r($data); echo "</pre>";
            ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Grafik Pendapatan (<?= htmlspecialchars($data->periodLabel ?? 'Data tidak tersedia') ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data->chartData)): ?>
                        <canvas id="revenueChart" style="min-height: 300px; max-height: 400px;"></canvas>
                    <?php else: ?>
                        <p class="text-center text-muted">Tidak ada data pendapatan untuk periode yang dipilih.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Breakdown Pendapatan per Kategori Kamar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Kategori Kamar</th>
                                    <th scope="col">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data->breakdownData)): ?>
                                    <?php $no = 1; foreach ($data->breakdownData as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row->category_name) ?></td>
                                            <td>Rp <?= number_format($row->revenue, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">Tidak ada data breakdown pendapatan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Perbandingan Periode</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card text-white bg-success shadow">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Periode Dipilih</h6>
                                    <p class="card-text fs-4">Rp <?= number_format($data->currentPeriodRevenue ?? 0, 0, ',', '.') ?></p>
                                    <?php if(isset($data->actualStartDate) && isset($data->actualEndDate)): ?>
                                    <small class="fst-italic">Dari: <?= htmlspecialchars(date('d M Y', strtotime($data->actualStartDate))) ?> <br>Sampai: <?= htmlspecialchars(date('d M Y', strtotime($data->actualEndDate))) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-white bg-secondary shadow">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Periode Sebelumnya</h6>
                                    <p class="card-text fs-4">Rp <?= number_format($data->previousPeriodRevenue ?? 0, 0, ',', '.') ?></p>
                                    <small class="fst-italic">Perhitungan berdasarkan durasi periode yang dipilih.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartRawData = <?= json_encode($data->chartData ?? []) ?>;
    
    if (chartRawData && chartRawData.length > 0) {
        const labels = chartRawData.map(item => item.label); // label sudah diformat di model
        const revenueData = chartRawData.map(item => parseFloat(item.revenue) || 0);

        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: revenueData,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting agar bisa mengatur tinggi canvas
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        ticks: {
                            // autoSkip: true,
                            // maxTicksLimit: 15 // Batasi jumlah label X jika terlalu banyak
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
<?php require VIEW_PATH . 'admin/layouts/footer.php'; ?>