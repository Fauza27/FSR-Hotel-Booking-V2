<?php
$pageTitle = isset($successTitle) ? $successTitle : 'Success';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-5 mb-5 text-center">
    <div style="font-size:5rem;color:var(--success-color);margin-bottom:1rem;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1 class="mb-2"><?= isset($successTitle) ? $successTitle : 'Success!'; ?></h1>
    <p class="mb-3" style="color:var(--medium-text);font-size:1.2rem;">
        <?= isset($successMessage) ? $successMessage : 'Your action was completed successfully.'; ?>
    </p>
    <a href="<?= APP_URL ?>" class="btn btn-primary">Back to Home</a>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
