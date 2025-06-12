<?php include_once(VIEW_PATH . 'admin/layouts/header.php'); ?>
<?php
// views/admin/bookings/view.php
// Data yang diharapkan: $booking, $payment, $statusHistory
?>
<div class="admin-container">
    <div class="admin-sidebar">
            <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div> 
    <div class="admin-content">
        <h2>Detail Booking #<?= $booking->booking_id; ?></h2>
        <div style="display: flex; gap: 32px; align-items: flex-start; margin-bottom: 32px;">
            <!-- Info User -->
            <div style="min-width:260px;">
                <h4>Info User</h4>
                <table class="table table-bordered">
                    <tr><th>Nama</th><td><?= htmlspecialchars($booking->user_name ?? $booking->user_id); ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($booking->user_email ?? '-'); ?></td></tr>
                    <tr><th>Telepon</th><td><?= htmlspecialchars($booking->user_phone ?? '-'); ?></td></tr>
                </table>
            </div>
            <!-- Info Kamar -->
            <div style="min-width:260px;">
                <h4>Info Kamar</h4>
                <table class="table table-bordered">
                    <tr><th>Nomor</th><td><?= htmlspecialchars($booking->room_number); ?></td></tr>
                    <tr><th>Harga/Malam</th><td>Rp <?= number_format($booking->price_per_night, 0, ',', '.'); ?></td></tr>
                    <tr><th>Check-in</th><td><?= date('d M Y', strtotime($booking->check_in_date)); ?></td></tr>
                    <tr><th>Check-out</th><td><?= date('d M Y', strtotime($booking->check_out_date)); ?></td></tr>
                    <tr><th>Total</th><td>Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></td></tr>
                </table>
            </div>
            <!-- Status & Aksi -->
            <div style="flex:1;">
                <h4>Status Booking</h4>
                <p>Status: <span class="badge badge-<?= $booking->status; ?>" style="font-size:16px;"> <?= ucfirst($booking->status); ?> </span></p>
                <form action="/admin/bookings/update-status/<?= $booking->booking_id; ?>" method="post" style="margin-bottom:16px;">
                    <label>Update Status:</label>
                    <select name="status" class="form-control" style="width:auto;display:inline-block;">
                        <option value="pending" <?= $booking->status==='pending'?'selected':''; ?>>Pending</option>
                        <option value="confirmed" <?= $booking->status==='confirmed'?'selected':''; ?>>Confirmed</option>
                        <option value="completed" <?= $booking->status==='completed'?'selected':''; ?>>Completed</option>
                        <option value="cancelled" <?= $booking->status==='cancelled'?'selected':''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
                <h4>Status Pembayaran</h4>
                <?php if (!empty($payment)): ?>
                    <table class="table table-bordered">
                        <tr><th>Metode</th><td><?= htmlspecialchars($payment->payment_method); ?></td></tr>
                        <tr><th>Status</th><td><span class="badge badge-<?= $payment->payment_status; ?>"> <?= ucfirst($payment->payment_status); ?> </span></td></tr>
                        <tr><th>Jumlah</th><td>Rp <?= number_format($payment->amount, 0, ',', '.'); ?></td></tr>
                        <tr><th>Tanggal</th><td><?= date('d M Y H:i', strtotime($payment->payment_date)); ?></td></tr>
                        <tr><th>Transaksi</th><td><?= htmlspecialchars($payment->transaction_id); ?></td></tr>
                    </table>
                <?php else: ?>
                    <p><i>Belum ada pembayaran.</i></p>
                <?php endif; ?>
            </div>
        </div>
        <!-- History Perubahan Status -->
        <div style="margin-top:32px;">
            <h4>History Perubahan Status</h4>
            <?php if (!empty($statusHistory)): ?>
                <table class="table table-bordered">
                    <thead><tr><th>Status</th><th>Waktu</th></tr></thead>
                    <tbody>
                        <?php foreach ($statusHistory as $h): ?>
                            <tr>
                                <td><span class="badge badge-<?= $h->status; ?>"> <?= ucfirst($h->status); ?> </span></td>
                                <td><?= date('d M Y H:i', strtotime($h->updated_at)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><i>Belum ada history perubahan status.</i></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo APP_URL; ?>/admin/bookings" class="btn btn-secondary">Kembali ke Daftar Booking</a>
    </div>
</div>