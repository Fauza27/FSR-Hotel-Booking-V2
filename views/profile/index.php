<?php
$pageTitle = 'My Profile';
$currentPage = 'profile';
$activeMenu = 'profile';
include_once(VIEW_PATH . 'layouts/header.php');
?>
<!-- Layout Utama: Grid, responsif -->
<div class="container my-8 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
    
    <!-- Sidebar Profil (sama seperti di atas) -->
    <aside class="bg-darker-bg p-6 rounded-lg h-fit">
        <div class="mx-auto mb-6">
            <?php
            $avatar_path = asset_url('images/user-placeholder.png');
            if (!empty($user->avatar) && file_exists(AVATAR_UPLOAD_PATH . $user->avatar)) {
                $avatar_path = asset_url(AVATAR_UPLOAD_DIR . $user->avatar);
            }
            ?>
            <img src="<?= $avatar_path; ?>" alt="Avatar" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-accent">
        </div>
        <div class="text-center mb-6">
            <h4 class="text-xl font-bold"><?= htmlspecialchars($user->full_name); ?></h4>
        </div>
        <nav class="flex flex-col space-y-2">
            <a href="<?= base_url('profile'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'profile' ? 'active' : ''; ?>">Profile</a>
            <a href="<?= base_url('profile/bookings'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'booking_history' ? 'active' : ''; ?>">Booking History</a>
            <a href="<?= base_url('profile/edit'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'edit' ? 'active' : ''; ?>">Edit Profile</a>
        </nav>
    </aside>

    <!-- Konten Utama: Detail Profil -->
    <section class="bg-darker-bg p-6 lg:p-8 rounded-lg">
        <h2 class="text-2xl font-bold mb-6">Welcome, <?= htmlspecialchars($user->full_name); ?>!</h2>
        
        <!-- Menggunakan komponen .card yang telah didefinisikan -->
        <div class="card p-6">
            <div class="space-y-4 text-base">
                <p><strong class="font-semibold text-medium-text w-20 inline-block">Email:</strong> <?= htmlspecialchars($user->email); ?></p>
                <p><strong class="font-semibold text-medium-text w-20 inline-block">Phone:</strong> <?= htmlspecialchars($user->phone ?? 'Not set'); ?></p>
                <div>
                    <strong class="font-semibold text-medium-text w-20 inline-block align-top">Address:</strong>
                    <span class="inline-block"><?= nl2br(htmlspecialchars($user->address ?? 'Not set')); ?></span>
                </div>
            </div>
            <div class="mt-6">
                <a href="<?= base_url('profile/edit'); ?>" class="btn btn-primary">Edit My Profile</a>
            </div>
        </div>
    </section>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>