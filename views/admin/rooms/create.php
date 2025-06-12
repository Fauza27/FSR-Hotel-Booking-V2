<?php
// views/admin/rooms/create.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2>Tambah Kamar Baru</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('admin/rooms/store') ?>" method="post" enctype="multipart/form-data" id="roomForm">
            <div class="form-group">
                <label for="room_number">Nomor Kamar</label>
                <input type="text" name="room_number" id="room_number" class="form-control" value="<?= htmlspecialchars($_POST['room_number'] ?? '') ?>" required />
            </div>
            <div class="form-group">
                <label for="category_id">Kategori</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat->category_id; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat->category_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price_per_night">Harga per Malam (Rp)</label>
                <input type="number" name="price_per_night" id="price_per_night" class="form-control" min="1" value="<?= htmlspecialchars($_POST['price_per_night'] ?? '') ?>" required />
            </div>
            <div class="form-group">
                <label for="capacity">Kapasitas (Orang)</label>
                <input type="number" name="capacity" id="capacity" class="form-control" min="1" value="<?= htmlspecialchars($_POST['capacity'] ?? '') ?>" required />
            </div>
            <div class="form-group">
                <label for="size_sqm">Ukuran (m<sup>2</sup>)</label>
                <input type="number" name="size_sqm" id="size_sqm" step="0.01" class="form-control" min="1" value="<?= htmlspecialchars($_POST['size_sqm'] ?? '') ?>" required />
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="3" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="images">Upload Gambar (bisa lebih dari satu, gambar pertama akan jadi utama)</label>
                <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple />
                <small>Ekstensi yang diizinkan: <?= implode(', ', ALLOWED_EXTENSIONS) ?>. Ukuran maks: <?= MAX_FILE_SIZE / 1024 / 1024 ?> MB per file.</small>
            </div>
            <div class="form-group">
                <label>Fasilitas</label>
                <div class="facility-checkboxes" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php 
                    $selectedFacilities = $_POST['facilities'] ?? [];
                    foreach ($facilities as $f): ?>
                        <label style="min-width: 150px;">
                            <input type="checkbox" name="facilities[]" value="<?php echo $f->facility_id; ?>" <?php echo in_array($f->facility_id, $selectedFacilities) ? 'checked' : ''; ?> />
                            <?php echo htmlspecialchars($f->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="available" <?php echo (isset($_POST['status']) && $_POST['status'] == 'available') ? 'selected' : ''; ?>>Tersedia</option>
                    <option value="occupied" <?php echo (isset($_POST['status']) && $_POST['status'] == 'occupied') ? 'selected' : ''; ?>>Terisi</option>
                    <option value="maintenance" <?php echo (isset($_POST['status']) && $_POST['status'] == 'maintenance') ? 'selected' : ''; ?>>Perbaikan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/rooms') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
<script>
// Validasi client-side sederhana (bisa ditingkatkan)
const form = document.getElementById('roomForm');
form.addEventListener('submit', function(e) {
    let valid = true;
    const errors = [];

    const requiredFields = [
        { id: 'room_number', name: 'Nomor Kamar' },
        { id: 'category_id', name: 'Kategori' },
        { id: 'price_per_night', name: 'Harga per Malam' },
        { id: 'capacity', name: 'Kapasitas' },
        { id: 'size_sqm', name: 'Ukuran' },
        { id: 'description', name: 'Deskripsi' },
        { id: 'status', name: 'Status' }
    ];

    requiredFields.forEach(function(field) {
        const el = document.getElementById(field.id);
        if (!el.value) {
            el.classList.add('is-invalid');
            errors.push(field.name + ' wajib diisi.');
            valid = false;
        } else {
            el.classList.remove('is-invalid');
        }
    });

    const imgInput = document.getElementById('images');
    if (!imgInput.files || imgInput.files.length === 0) {
        // Cek apakah ini form edit dan sudah ada gambar sebelumnya (jika iya, tidak wajib upload baru)
        // Untuk form create, gambar wajib
        // Untuk simplifikasi, kita anggap wajib di create.php
        imgInput.classList.add('is-invalid');
        errors.push('Minimal satu gambar wajib diupload.');
        valid = false;
    } else {
        imgInput.classList.remove('is-invalid');
        // Validasi ekstensi dan ukuran di client-side bisa ditambahkan di sini
        for (let i = 0; i < imgInput.files.length; i++) {
            const file = imgInput.files[i];
            const allowedExt = <?= json_encode(ALLOWED_EXTENSIONS) ?>;
            const maxFileSize = <?= MAX_FILE_SIZE ?>;
            const fileExt = file.name.split('.').pop().toLowerCase();

            if (!allowedExt.includes(fileExt)) {
                errors.push('Ekstensi file "' + file.name + '" tidak diizinkan.');
                valid = false;
            }
            if (file.size > maxFileSize) {
                errors.push('Ukuran file "' + file.name + '" terlalu besar.');
                valid = false;
            }
        }
    }

    const facilityChecked = document.querySelectorAll('input[name="facilities[]"]:checked').length;
    if (facilityChecked === 0) {
        errors.push('Pilih minimal satu fasilitas!');
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
        // Tampilkan error (misalnya menggunakan alert atau elemen div khusus)
        alert("Terdapat error pada input:\n" + errors.join("\n"));
    }
});
</script>
<?php require VIEW_PATH . 'admin/layouts/footer.php'; // Gunakan VIEW_PATH ?>