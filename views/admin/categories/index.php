<?php
// views/admin/categories/index.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Daftar Kategori Kamar</h2>
        
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"> <?= $_SESSION['success']; unset($_SESSION['success']); ?> </div>
        <?php endif; ?>
        
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
        <?php endif; ?>
        
        <a href="<?php echo APP_URL; ?>/admin/categories/create" class="btn btn-primary mb-3">+ Tambah Kategori</a>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Kamar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php $no = 1; foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($cat->name) ?></td>
                                <td><?= htmlspecialchars($cat->description ?? '-') ?></td>
                                <td><?= $cat->room_count ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/admin/categories/edit/<?= $cat->category_id ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <?php if ($cat->room_count == 0): ?>
                                        <a href="<?php echo APP_URL; ?>/admin/categories/delete/<?= $cat->category_id ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Yakin ingin menghapus kategori ini?')">Delete</a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" 
                                                disabled 
                                                title="Tidak bisa hapus, masih ada <?= $cat->room_count ?> kamar">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada kategori ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>