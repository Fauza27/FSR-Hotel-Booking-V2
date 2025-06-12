<?php
$pageTitle = 'Login Admin';
include_once(VIEW_PATH . 'admin/layouts/header.php');
?>
<div class="container mt-4 mb-4">
    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-title">
                <h2>Login Admin</h2>
                <p>Masukkan username dan password admin</p>
            </div>
            <?php if(isset(
                $data['error']) && !empty($data['error'])): ?>
                <div class="alert alert-danger"><?= $data['error']; ?></div>
            <?php endif; ?>
            <?php if(isset($data['admin_debug'])): ?>
                <div class="alert alert-info">DEBUG: <?= htmlspecialchars($data['admin_debug']); ?></div>
            <?php endif; ?>
            <form action="index.php?url=admin/login" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username admin" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</div>
<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
