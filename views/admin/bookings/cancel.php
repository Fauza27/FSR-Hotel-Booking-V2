<?php
// views/admin/bookings/cancel.php
// Data yang diharapkan: $booking, $payment
include_once(VIEW_PATH . 'admin/layouts/header.php');
include_once(VIEW_PATH . 'admin/layouts/sidebar.php');
?>
<div class="admin-content">
    <h2>Batalkan Booking #<?= $booking['booking_id']; ?></h2>
    <form action="" method="post" style="max-width: 520px; margin-bottom: 32px;">
        <div class="alert alert-warning">
            <strong>Konfirmasi Pembatalan:</strong> Anda yakin ingin membatalkan booking ini?
        </div>
        <h4>Detail Booking</h4>
        <table class="table table-bordered">
            <tr><th>Nama Tamu</th><td><?= htmlspecialchars($booking['user_name'] ?? $booking['user_id']); ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($booking['user_email'] ?? '-'); ?></td></tr>
            <tr><th>Nomor Kamar</th><td><?= htmlspecialchars($booking['room_number']); ?></td></tr>
            <tr><th>Check-in</th><td><?= date('d M Y', strtotime($booking['check_in_date'])); ?></td></tr>
            <tr><th>Check-out</th><td><?= date('d M Y', strtotime($booking['check_out_date'])); ?></td></tr>
            <tr><th>Total</th><td>Rp <?= number_format($booking['total_price'], 0, ',', '.'); ?></td></tr>
            <tr><th>Status Saat Ini</th><td><span class="badge badge-<?= $booking['status']; ?>"> <?= ucfirst($booking['status']); ?> </span></td></tr>
        </table>
        <h4>Status Pembayaran & Refund</h4>
        <?php if (!empty($payment)): ?>
            <table class="table table-bordered">
                <tr><th>Metode</th><td><?= htmlspecialchars($payment['payment_method']); ?></td></tr>
                <tr><th>Status</th><td><span class="badge badge-<?= $payment['payment_status']; ?>"> <?= ucfirst($payment['payment_status']); ?> </span></td></tr>
                <tr><th>Jumlah</th><td>Rp <?= number_format($payment['amount'], 0, ',', '.'); ?></td></tr>
                <tr><th>Tanggal</th><td><?= date('d M Y H:i', strtotime($payment['payment_date'])); ?></td></tr>
                <tr><th>Transaksi</th><td><?= htmlspecialchars($payment['transaction_id']); ?></td></tr>
                <?php if ($payment['payment_status'] === 'completed'): ?>
                    <tr><th>Info Refund</th><td><span class="text-success">Refund akan diproses sesuai kebijakan hotel.</span></td></tr>
                <?php elseif ($payment['payment_status'] === 'refunded'): ?>
                    <tr><th>Info Refund</th><td><span class="text-info">Pembayaran sudah direfund.</span></td></tr>
                <?php else: ?>
                    <tr><th>Info Refund</th><td><span class="text-muted">Tidak ada pembayaran/refund.</span></td></tr>
                <?php endif; ?>
            </table>
        <?php else: ?>
            <p><i>Belum ada pembayaran.</i></p>
        <?php endif; ?>
        <div class="form-group mt-3">
            <label for="reason">Alasan Pembatalan <span style="color:#d00;">*</span></label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="Tuliskan alasan pembatalan..."></textarea>
        </div>
        <div class="mt-4" style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin membatalkan booking ini?')">Ya, Batalkan Booking</button>
            <a href="/admin/bookings/view/<?= $booking['booking_id']; ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
