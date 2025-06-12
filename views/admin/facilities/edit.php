<?php
//views/admin/facilities/edit.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>

    <div class="admin-content">
        <div class="container mt-4">
            <h2 class="mb-3">Edit Fasilitas</h2>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($errors)): // Jika $errors dilewatkan langsung oleh controller ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('admin/facilities/update/' . ($facility->facility_id ?? $id) ) ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Fasilitas <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($facility->name ?? '') ?>" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label for="icon" class="form-label">Icon <span class="text-danger">*</span></label>
                    <input type="text" name="icon" id="icon" class="form-control" value="<?= htmlspecialchars($facility->icon ?? '') ?>" required maxlength="50" oninput="previewIcon()">
                    <div class="mt-2">
                        <span>Preview: </span>
                        <i id="iconPreview" class="bi bi-<?= htmlspecialchars($facility->icon ?? '') ?>" style="font-size: 1.5em;"></i>
                        <span class="ms-2 text-muted" id="iconNamePreview"><?= htmlspecialchars($facility->icon ?? '') ?></span>
                    </div>
                    <small class="text-muted">Gunakan nama icon Bootstrap Icons, contoh: wifi, tv, swim, bar, dll.</small>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" rows="3" maxlength="255"><?= htmlspecialchars($facility->description ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="<?= base_url('admin/facilities') ?>" class="btn btn-secondary ms-2">Batal</a>
            </form>
        </div>
    </div>
</div>
<script>
function previewIcon() {
    var val = document.getElementById('icon').value.trim();
    var icon = document.getElementById('iconPreview');
    var name = document.getElementById('iconNamePreview');
    icon.className = 'bi bi-' + val; // Pastikan class dasar 'bi' tidak hilang jika val kosong
    if (val === '') {
        icon.className = 'bi'; // Atau class default lain
    }
    name.textContent = val;
}
// Panggil sekali saat load untuk memastikan preview benar jika ada value awal
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('icon')) { // Cek jika elemen ada
         previewIcon();
    }
});
</script>
<?php // require __DIR__ . '/../layouts/footer.php'; // Juga di-handle oleh layout utama ?>