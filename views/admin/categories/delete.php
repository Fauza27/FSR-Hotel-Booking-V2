<?php
// views/admin/categories/delete.php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>
<div class="container mt-4">
    <h2 class="mb-3">Hapus Kategori Kamar</h2>
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Konfirmasi Hapus Kategori</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th>Nama Kategori</th><td><?= htmlspecialchars($category['name']) ?></td></tr>
                <tr><th>Deskripsi</th><td><?= htmlspecialchars($category['description']) ?></td></tr>
            </table>
            <hr>
            <?php if (!empty($rooms)): ?>
                <div class="alert alert-warning">
                    <strong>Kategori ini tidak dapat dihapus!</strong><br>
                    Masih ada kamar yang menggunakan kategori ini.<br>
                    <ul class="mb-0">
                        <?php foreach ($rooms as $room): ?>
                            <li>Kamar: <?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($room['description']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <a href="/admin/categories" class="btn btn-secondary">Kembali</a>
            <?php else: ?>
                <form method="post" action="">
                    <div class="alert alert-danger">
                        Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.
                    </div>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    <a href="/admin/categories" class="btn btn-secondary ms-2">Batal</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
