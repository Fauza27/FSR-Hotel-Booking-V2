<?php
// views/admin/rooms/delete.php
// Data yang diharapkan: $room, $roomFacilities, $roomImages, $activeBookings
?>
<div class="admin-content">
    <h2>Konfirmasi Hapus Kamar</h2>
    <div class="room-delete-detail" style="margin-bottom: 24px;">
        <h4>Detail Kamar</h4>
        <div style="display: flex; gap: 24px; align-items: flex-start;">
            <div>
                <?php if (!empty($roomImages)): ?>
                    <img src="<?= APP_URL . '/assets/images/rooms/' . $roomImages[0]['image_url']; ?>" alt="<?= htmlspecialchars($room['room_number']); ?>" style="width: 180px; border-radius: 8px; border: 1px solid #eee;">
                <?php else: ?>
                    <img src="<?= APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="No Image" style="width: 180px; border-radius: 8px; border: 1px solid #eee;">
                <?php endif; ?>
            </div>
            <div>
                <table class="table table-bordered" style="width: auto;">
                    <tr><th>Nomor Kamar</th><td><?= htmlspecialchars($room['room_number']); ?></td></tr>
                    <tr><th>Kategori</th><td><?= htmlspecialchars($room['category_name']); ?></td></tr>
                    <tr><th>Harga/Malam</th><td>Rp <?= number_format($room['price_per_night'], 0, ',', '.'); ?></td></tr>
                    <tr><th>Kapasitas</th><td><?= htmlspecialchars($room['capacity']); ?> org</td></tr>
                    <tr><th>Ukuran</th><td><?= htmlspecialchars($room['size_sqm']); ?> m<sup>2</sup></td></tr>
                    <tr><th>Fasilitas</th><td><?php if (!empty($roomFacilities)) { foreach ($roomFacilities as $f) { echo '<span class="badge badge-info" style="margin-right:4px;">' . htmlspecialchars($f['name']) . '</span>'; } } else { echo '-'; } ?></td></tr>
                    <tr><th>Status</th><td><span class="badge badge-<?= $room['status'] == 'available' ? 'success' : ($room['status'] == 'occupied' ? 'warning' : 'secondary'); ?>"><?= ucfirst($room['status']); ?></span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <?php if (!empty($activeBookings)): ?>
        <div class="alert alert-warning">
            <strong>Peringatan!</strong> Kamar ini memiliki <b><?= count($activeBookings); ?></b> booking aktif (pending/confirmed). Menghapus kamar dapat menyebabkan data booking bermasalah.<br>
            <ul style="margin-top:8px;">
                <?php foreach ($activeBookings as $booking): ?>
                    <li>Booking #<?= $booking['booking_id']; ?>, Tamu: <?= htmlspecialchars($booking['user_id']); ?>, Check-in: <?= date('d M Y', strtotime($booking['check_in_date'])); ?>, Status: <span class="badge badge-<?= $booking['status']; ?>"><?= ucfirst($booking['status']); ?></span></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="" method="post" style="margin-top: 24px;">
        <input type="hidden" name="confirm_delete" value="1">
        <div style="display: flex; gap: 16px;">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus kamar ini? Semua data terkait akan dihapus!')">Ya, Hapus Kamar</button>
            <a href="/admin/rooms" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
