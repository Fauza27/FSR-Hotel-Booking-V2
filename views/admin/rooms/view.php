<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>
<?php
// views/admin/rooms/view.php
// Data yang diharapkan: $room, $roomFacilities, $roomImages, $roomBookings
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">

        <a href="<?= APP_URL ?>/admin/rooms" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
        </a>

        <div class="page-header">
            <h2 class="page-title">Detail Kamar</h2>
        </div>
        
        <div class="room-detail-section">
            <!-- Gallery Gambar -->
            <div class="room-gallery-section">
                <h4 class="section-title"><i class="fas fa-images"></i> Gallery Gambar</h4>
                <div class="room-gallery">
                <?php if (!empty($roomImages)): ?>
                    <?php foreach ($roomImages as $img): ?>
                        <img src="<?= APP_URL . '/assets/images/rooms/' . $img->image_url; ?>" alt="Gambar Kamar" style="width: 120px; height: 80px; object-fit:cover; border-radius: 6px; border:1px solid #eee;" />
                    <?php endforeach; ?>                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-image"></i>
                        <p>Belum ada gambar untuk kamar ini</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>        
        <!-- Detail Kamar -->        
         <div class="room-info">
            <h4 class="section-title"><i class="fas fa-info-circle"></i> Informasi Kamar</h4>
            <div class="table-responsive">
            <table class="room-info-table"><tr><th>Nomor Kamar</th><td><?= htmlspecialchars($room->room_number); ?></td></tr>
                <tr><th>Kategori</th><td><?= htmlspecialchars($room->category_name); ?></td></tr>
                <tr><th>Harga/Malam</th><td>Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></td></tr>
                <tr><th>Kapasitas</th><td><?= htmlspecialchars($room->capacity); ?> org</td></tr>
                <tr><th>Ukuran</th><td><?= htmlspecialchars($room->size_sqm); ?> m<sup>2</sup></td></tr>
                <tr><th>Status</th><td><span class="badge badge-<?= $room->status == 'available' ? 'success' : ($room->status == 'occupied' ? 'warning' : 'secondary'); ?>">
                    <i class="fas fa-<?= $room->status == 'available' ? 'check-circle' : ($room->status == 'occupied' ? 'clock' : 'tools'); ?>"></i>
                    <?= ucfirst($room->status); ?>
                </span></td></tr>
                <tr><th>Deskripsi</th><td><?= nl2br(htmlspecialchars($room->description)); ?></td></tr>
            </table>
            <a href="<?= APP_URL ?>/admin/rooms/edit/<?= $room->room_id; ?>" class="btn btn-warning">Edit Kamar</a>
        </div>
    </div>    <!-- List Fasilitas -->
    <div class="room-detail-section">
        <h4 class="section-title"><i class="fas fa-concierge-bell"></i> Fasilitas Kamar</h4>
        <?php if (!empty($roomFacilities)): ?>
            <div class="facilities-list">
                <?php foreach ($roomFacilities as $f): ?>
                    <span class="facility-badge"><i class="fas fa-check"></i> <?= htmlspecialchars($f->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-concierge-bell"></i>
                <p>Tidak ada fasilitas khusus untuk kamar ini</p>
            </div>
        <?php endif; ?>
    </div>    <!-- History Booking -->
    <div class="room-detail-section">
        <h4 class="section-title"><i class="fas fa-history"></i> History Booking Kamar Ini</h4>
        <?php if (!empty($roomBookings)): ?>
            <div style="overflow-x:auto;">
            <table class="booking-history-table">
                <thead>
                    <tr>                        <th><i class="fas fa-hashtag"></i> ID Booking</th>
                        <th><i class="fas fa-user"></i> Nama Tamu</th>
                        <th><i class="fas fa-calendar-check"></i> Check-in</th>
                        <th><i class="fas fa-calendar-minus"></i> Check-out</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-money-bill-wave"></i> Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roomBookings as $b): ?>                        <tr>
                            <td><?= $b->booking_id; ?></td>
                            <td><?= htmlspecialchars($b->user_name ?? $b->user_id); ?></td>
                            <td><?= date('d M Y', strtotime($b->check_in_date)); ?></td>
                            <td><?= date('d M Y', strtotime($b->check_out_date)); ?></td>
                            <td><span class="badge badge-<?= $b->status; ?>">
                                <i class="fas fa-<?= $b->status == 'pending' ? 'clock' : 
                                    ($b->status == 'confirmed' ? 'check-circle' : 
                                    ($b->status == 'cancelled' ? 'times-circle' : 'check-double')); ?>"></i>
                                <?= ucfirst($b->status); ?>
                            </span></td>
                            <td>Rp <?= number_format($b->total_price, 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>Belum ada history booking untuk kamar ini</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>