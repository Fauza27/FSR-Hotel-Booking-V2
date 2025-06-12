<?php
// views/admin/users/block.php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';
?>
<div class="container mt-4">
    <h2 class="mb-3">Blokir User</h2>
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Preview User</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th>Nama Lengkap</th><td><?= htmlspecialchars($user->full_name) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($user->email) ?></td></tr>
                <tr><th>Username</th><td><?= htmlspecialchars($user->username) ?></td></tr>
                <tr><th>Telepon</th><td><?= htmlspecialchars($user->phone) ?></td></tr>
                <tr><th>Status</th><td>
                    <?php if ($user->status === 'active'): ?>
                        <span class="badge bg-success">Aktif</span>
                    <?php elseif ($user->status === 'blocked'): ?>
                        <span class="badge bg-danger">Blocked</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($user->status) ?></span>
                    <?php endif; ?>
                </td></tr>
            </table>
        </div>
    </div>
    <form method="post" action="">
        <div class="mb-3">
            <label for="reason" class="form-label">Alasan Blokir <span class="text-danger">*</span></label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="Tulis alasan pemblokiran user ini..."></textarea>
        </div>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin memblokir user ini?')">Konfirmasi Blokir</button>
        <a href="/admin/users/view?id=<?= $user->user_id ?>" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
