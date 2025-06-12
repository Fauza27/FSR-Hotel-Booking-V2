<?php
// views/admin/users/view.php
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include_once(VIEW_PATH . 'admin/layouts/sidebar.php'); ?>
    </div>

    <div class="admin-content">
        <h2 class="mb-3">Detail User</h2>

        <!-- Flash messages -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"> <?= $_SESSION['success']; unset($_SESSION['success']); ?> </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"> <?= $_SESSION['error']; unset($_SESSION['error']); ?> </div>
        <?php endif; ?>

        <!-- User Profile Card -->
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold">Profil User</div>
            <div class="card-body row">
                <!-- Left Column - User Details -->
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr><th>Nama Lengkap</th><td><?= htmlspecialchars($user->full_name) ?></td></tr>
                        <tr><th>Email</th><td><?= htmlspecialchars($user->email) ?></td></tr>
                        <tr><th>Username</th><td><?= htmlspecialchars($user->username) ?></td></tr>
                        <tr><th>Telepon</th><td><?= htmlspecialchars($user->phone) ?></td></tr>
                        <tr><th>Alamat</th><td><?= htmlspecialchars($user->address) ?></td></tr>
                        <tr><th>Role</th><td><?= htmlspecialchars($user->role) ?></td></tr>
                    </table>
                </div>

                <!-- Right Column - Actions -->
                <div class="col-md-6 d-flex align-items-center">
                    <?php if (empty($user->role) || $user->role !== 'admin'): ?>
                        <a href="<?= APP_URL ?>/admin/users/make-admin/<?= htmlspecialchars($user->user_id) ?>" 
                           class="btn btn-secondary ms-2" 
                           onclick="return confirm('Jadikan user ini sebagai admin?')">
                            Jadikan Admin
                        </a>
                    <?php else: ?>
                        <span class="badge bg-info ms-2">Admin</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking History Card -->
        <div class="card mb-4">
            <div class="card-header bg-light fw-bold">History Booking</div>
            <div class="card-body">
                <?php
                $totalSpending = 0;
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Booking</th>
                                <th>Kamar</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Total Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bookings)): ?>
                                <?php $no=1; foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>#<?= $booking->booking_id ?></td>
                                        <td><?= htmlspecialchars($booking->room_number ?? $booking->room_id) ?></td>
                                        <td><?= date('d M Y', strtotime($booking->check_in_date)) ?></td>
                                        <td><?= date('d M Y', strtotime($booking->check_out_date)) ?></td>
                                        <td>
                                            <?php if ($booking->status === 'confirmed'): ?>
                                                <span class="badge bg-success">Confirmed</span>
                                            <?php elseif ($booking->status === 'cancelled'): ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($booking->status) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Rp <?= number_format($booking->total_price, 0, ',', '.') ?></td>
                                    </tr>
                                    <?php $totalSpending += $booking->total_price; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">Belum ada booking.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <strong>Total Spending:</strong> <span class="fs-5 text-primary">Rp <?= number_format($totalSpending, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <a href="<?php echo APP_URL ?>/admin/users" class="btn btn-secondary">Kembali ke Daftar User</a>
    </div>
</div>

<?php include_once(VIEW_PATH . 'admin/layouts/footer.php'); ?>
