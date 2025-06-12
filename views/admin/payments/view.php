<?php
// views/admin/payments/view.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>

    <div class="admin-content">
    <h2 class="mb-3">Detail Pembayaran</h2>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"> <?= $_SESSION['success']; unset($_SESSION['success']); ?> </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
    <?php endif; ?>
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Info Transaksi</div>
        <div class="card-body row">
            <div class="col-md-6">
                <table class="table table-borderless mb-0">
                    <tr><th>ID Transaksi</th><td><?= htmlspecialchars($payment->transaction_id) ?></td></tr>
                    <tr><th>Booking</th><td><a href="/admin/bookings/view/<?= $payment->booking_id ?>">#<?= $payment->booking_id ?></a></td></tr>
                    <tr><th>Metode</th><td><?= htmlspecialchars($payment->payment_method) ?></td></tr>
                    <tr><th>Jumlah</th><td>Rp <?= number_format($payment->amount, 0, ',', '.') ?></td></tr>
                    <tr><th>Status</th><td>
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
                    <tr><th>Tanggal</th><td><?= date('d M Y H:i', strtotime($payment->payment_date)) ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <?php if (!empty($payment->proof_file)): ?>
                    <div class="mb-2"><strong>Bukti Pembayaran:</strong></div>
                    <img src="<?= htmlspecialchars($payment->proof_file) ?>" alt="Bukti Pembayaran" class="img-fluid rounded border" style="max-width:300px;">
                <?php else: ?>
                    <div class="text-muted">Tidak ada bukti pembayaran.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <a href="<?php echo APP_URL; ?>/admin/payments/updatestatus/<?= $payment->payment_id ?>" class="btn btn-warning">Update Status</a>
    <a href="<?php echo APP_URL; ?>/admin/payments" class="btn btn-secondary ms-2">Kembali</a>
    </div>
</div>
<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
