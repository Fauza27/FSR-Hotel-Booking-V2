<?php
// ... (kode PHP Anda tetap sama) ...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- LINK KE OUTPUT TAILWIND CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/src/output.css"> <!-- Ganti ini! -->
</head>
<body class="bg-dark-bg text-light-text">
    <!-- Header -->
    <header class="bg-darker-bg shadow-lg sticky top-0 z-50">
        <div class="container">
            <nav class="flex justify-between items-center py-4">
                <a href="<?= APP_URL ?>" class="text-2xl font-bold text-accent">FSR<span class="text-light-text">Hotel</span></a>
                
                <!-- Tombol Menu Hamburger untuk Mobile -->
                <button id="menu-toggle-btn" class="md:hidden text-light-text text-2xl z-50">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Nav Links - Perhatikan kelas responsifnya -->
                <ul class="nav-links hidden md:flex items-center space-x-8">
                    <li><a href="<?= APP_URL ?>" class="<?= (isset($currentPage) && $currentPage == 'home') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?= APP_URL ?>/rooms" class="<?= (isset($currentPage) && $currentPage == 'rooms') ? 'active' : ''; ?>">Rooms</a></li>
                    <li><a href="<?= APP_URL ?>/about" class="<?= (isset($currentPage) && $currentPage == 'about') ? 'active' : ''; ?>">About</a></li>
                    <li><a href="<?= APP_URL ?>/contact" class="<?= (isset($currentPage) && $currentPage == 'contact') ? 'active' : ''; ?>">Contact</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?= APP_URL ?>/profile" class="<?= (isset($currentPage) && $currentPage == 'profile') ? 'active' : ''; ?>">My Account</a></li>
                        <li><a href="<?= APP_URL ?>/logout" class="btn btn-secondary !py-2 !px-4">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?= APP_URL ?>/login" class="btn btn-secondary !py-2 !px-4">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <!-- Mobile Menu - Awalnya tersembunyi, di-toggle oleh JS -->
        <div id="mobile-menu" class="hidden md:hidden bg-darker-bg/95 backdrop-blur-sm">
            <ul class="flex flex-col items-center py-4 space-y-4">
                <li class="w-full px-4"><a href="<?= APP_URL ?>/rooms" class="block text-center btn btn-secondary w-full <?= (isset($currentPage) && $currentPage == 'rooms') ? 'active' : ''; ?>">Rooms</a></li>
                <li class="w-full px-4"><a href="<?= APP_URL ?>/about" class="block text-center btn btn-secondary w-full <?= (isset($currentPage) && $currentPage == 'about') ? 'active' : ''; ?>">About</a></li>
                <li class="w-full px-4"><a href="<?= APP_URL ?>/contact" class="block text-center btn btn-secondary w-full <?= (isset($currentPage) && $currentPage == 'contact') ? 'active' : ''; ?>">Contact</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="w-full px-4"><a href="<?= APP_URL ?>/profile" class="block text-center btn btn-secondary w-full <?= (isset($currentPage) && $currentPage == 'profile') ? 'active' : ''; ?>">My Account</a></li>
                    <li class="w-full px-4"><a href="<?= APP_URL ?>/logout" class="block text-center btn btn-secondary w-full">Logout</a></li>
                <?php else: ?>
                    <li class="w-full px-4"><a href="<?= APP_URL ?>/login" class="block text-center btn btn-secondary w-full">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    
    <!-- Flash messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container mt-4">
            <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?>">
                <?= $_SESSION['flash_message']; ?>
            </div>
        </div>
        <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow">