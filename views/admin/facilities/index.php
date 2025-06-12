<?php
// views/admin/facilities/index.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Daftar Fasilitas</h2>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"> <?= $_SESSION['success']; unset($_SESSION['success']); ?> </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
        <?php endif; ?>
        <a href="<?php echo APP_URL; ?>/admin/facilities/create/" class="btn btn-primary mb-3">+ Tambah Fasilitas</a>
        <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Fasilitas</th>
                    <th>Icon</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($facilities)): ?>
                    <?php $no=1; foreach ($facilities as $f): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($f->name) ?></td>
                            <td>
                                <?php if (!empty($f->icon)): ?>
                                    <i class="bi bi-<?= htmlspecialchars($f->icon) ?>" style="font-size: 1.5em;"></i>
                                    <span class="d-none d-md-inline ms-2">(<?= htmlspecialchars($f->icon) ?>)</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($f->description) ?></td>
                            <td>
                                <a href="<?= APP_URL ?>/admin/facilities/edit/<?= $f->facility_id ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= APP_URL ?>/admin/facilities/delete/<?= $f->facility_id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus fasilitas ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Tidak ada fasilitas ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
