<?php
// views/admin/payments/update_status.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>
    <div class="admin-content">
        <h2 class="mb-3">Update Status Pembayaran</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold">Info Pembayaran</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th>ID Transaksi</th><td><?= htmlspecialchars($payment->transaction_id) ?></td></tr>
                    <tr><th>Booking</th><td><a href="/admin/bookings/view/<?= $payment->booking_id ?>">#<?= $payment->booking_id ?></a></td></tr>
                    <tr><th>Metode</th><td><?= htmlspecialchars($payment->payment_method) ?></td></tr>
                    <tr><th>Jumlah</th><td>Rp <?= number_format($payment->amount, 0, ',', '.') ?></td></tr>
                    <tr><th>Status Saat Ini</th><td>
                        <?php
                        $status = $payment->payment_status;
                        $badge = 'secondary';
                        if ($status === 'completed') $badge = 'success';
                        elseif ($status === 'pending') $badge = 'warning';
                        elseif ($status === 'failed') $badge = 'danger';
                        elseif ($status === 'refunded') $badge = 'info';
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= ucfirst($status) ?></span>
                    </td></tr>
                </table>
            </div>
        </div>
        <form method="post" action="">
            <div class="mb-3">
                <label for="status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="pending" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="failed" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'failed') ? 'selected' : '' ?>>Failed</option>
                    <option value="refunded" <?= (isset($_GET['payment_status']) && $_GET['payment_status'] == 'refunded') ? 'selected' : '' ?>>Refunded</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
            <a href="<?= APP_URL ?>/admin/payments/view/<?= $payment->payment_id ?>" class="btn btn-secondary ms-2">Batal</a>
        </form>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
