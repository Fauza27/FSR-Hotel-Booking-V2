<?php
$pageTitle = 'Register';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container py-16">
    <div class="flex items-center justify-center">
        <!-- Menggunakan .card dengan lebar maksimum lebih besar -->
        <div class="card p-8 w-full max-w-2xl"> 
            <div class="text-center mb-8">
                <h2 class="text-3xl">Create an Account</h2>
                <p class="text-medium-text mt-2">Fill in the form below to register</p>
            </div>
            
            <form action="<?= APP_URL ?>/register" method="POST" id="register-form">
                <!-- Grid Responsif: 2 kolom di layar medium ke atas, 1 kolom di layar kecil -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" value="<?= isset($data['username']) ? htmlspecialchars($data['username']) : ''; ?>" required>
                        <?php if(isset($data['username_err']) && !empty($data['username_err'])): ?>
                            <small class="text-error text-sm mt-1 block"><?= $data['username_err']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
                        <?php if(isset($data['email_err']) && !empty($data['email_err'])): ?>
                            <small class="text-error text-sm mt-1 block"><?= $data['email_err']; ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Enter your full name" value="<?= isset($data['full_name']) ? htmlspecialchars($data['full_name']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" value="<?= isset($data['phone']) ? htmlspecialchars($data['phone']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" class="form-control" placeholder="Enter your address" rows="3"><?= isset($data['address']) ? htmlspecialchars($data['address']) : ''; ?></textarea>
                </div>
                
                <!-- Grid Responsif untuk password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Choose a password" required>
                            <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text hover:text-light-accent" data-target="password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if(isset($data['password_err']) && !empty($data['password_err'])): ?>
                            <small class="text-error text-sm mt-1 block"><?= $data['password_err']; ?></small>
                        <?php endif; ?>
                        <small class="text-medium-text text-xs mt-1 block">Password must be at least 6 characters long</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                            <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text hover:text-light-accent" data-target="confirm_password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="flex items-start cursor-pointer text-medium-text">
                        <input type="checkbox" name="terms" required class="mr-3 mt-1 h-4 w-4 rounded border-dark-text bg-dark-bg text-primary focus:ring-accent">
                        <span>I agree to the <a href="<?= APP_URL ?>/terms" target="_blank">Terms and Conditions</a> and <a href="<?= APP_URL ?>/privacy" target="_blank">Privacy Policy</a></span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-full mt-4">Register</button>
            </form>
            
            <div class="text-center mt-6">
                <p class="text-medium-text">Already have an account? <a href="<?= APP_URL ?>/login">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>