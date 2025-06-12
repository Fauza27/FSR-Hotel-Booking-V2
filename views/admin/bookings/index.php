<?php 
// Ekstrak variabel dari $data untuk kemudahan akses
extract($data); // Ini akan membuat $bookings, $currentPage, $totalPages, dll. tersedia
include_once(VIEW_PATH . 'admin/layouts/header.php'); 
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2>Daftar Booking</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="GET" action="<?= base_url('admin/bookings'); ?>">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Semua</option>
                        <option value="pending" <?= (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?= (isset($filters['status']) && $filters['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?= (isset($filters['status']) && $filters['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="completed" <?= (isset($filters['status']) && $filters['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_start">Dari Check-in Tgl</label>
                    <input type="date" name="date_start" id="date_start" class="form-control" value="<?= htmlspecialchars($filters['date_start'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_end">Sampai Check-out Tgl</label>
                    <input type="date" name="date_end" id="date_end" class="form-control" value="<?= htmlspecialchars($filters['date_end'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary" style="margin-top: 30px;">Filter</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php 
                    // Persiapkan query string filter untuk link aksi
                    $actionFilterParams = [];
                    if (!empty($filters['status'])) $actionFilterParams['filter_status'] = $filters['status'];
                    if (!empty($filters['date_start'])) $actionFilterParams['filter_date_start'] = $filters['date_start'];
                    if (!empty($filters['date_end'])) $actionFilterParams['filter_date_end'] = $filters['date_end'];
                    if (!empty($currentPage)) $actionFilterParams['current_page'] = $currentPage; // halaman saat ini
                    $actionFilterQueryString = http_build_query($actionFilterParams);
                    ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td><?= $b->booking_id; ?></td>
                            <td><?= htmlspecialchars($b->user_name ?? ('User ID: ' . $b->user_id)); ?></td>
                            <td><?= htmlspecialchars($b->room_number); ?></td>
                            <td><?= date('d M Y', strtotime($b->check_in_date)); ?></td>
                            <td><?= date('d M Y', strtotime($b->check_out_date)); ?></td>
                            <td>Rp <?= number_format($b->total_price, 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?= htmlspecialchars($b->status); ?>"><?= ucfirst(htmlspecialchars($b->status)); ?></span></td>
                            <td>
                                <a href="<?= base_url('admin/bookings/view/' . $b->booking_id); ?>" class="btn btn-info btn-sm">Detail</a>
                                <?php
                                $confirmUrl = base_url('admin/bookings/update-status/' . $b->booking_id . '?status=confirmed&from_index=1' . ($actionFilterQueryString ? '&' . $actionFilterQueryString : ''));
                                $cancelUrl = base_url('admin/bookings/update-status/' . $b->booking_id . '?status=cancelled&from_index=1' . ($actionFilterQueryString ? '&' . $actionFilterQueryString : ''));
                                $completeUrl = base_url('admin/bookings/update-status/' . $b->booking_id . '?status=completed&from_index=1' . ($actionFilterQueryString ? '&' . $actionFilterQueryString : ''));
                                ?>
                                <?php if ($b->status === 'pending'): ?>
                                    <a href="<?= $confirmUrl; ?>" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi booking ini?')">Konfirmasi</a>
                                    <a href="<?= $cancelUrl; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan booking ini?')">Batalkan</a>
                                <?php elseif ($b->status === 'confirmed'): ?>
                                    <a href="<?= $completeUrl; ?>" class="btn btn-primary btn-sm" onclick="return confirm('Tandai sebagai selesai?')">Selesai</a>
                                    <a href="<?= $cancelUrl; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan booking ini?')">Batalkan</a>
                                <?php elseif ($b->status === 'cancelled'): ?>
                                    <span class="text-muted">- Dibatalkan -</span>
                                <?php elseif ($b->status === 'completed'): ?>
                                    <span class="text-success">✓ Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">Tidak ada data booking yang sesuai dengan filter.</td></tr>
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
                        $prevPageParams = $filters; // Ambil filter yang ada
                        $prevPageParams['page'] = $currentPage - 1;
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('admin/bookings?' . http_build_query($prevPageParams)); ?>">Sebelumnya</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Sebelumnya</span>
                        </li>
                    <?php endif; ?>

                    <?php
                    // Nomor Halaman (dengan batasan untuk tidak terlalu banyak)
                    $numPageLinksToShow = 5; // Jumlah link nomor halaman yang ditampilkan
                    $startPage = max(1, $currentPage - floor($numPageLinksToShow / 2));
                    $endPage = min($totalPages, $startPage + $numPageLinksToShow - 1);
                    if ($endPage - $startPage + 1 < $numPageLinksToShow) { // Jika di akhir, sesuaikan $startPage
                        $startPage = max(1, $endPage - $numPageLinksToShow + 1);
                    }

                    if ($startPage > 1): // Tombol ke halaman 1 jika tidak dimulai dari 1
                        $firstPageParams = $filters; $firstPageParams['page'] = 1;
                    ?>
                        <li class="page-item"><a class="page-link" href="<?= base_url('admin/bookings?' . http_build_query($firstPageParams)); ?>">1</a></li>
                        <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif;
                    endif;

                    for ($i = $startPage; $i <= $endPage; $i++):
                        $pageParams = $filters; // Ambil filter yang ada
                        $pageParams['page'] = $i;
                    ?>
                        <li class="page-item <?= ($i == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= base_url('admin/bookings?' . http_build_query($pageParams)); ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($endPage < $totalPages): // Tombol ke halaman terakhir jika tidak di akhir
                        if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif;
                        $lastPageParams = $filters; $lastPageParams['page'] = $totalPages;
                    ?>
                        <li class="page-item"><a class="page-link" href="<?= base_url('admin/bookings?' . http_build_query($lastPageParams)); ?>"><?= $totalPages; ?></a></li>
                    <?php endif; ?>

                    <?php
                    // Tombol Berikutnya
                    if ($currentPage < $totalPages):
                        $nextPageParams = $filters; // Ambil filter yang ada
                        $nextPageParams['page'] = $currentPage + 1;
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= base_url('admin/bookings?' . http_build_query($nextPageParams)); ?>">Berikutnya</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Berikutnya</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="text-center mt-2">
                <small>Menampilkan <?= count($bookings); ?> dari <?= $totalItems; ?> data. Halaman <?= $currentPage; ?> dari <?= $totalPages; ?>.</small>
            </div>
        <?php endif; ?>
        <!-- End Pagination -->

    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>