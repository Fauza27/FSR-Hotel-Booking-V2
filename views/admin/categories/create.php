<!-- views/admin/categories/create.php -->
<?php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>

    <div class="admin-content">
        <h2 class="mb-3">Tambah Kategori Kamar</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <!-- PERBAIKAN: Ubah atribut action -->
        <form method="post" action="<?= APP_URL ?>/admin/categories/store">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required maxlength="100">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="3" maxlength="255"><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= APP_URL ?>/admin/categories/" class="btn btn-secondary ms-2">Batal</a>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>