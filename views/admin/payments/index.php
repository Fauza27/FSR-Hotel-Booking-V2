<?php
// views/admin/payments/index.php
// Ekstrak variabel dari $data untuk kemudahan akses
if (isset($data) && is_array($data)) { // Pastikan $data ada dan berupa array
    extract($data); 
} else {
    // Set default jika $data tidak ada (misalnya, akses langsung tanpa controller)
    $payments = [];
    $currentPage = 1;
    $totalPages = 1;
    $totalItems = 0;
    $limit = 10;
    $filters = ['method' => ($_GET['method'] ?? ''), 'status' => ($_GET['status'] ?? '')];
}
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Daftar Pembayaran</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="get" action="<?= base_url('admin/payments'); ?>" class="admin-actions" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px;">
            <input type="text" name="method" class="form-control" placeholder="Metode pembayaran..." value="<?= htmlspecialchars($filters['method'] ?? '') ?>">
            <select name="status" class="form-control">
                <option value="">-- Semua Status --</option>
                <option value="pending" <?= (($filters['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="completed" <?= (($filters['status'] ?? '') === 'completed') ? 'selected' : '' ?>>Completed</option>
                <option value="failed" <?= (($filters['status'] ?? '') === 'failed') ? 'selected' : '' ?>>Failed</option>
                <option value="refunded" <?= (($filters['status'] ?? '') === 'refunded') ? 'selected' : '' ?>>Refunded</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Booking ID</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): ?>
                    <?php $no = (($currentPage - 1) * $limit) + 1; foreach ($payments as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p->transaction_id ?? 'N/A') ?></td>
                            <td>
                                <a href="<?= base_url('admin/bookings/view/' . $p->booking_id); ?>">#<?= $p->booking_id ?></a>
                            </td>
                            <td><?= htmlspecialchars($p->payment_method) ?></td>
                            <td>Rp <?= number_format($p->amount, 0, ',', '.') ?></td>
                            <td>
                                <?php
                                $status_display = $p->payment_status;
                                $badge_class = 'secondary'; // default
                                if ($status_display === 'completed') $badge_class = 'confirmed'; // atau 'success'
                                elseif ($status_display === 'pending') $badge_class = 'pending';   // atau 'warning'
                                elseif ($status_display === 'failed') $badge_class = 'cancelled'; // atau 'danger'
                                elseif ($status_display === 'refunded') $badge_class = 'completed'; // atau 'info'
                                ?>
                                <span class="badge badge-<?= $badge_class ?>"><?= ucfirst($status_display) ?></span>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($p->payment_date)) ?></td>
                            <td>
                                <?php
                                // Persiapkan query string filter untuk link aksi
                                $actionFilterParams = [];
                                if (!empty($filters['method'])) $actionFilterParams['filter_method'] = $filters['method'];
                                if (!empty($filters['status'])) $actionFilterParams['filter_status'] = $filters['status'];
                                if (!empty($currentPage)) $actionFilterParams['current_page'] = $currentPage;
                                $actionFilterQueryString = !empty($actionFilterParams) ? '&' . http_build_query($actionFilterParams) : '';
                                ?>
                                <a href="<?= base_url('admin/payments/view/' . $p->payment_id . '?' . ltrim($actionFilterQueryString, '&')); ?>" class="btn btn-info btn-sm">Detail</a>
                                <a href="<?= base_url('admin/payments/updatestatus/' . $p->payment_id . '?' . ltrim($actionFilterQueryString, '&')); ?>" class="btn btn-warning btn-sm">Update Status</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">Tidak ada pembayaran ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php
                    // Tombol Sebelumnya
                    if ($currentPage > 1):
                        $prevPageParams = $filters;
                        $prevPageParams['page'] = $currentPage - 1;
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('admin/payments?' . http_build_query($prevPageParams)); ?>">Sebelumnya</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Sebelumnya</span>
                        </li>
                    <?php endif; ?>

                    <?php
                    $numPageLinksToShow = 5;
                    $startPage = max(1, $currentPage - floor($numPageLinksToShow / 2));
                    $endPage = min($totalPages, $startPage + $numPageLinksToShow - 1);
                    if ($endPage - $startPage + 1 < $numPageLinksToShow) {
                        $startPage = max(1, $endPage - $numPageLinksToShow + 1);
                    }

                    if ($startPage > 1):
                        $firstPageParams = $filters; $firstPageParams['page'] = 1;
                    ?>
                        <li class="page-item"><a class="page-link" href="<?= base_url('admin/payments?' . http_build_query($firstPageParams)); ?>">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif;
                    endif;

                    for ($i = $startPage; $i <= $endPage; $i++):
                        $pageParams = $filters;
                        $pageParams['page'] = $i;
                    ?>
                        <li class="page-item <?= ($i == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= base_url('admin/payments?' . http_build_query($pageParams)); ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages):
                        if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif;
                        $lastPageParams = $filters; $lastPageParams['page'] = $totalPages;
                    ?>
                        <li class="page-item"><a class="page-link" href="<?= base_url('admin/payments?' . http_build_query($lastPageParams)); ?>"><?= $totalPages; ?></a></li>
                    <?php endif; ?>

                    <?php
                    // Tombol Berikutnya
                    if ($currentPage < $totalPages):
                        $nextPageParams = $filters;
                        $nextPageParams['page'] = $currentPage + 1;
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('admin/payments?' . http_build_query($nextPageParams)); ?>">Berikutnya</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Berikutnya</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="text-center mt-2">
                <small>Menampilkan <?= count($payments); ?> dari <?= $totalItems; ?> data. Halaman <?= $currentPage; ?> dari <?= $totalPages; ?>.</small>
            </div>
        <?php endif; ?>
        <!-- End Pagination -->

    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>