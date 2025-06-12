<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>

    <div class="admin-content">
        <div class="page-header">
            <h2 class="page-title">Edit Kamar</h2>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/rooms/update/' . $room->room_id); ?>" method="post" enctype="multipart/form-data" id="roomEditForm">
            
            <a href="<?= base_url('admin/rooms') ?>" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <div class="form-group">
                <label for="room_number">Nomor Kamar</label>
                <input type="text" name="room_number" id="room_number" class="form-control" value="<?php echo htmlspecialchars($room->room_number ?? ''); ?>" required />
            </div>
            <div class="form-group">
                <label for="category_id">Kategori</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat->category_id; ?>" <?php if(isset($room->category_id) && $room->category_id == $cat->category_id) echo 'selected'; ?>><?php echo htmlspecialchars($cat->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price_per_night">Harga per Malam</label>
                <input type="number" name="price_per_night" id="price_per_night" class="form-control" min="1" value="<?php echo htmlspecialchars($room->price_per_night ?? ''); ?>" required />
            </div>
            <div class="form-group">
                <label for="capacity">Kapasitas (Orang)</label>
                <input type="number" name="capacity" id="capacity" class="form-control" min="1" value="<?php echo htmlspecialchars($room->capacity ?? ''); ?>" required />
            </div>
            <div class="form-group">
                <label for="size_sqm">Ukuran (m<sup>2</sup>)</label>
                <input type="number" step="0.01" name="size_sqm" id="size_sqm" class="form-control" min="1" value="<?php echo htmlspecialchars($room->size_sqm ?? ''); ?>" required />
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" class="form-control" rows="3" required><?php echo htmlspecialchars($room->description ?? ''); ?></textarea>
            </div>        
            
            <h3 class="form-section-title">Gambar Kamar</h3>
            <div class="form-group">
                <label>Gambar Kamar Saat Ini</label>
                <div class="current-images">
                    <?php if (!empty($roomImages)): ?>
                        <?php foreach ($roomImages as $img): ?>
                            <div class="image-item" style="display: inline-block; margin: 5px; text-align: center; border: 1px solid #ddd; padding: 5px; border-radius: 4px; position: relative;">
                                <img src="<?= APP_URL . '/assets/images/rooms/' . $img->image_url; ?>" alt="Gambar Kamar" style="width: 120px; height: 80px; object-fit:cover; border-radius: 6px; border:1px solid #eee;" />
                                <br>
                                <?php if (!$img->is_primary): ?>
                                <a href="<?= base_url('admin/rooms/set-primary-image/' . $img->image_id . '/' . $room->room_id) ?>" 
                                   class="btn btn-sm btn-outline-success" title="Jadikan Utama" onclick="return confirm('Jadikan gambar ini sebagai gambar utama?')">
                                   <i class="fas fa-check-circle"></i> Utama
                                </a>
                                <?php else: ?>
                                <span class="badge badge-success"><i class="fas fa-star"></i> Utama</span>
                                <?php endif; ?>
                                <a href="<?= base_url('admin/rooms/delete-image/' . $img->image_id) ?>" 
                                   onclick="return confirm('Hapus gambar ini? Ini tidak bisa dibatalkan.')" 
                                   class="btn btn-sm btn-outline-danger" title="Hapus Gambar" style="position: absolute; top: -5px; right: -5px; background: white; border-radius: 50%;">
                                   ×
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Belum ada gambar untuk kamar ini.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="images">Upload Gambar Baru (bisa lebih dari satu, gambar pertama yang diupload akan menjadi utama jika belum ada)</label>
                <input type="file" name="images[]" id="images" class="form-control" accept="image/*" multiple />
                <small class="form-text text-muted">File yang diizinkan: JPG, JPEG, PNG, GIF. Maksimum: <?= MAX_FILE_SIZE / 1024 / 1024 ?> MB per file.</small>
            </div>        
            
            <h3 class="form-section-title">Fasilitas Kamar</h3>
            <div class="form-group">
                <label>Pilih Fasilitas (minimal satu)</label>
                <div class="facility-checkboxes">
                    <?php 
                        // $roomFacilityIds sudah disiapkan di controller
                    ?>
                    <?php foreach ($facilities as $f): ?>
                        <div class="facility-option" style="display: inline-block; margin-right: 15px;">
                            <input type="checkbox" 
                                id="facility_<?php echo $f->facility_id; ?>"  
                                name="facilities[]" 
                                value="<?php echo $f->facility_id; ?>"  
                                <?php if (is_array($roomFacilityIds) && in_array($f->facility_id, $roomFacilityIds)) echo 'checked'; ?> />
                            <label for="facility_<?php echo $f->facility_id; ?>">
                                <?php echo htmlspecialchars($f->name); ?> 
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="available" <?php if(isset($room->status) && $room->status=='available') echo 'selected'; ?>>Tersedia</option>
                    <option value="occupied" <?php if(isset($room->status) && $room->status=='occupied') echo 'selected'; ?>>Terisi</option>
                    <option value="maintenance" <?php if(isset($room->status) && $room->status=='maintenance') echo 'selected'; ?>>Perbaikan</option>
                </select>
            </div>
            <div class="form-buttons mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="<?= base_url('admin/rooms') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>    
        </form>
    </div>
</div>
<script>
// Validasi client-side sederhana
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('roomEditForm');
    if (form) {
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
                if (!el.value.trim()) {
                    el.classList.add('is-invalid');
                    errors.push(field.name + ' wajib diisi.');
                    valid = false;
                } else {
                    el.classList.remove('is-invalid');
                }
            });
            
            const facilityChecked = document.querySelectorAll('input[name="facilities[]"]:checked').length;
            if (facilityChecked === 0) {
                errors.push('Pilih minimal satu fasilitas!');
                valid = false;
            }

            // Cek apakah ada gambar sama sekali (existing atau baru)
            // Ini lebih kompleks, untuk sekarang validasi server cukup
            // const existingImages = document.querySelectorAll('.current-images .image-item').length;
            // const newImages = document.getElementById('images').files.length;
            // if (existingImages === 0 && newImages === 0) {
            //     errors.push('Minimal satu gambar harus ada (bisa dari yang sudah ada atau upload baru).');
            //     valid = false;
            // }


            if (!valid) {
                e.preventDefault();
                // Tampilkan error (opsional, bisa pakai alert atau elemen di halaman)
                let errorMsg = "Harap perbaiki kesalahan berikut:\n";
                errors.forEach(err => errorMsg += "- " + err + "\n");
                alert(errorMsg);
            }
        });
    }
});
</script>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>