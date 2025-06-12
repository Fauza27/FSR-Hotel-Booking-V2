<?php
$pageTitle = 'My Booking History';
$currentPage = 'profile';
$activeMenu = 'booking_history';
include_once(VIEW_PATH . 'layouts/header.php');
?>
<!-- Layout Utama: Grid, responsif (menumpuk di mobile, 2 kolom di desktop) -->
<div class="container my-8 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
    
    <!-- Sidebar Profil -->
    <aside class="bg-darker-bg p-6 rounded-lg h-fit">
        <div class="mx-auto mb-6">
            <?php
            $avatar_path = asset_url('images/user-placeholder.png');
            if (!empty($user->avatar) && file_exists(AVATAR_UPLOAD_PATH . $user->avatar)) {
                $avatar_path = asset_url(AVATAR_UPLOAD_DIR . $user->avatar);
            }
            ?>
            <!-- Avatar dengan style Tailwind -->
            <img src="<?= htmlspecialchars($avatar_path); ?>" alt="Avatar" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-accent">
        </div>
        <div class="text-center mb-6">
            <h4 class="text-xl font-bold"><?= htmlspecialchars($user->full_name); ?></h4>
        </div>
        <!-- Menu Navigasi Sidebar Profil -->
        <nav class="flex flex-col space-y-2">
            <a href="<?= base_url('profile'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'profile' ? 'active' : ''; ?>">Profile</a>
            <a href="<?= base_url('profile/bookings'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'booking_history' ? 'active' : ''; ?>">Booking History</a>
            <a href="<?= base_url('profile/edit'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'edit' ? 'active' : ''; ?>">Edit Profile</a>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <section class="bg-darker-bg p-6 lg:p-8 rounded-lg">
        <h2 class="text-2xl font-bold mb-6">My Booking History</h2>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <!-- Komponen Alert dari input.css -->
            <div class="alert alert-<?= $_SESSION['flash_type']; ?>">
                <span><?= $_SESSION['flash_message']; ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if (!isset($bookings)): ?>
                <div class="alert alert-danger">Booking data could not be loaded.</div>
            <?php elseif (empty($bookings)): ?>
                <div class="alert alert-info">You have no bookings yet. <a href="<?= base_url('rooms'); ?>" class="font-semibold underline">Find a room to book.</a></div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <!-- Kartu Riwayat Booking -->
                    <div class="bg-dark-bg p-5 rounded-lg border-l-4 border-primary shadow-md">
                        <!-- Header Kartu: Responsif -->
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-4">
                            <h4 class="text-lg font-semibold">Booking #<?= htmlspecialchars($booking->booking_id); ?></h4>
                            <?php $status_class = 'bg-info/20 text-info'; // Default
                                if (strtolower($booking->status) === 'pending') $status_class = 'bg-warning/20 text-warning';
                                if (strtolower($booking->status) === 'confirmed') $status_class = 'bg-success/20 text-success';
                                if (strtolower($booking->status) === 'cancelled') $status_class = 'bg-error/20 text-error';
                            ?>
                            <div class="px-3 py-1 rounded-full text-xs font-medium <?= $status_class; ?> self-start sm:self-center">
                                <?= htmlspecialchars(ucfirst($booking->status)); ?>
                            </div>
                        </div>

                        <!-- Detail Booking: Responsif -->
                        <div class="grid grid-cols-1 md:grid-cols-[120px_1fr] gap-5">
                            <div class="w-full h-auto md:h-full">
                                <img src="<?= !empty($booking->image_url) ? base_url('assets/images/rooms/' . rawurlencode($booking->image_url)) : base_url('assets/images/room-placeholder.jpg'); ?>" alt="<?= htmlspecialchars($booking->room_number); ?>" class="rounded-md object-cover w-full h-full">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div><strong>Room:</strong> <span class="block text-medium-text"><?= htmlspecialchars($booking->room_number); ?> (<?= htmlspecialchars($booking->category_name); ?>)</span></div>
                                <div><strong>Check-in:</strong> <span class="block text-medium-text"><?= date('d M Y', strtotime($booking->check_in_date)); ?></span></div>
                                <div><strong>Check-out:</strong> <span class="block text-medium-text"><?= date('d M Y', strtotime($booking->check_out_date)); ?></span></div>
                                <div><strong>Total:</strong> <span class="block text-medium-text">Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span></div>
                                <div class="sm:col-span-2"><strong>Booked on:</strong> <span class="block text-medium-text"><?= date('d M Y, H:i', strtotime($booking->created_at)); ?></span></div>
                            </div>
                        </div>

                        <!-- Footer Kartu: Responsif, tombol menumpuk di mobile -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mt-4 pt-4 border-t border-dark-text">
                            <div class="font-semibold text-light-accent text-lg">
                                Total: Rp <?= number_format($booking->total_price, 0, ',', '.'); ?>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <a href="<?= base_url('booking/details/' . $booking->booking_id); ?>" class="btn btn-secondary">View Details</a>
                                <?php if ($booking->status === 'pending'): ?>
                                    <a href="<?= base_url('payment/process/' . $booking->booking_id); ?>" class="btn btn-success">Pay Now</a>
                                    <a href="<?= base_url('booking/cancel/' . $booking->booking_id); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>