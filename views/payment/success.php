<?php
$pageTitle = 'Payment Success';
$currentPage = 'profile';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Bagian Konten Utama -->
<main class="flex-grow">
    <!-- py-16 md:py-24 untuk padding vertikal yang lebih besar -->
    <div class="container py-16 md:py-24 text-center">

        <!-- Ikon checkmark, mengganti inline style dengan utility class -->
        <!-- text-8xl untuk ukuran font, text-success untuk warna, mb-4 untuk margin bawah -->
        <div class="text-8xl text-success mb-4">
            <i class="fas fa-check-circle"></i>
        </div>

        <!-- Judul halaman -->
        <h1 class="mb-2">Payment Successful!</h1>

        <!-- Deskripsi dan detail booking -->
        <!-- max-w-3xl mx-auto agar teks tidak terlalu lebar di layar besar -->
        <p class="text-lg text-medium-text mb-8 max-w-3xl mx-auto">
            Thank you for your payment. Your booking has been confirmed.
            <!-- Menggunakan 'block' dan 'mt-4' untuk membuat baris baru dengan spasi -->
            <span class="block mt-4">
                <strong>Booking ID:</strong> <?= htmlspecialchars($booking->booking_id); ?><br>
                <strong>Room:</strong> <?= htmlspecialchars($room->room_number); ?> (<?= htmlspecialchars($room->category_name); ?>)<br>
                <strong>Check-in:</strong> <?= date('d M Y', strtotime($booking->check_in_date)); ?>,
                <strong>Check-out:</strong> <?= date('d M Y', strtotime($booking->check_out_date)); ?>
            </span>
        </p>

        <!-- Wrapper untuk tombol agar responsif -->
        <!-- flex-col di mobile, sm:flex-row di layar kecil ke atas, gap-4 untuk jarak antar tombol -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <!-- Menggunakan komponen .btn yang sudah ada -->
            <a href="<?= APP_URL; ?>/profile/bookings" class="btn btn-primary w-full sm:w-auto">View My Bookings</a>
            <a href="<?= APP_URL; ?>" class="btn btn-secondary w-full sm:w-auto">Back to Home</a>
        </div>
        
    </div>
</main>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>