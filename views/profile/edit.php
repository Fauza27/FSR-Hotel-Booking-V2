<?php
$pageTitle = 'Edit Profile';
$currentPage = 'profile';
$activeMenu = 'edit';
include_once(VIEW_PATH . 'layouts/header.php');
?>
<!-- Layout Utama: Grid, responsif -->
<div class="container my-8 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
    
    <!-- Sidebar Profil (sama seperti di atas) -->
    <aside class="bg-darker-bg p-6 rounded-lg h-fit">
        <div class="mx-auto mb-6">
            <?php
            $current_avatar_filename = $data['avatar'] ?? $user->avatar;
            $avatar_path = asset_url('images/user-placeholder.png');
            if (!empty($current_avatar_filename) && file_exists(AVATAR_UPLOAD_PATH . $current_avatar_filename)) {
                $avatar_path = asset_url(AVATAR_UPLOAD_DIR . $current_avatar_filename);
            }
            ?>
            <img src="<?= $avatar_path; ?>" alt="Avatar" class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-accent">
        </div>
        <div class="text-center mb-6">
            <h4 class="text-xl font-bold"><?= htmlspecialchars($data['full_name'] ?? $user->full_name); ?></h4>
        </div>
        <nav class="flex flex-col space-y-2">
            <a href="<?= base_url('profile'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'profile' ? 'active' : ''; ?>">Profile</a>
            <a href="<?= base_url('profile/bookings'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'booking_history' ? 'active' : ''; ?>">Booking History</a>
            <a href="<?= base_url('profile/edit'); ?>" class="px-4 py-3 rounded-md hover:bg-primary hover:text-light-text <?= $activeMenu == 'edit' ? 'active' : ''; ?>">Edit Profile</a>
        </nav>
    </aside>

    <!-- Konten Utama: Form Edit Profil -->
    <section class="bg-darker-bg p-6 lg:p-8 rounded-lg">
        <h2 class="text-2xl font-bold mb-6">Edit Profile</h2>

        <?php if(isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info'; ?>">
                <span><?= $_SESSION['flash_message']; ?></span>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <form action="<?= base_url('profile/edit'); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <!-- Full Name -->
            <div>
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($data['full_name'] ?? ''); ?>" required class="<?= isset($data['errors']['full_name']) ? 'border-error' : 'border-dark-text'; ?>">
                <?php if (isset($data['errors']['full_name'])): ?>
                    <p class="text-error text-sm mt-1"><?= $data['errors']['full_name']; ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Email (Readonly) -->
            <div>
                <label for="email">Email (cannot be changed)</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->email); ?>" readonly disabled class="bg-dark-text/30 cursor-not-allowed">
            </div>

            <!-- Phone -->
            <div>
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($data['phone'] ?? ''); ?>" class="<?= isset($data['errors']['phone']) ? 'border-error' : 'border-dark-text'; ?>">
                <?php if (isset($data['errors']['phone'])): ?>
                    <p class="text-error text-sm mt-1"><?= $data['errors']['phone']; ?></p>
                <?php endif; ?>
            </div>

            <!-- Address -->
            <div>
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?= htmlspecialchars($data['address'] ?? ''); ?></textarea>
            </div>

            <!-- Avatar -->
            <div>
                <label for="avatar">Avatar (optional)</label>
                <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg" class="<?= isset($data['errors']['avatar']) ? 'border-error' : 'border-dark-text'; ?>">
                <small class="text-medium-text text-xs mt-1">Accepted: JPG, JPEG, PNG. Max: <?= MAX_AVATAR_SIZE / 1024 / 1024; ?>MB</small>
                <?php if (isset($data['errors']['avatar'])): ?>
                    <p class="text-error text-sm mt-1"><?= $data['errors']['avatar']; ?></p>
                <?php endif; ?>
            </div>

            <hr class="border-dark-text my-6">

            <h3 class="text-xl font-semibold">Change Password (optional)</h3>
            
            <!-- Current Password with Toggle -->
            <div>
                <label for="current_password">Current Password</label>
                <div class="relative">
                    <input type="password" id="current_password" name="current_password" class="pr-10 <?= isset($data['errors']['current_password']) ? 'border-error' : 'border-dark-text'; ?>">
                    <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text" data-target="current_password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <?php if (isset($data['errors']['current_password'])): ?>
                    <p class="text-error text-sm mt-1"><?= $data['errors']['current_password']; ?></p>
                <?php endif; ?>
            </div>

            <!-- New Password with Toggle -->
            <div>
                <label for="new_password">New Password</label>
                <div class="relative">
                    <input type="password" id="new_password" name="new_password" class="pr-10 <?= isset($data['errors']['new_password']) ? 'border-error' : 'border-dark-text'; ?>">
                    <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text" data-target="new_password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <?php if (isset($data['errors']['new_password'])): ?>
                    <p class="text-error text-sm mt-1"><?= $data['errors']['new_password']; ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Confirm Password with Toggle -->
            <div>
                <label for="confirm_password">Confirm New Password</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" class="pr-10 <?= isset($data['errors']['confirm_password']) ? 'border-error' : 'border-dark-text'; ?>">
                    <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text" data-target="confirm_password">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <?php if (isset($data['errors']['confirm_password'])): ?>
                    <p class="text-error text-sm mt-1"><?= $data['errors']['confirm_password']; ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-full !mt-6">Save Changes</button>
        </form>
    </section>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>