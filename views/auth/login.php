<?php
$pageTitle = 'Login';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container py-16">
    <div class="flex items-center justify-center">
        <!-- Menggunakan komponen .card yang didefinisikan di input.css -->
        <div class="card p-8 w-full max-w-md">
            
            <div class="text-center mb-8">
                <h2 class="text-3xl">Login to Your Account</h2>
                <p class="text-medium-text mt-2">Enter your credentials to access your account</p>
            </div>
            
            <form action="<?= APP_URL ?>/login" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username or email" value="<?= isset($data['username']) ? htmlspecialchars($data['username']) : ''; ?>" required>
                    <?php if(isset($data['username_err']) && !empty($data['username_err'])): ?>
                        <small class="text-error text-sm mt-1 block"><?= $data['username_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <!-- Wrapper untuk ikon -->
                    <div class="relative">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        <!-- Ikon di-posisikan absolute di dalam wrapper relative -->
                        <span class="password-toggle absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-medium-text hover:text-light-accent" data-target="password">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <?php if(isset($data['password_err']) && !empty($data['password_err'])): ?>
                        <small class="text-error text-sm mt-1 block"><?= $data['password_err']; ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="flex justify-end mb-6">
                    <a href="<?= APP_URL ?>/forgot-password">Forgot password?</a>
                </div>
                
                <!-- Tombol menggunakan komponen .btn dan utilitas w-full untuk block-level -->
                <button type="submit" class="btn btn-primary w-full">Login</button>
            </form>
            
            <div class="text-center mt-6">
                <p class="text-medium-text">Don't have an account? <a href="<?= APP_URL ?>/register">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>