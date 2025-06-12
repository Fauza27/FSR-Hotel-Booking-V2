<?php
$pageTitle = isset($errorTitle) ? $errorTitle : 'Error';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<div class="container mt-5 mb-5 text-center">
    <div style="font-size:5rem;color:var(--error-color);margin-bottom:1rem;">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <h1 class="mb-2"><?= isset($errorTitle) ? $errorTitle : 'Oops! Something went wrong.'; ?></h1>
    <p class="mb-3" style="color:var(--medium-text);font-size:1.2rem;">
        <?= isset($errorMessage) ? $errorMessage : 'The page you are looking for might be unavailable, or an unexpected error has occurred.'; ?>
    </p>
    <a href="<?= APP_URL ?>" class="btn btn-primary">Back to Home</a>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>
