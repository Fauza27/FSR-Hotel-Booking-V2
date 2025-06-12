<?php
// views/admin/users/index.php
include_once(VIEW_PATH . 'admin/layouts/header.php');

// Ekstrak variabel dari $data jika ada, jika tidak set default
$users = $data['users'] ?? [];
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$totalUsers = $data['totalUsers'] ?? 0;
$itemsPerPage = $data['itemsPerPage'] ?? ITEMS_PER_PAGE; // Ambil dari data atau konstanta
$search = $data['search'] ?? '';
$role = $data['role'] ?? '';
$startNo = $data['startNo'] ?? 1;

?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Manajemen User (<?= $totalUsers ?> Total)</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['info']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>
        
        <form method="get" action="<?= base_url('admin/users') ?>" class="admin-actions mb-3" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($search) ?>">
            <select name="role" class="form-select" style="width: auto;"> <!-- form-select untuk Bootstrap 5 -->
                <option value="">-- Semua Role --</option>
                <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="user" <?= ($role === 'user') ? 'selected' : '' ?>>User</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="admin-table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Total Booking</th>
                        <!-- Kolom status disembunyikan karena tidak ada di DB -->
                        <!-- <th>Status</th> -->
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php 
                        // $currentEntryNo = ($currentPage - 1) * $itemsPerPage + 1;
                        foreach ($users as $user): ?>
                            <tr>
                                <td><?= $startNo++ ?></td>
                                <td><?= htmlspecialchars($user->full_name) ?></td>
                                <td><?= htmlspecialchars($user->email) ?></td>
                                <td><?= htmlspecialchars($user->phone ?? '-') ?></td>
                                <td>
                                    <?= $user->total_booking ?? 0; // Sudah dihitung di model ?>
                                </td>
                                <!-- <td>
                                    <?php /* if (!empty($user->status)): ?>
                                        <?php if ($user->status === 'active'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($user->status === 'blocked'): ?>
                                            <span class="badge bg-danger">Blocked</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($user->status) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark">-</span> 
                                    <?php endif; */ ?>
                                </td> -->
                                <td>
                                    <?php if ($user->role === 'admin'): ?>
                                        <span class="badge bg-info text-dark">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <a href="<?= base_url('admin/users/view/' . $user->user_id) ?>" class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <?php if ($user->role !== 'admin'): ?>
                                        <a href="<?= base_url('admin/users/make-admin/' . $user->user_id) ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Jadikan Admin"
                                           onclick="return confirm('Yakin jadikan user ini sebagai admin?')">
                                            <i class="fas fa-user-shield"></i> Jadikan Admin
                                        </a>
                                    <?php endif; ?>
                                    <!-- Tombol block/unblock akan berfungsi jika kolom status ada dan logika di controller/model diaktifkan -->
                                    <?php /*
                                    if (isset($user->status) && $user->status === 'active'): ?>
                                        <a href="<?= base_url('admin/users/block/' . $user->user_id) ?>" class="btn btn-sm btn-outline-danger" title="Blokir" onclick="return confirm('Yakin blokir user ini?')"><i class="fas fa-ban"></i> Blokir</a>
                                    <?php elseif (isset($user->status) && $user->status === 'blocked'): ?>
                                        <a href="<?= base_url('admin/users/unblock/' . $user->user_id) ?>" class="btn btn-sm btn-outline-success" title="Aktifkan" onclick="return confirm('Yakin aktifkan user ini?')"><i class="fas fa-check-circle"></i> Aktifkan</a>
                                    <?php endif; */ ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center p-3">Tidak ada user ditemukan yang sesuai dengan kriteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php
                // Tombol Previous
                $queryParams = ['search' => $search, 'role' => $role]; // Simpan filter saat ini
                
                if ($currentPage > 1):
                    $prevPageParams = array_merge($queryParams, ['page' => $currentPage - 1]); ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(array_filter($prevPageParams))) ?>">Previous</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                <?php endif;

                // Nomor Halaman
                // Logika untuk menampilkan rentang halaman (misal, 2 halaman sebelum dan sesudah halaman saat ini)
                $startRange = max(1, $currentPage - 2);
                $endRange = min($totalPages, $currentPage + 2);

                if ($startRange > 1) {
                    $firstPageParams = array_merge($queryParams, ['page' => 1]);
                    echo '<li class="page-item"><a class="page-link" href="'.base_url('admin/users?' . http_build_query(array_filter($firstPageParams))).'">1</a></li>';
                    if ($startRange > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }

                for ($i = $startRange; $i <= $endRange; $i++):
                    $pageParams = array_merge($queryParams, ['page' => $i]); ?>
                    <li class="page-item <?= ($i == $currentPage) ? "active" : "" ?>">
                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(array_filter($pageParams))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($endRange < $totalPages) {
                    if ($endRange < $totalPages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    $lastPageParams = array_merge($queryParams, ['page' => $totalPages]);
                    echo '<li class="page-item"><a class="page-link" href="'.base_url('admin/users?' . http_build_query(array_filter($lastPageParams))).'">'.$totalPages.'</a></li>';
                }

                // Tombol Next
                if ($currentPage < $totalPages):
                    $nextPageParams = array_merge($queryParams, ['page' => $currentPage + 1]); ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= base_url('admin/users?' . http_build_query(array_filter($nextPageParams))) ?>">Next</a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        <p class="text-center mt-2 text-muted">
            Menampilkan <?= count($users) ?> dari <?= $totalUsers ?> total user. Halaman <?= $currentPage ?> dari <?= $totalPages ?>.
        </p>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>