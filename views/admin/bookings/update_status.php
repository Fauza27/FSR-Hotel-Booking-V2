<?php
// views/admin/bookings/update_status.php
// Data yang diharapkan: $booking, $currentStatus, $note
?>
<div class="admin-content">
    <h2>Update Status Booking #<?= $booking['booking_id']; ?></h2>
    <form action="" method="post" style="max-width: 480px; margin-bottom: 32px;">
        <div class="form-group">
            <label for="status">Status Baru</label>
            <select name="status" id="status" class="form-control" required>
                <option value="pending" <?= $currentStatus==='pending'?'selected':''; ?>>Pending</option>
                <option value="confirmed" <?= $currentStatus==='confirmed'?'selected':''; ?>>Confirmed</option>
                <option value="completed" <?= $currentStatus==='completed'?'selected':''; ?>>Completed</option>
                <option value="cancelled" <?= $currentStatus==='cancelled'?'selected':''; ?>>Cancelled</option>
            </select>
        </div>
        <div class="form-group">
            <label for="note">Catatan/Alasan</label>
            <textarea name="note" id="note" class="form-control" rows="3" placeholder="Opsional, misal alasan pembatalan atau catatan perubahan."><?= htmlspecialchars($note ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Status</button>
        <a href="/admin/bookings/view/<?= $booking['booking_id']; ?>" class="btn btn-secondary">Batal</a>
    </form>
    <div style="margin-top:32px;">
        <h4>Preview Detail Booking</h4>
        <table class="table table-bordered" style="max-width:600px;">
            <tr><th>Nama Tamu</th><td><?= htmlspecialchars($booking['user_name'] ?? $booking['user_id']); ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($booking['user_email'] ?? '-'); ?></td></tr>
            <tr><th>Nomor Kamar</th><td><?= htmlspecialchars($booking['room_number']); ?></td></tr>
            <tr><th>Check-in</th><td><?= date('d M Y', strtotime($booking['check_in_date'])); ?></td></tr>
            <tr><th>Check-out</th><td><?= date('d M Y', strtotime($booking['check_out_date'])); ?></td></tr>
            <tr><th>Total</th><td>Rp <?= number_format($booking['total_price'], 0, ',', '.'); ?></td></tr>
            <tr><th>Status Saat Ini</th><td><span class="badge badge-<?= $currentStatus; ?>"> <?= ucfirst($currentStatus); ?> </span></td></tr>
        </table>
    </div>
</div>
