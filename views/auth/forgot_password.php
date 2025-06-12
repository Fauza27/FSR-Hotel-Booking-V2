<?php
$pageTitle = (isset($data['show_password_form']) && $data['show_password_form']) ? 'Reset Your Password' : 'Forgot Password';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container py-16">
    <div class="flex items-center justify-center">
        <div class="card p-8 w-full max-w-md">
            
            <?php if(isset($data['show_password_form']) && $data['show_password_form']): ?>
                <!-- TAHAP 2: FORM RESET PASSWORD -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl">Reset Your Password</h2>
                    <p class="text-medium-text mt-2">Enter your new password for <span class="font-semibold text-light-accent"><?= htmlspecialchars($data['email']); ?></span>.</p>
                </div>
                
                <form action="<?= APP_URL ?>/reset-password" method="POST">
                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required>
                            <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text hover:text-light-accent" data-target="password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if(isset($data['password_err']) && !empty($data['password_err'])): ?>
                            <small class="text-error text-sm mt-1 block"><?= $data['password_err']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                             <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text hover:text-light-accent" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if(isset($data['confirm_password_err']) && !empty($data['confirm_password_err'])): ?>
                            <small class="text-error text-sm mt-1 block"><?= $data['confirm_password_err']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="submit_new_password" class="btn btn-primary w-full mt-4">Reset Password</button>
                </form>

            <?php else: ?>
                <!-- TAHAP 1: FORM INPUT EMAIL -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl">Forgot Your Password?</h2>
                    <p class="text-medium-text mt-2">Enter your email and we'll help you reset it.</p>
                </div>
                
                <form action="<?= APP_URL ?>/forgot-password" method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
                        <?php if(isset($data['email_err']) && !empty($data['email_err'])): ?>
                            <small class="text-error text-sm mt-1 block"><?= $data['email_err']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="submit_email" class="btn btn-primary w-full mt-4">Find Account</button>
                </form>
            <?php endif; ?>
            
            <div class="text-center mt-6">
                <p class="text-medium-text">Remember your password? <a href="<?= APP_URL ?>/login">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>