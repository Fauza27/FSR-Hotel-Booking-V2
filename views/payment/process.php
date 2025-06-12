<?php
$pageTitle = 'Payment Process';
$currentPage = 'profile';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Bagian Konten Utama -->
<main class="flex-grow">
    <div class="container py-12 md:py-16">
        <!-- Menggunakan komponen .card yang telah didefinisikan di input.css -->
        <!-- max-w-2xl untuk membatasi lebar kartu dan mx-auto untuk memusatkannya -->
        <div class="card max-w-2xl mx-auto">
            <!-- p-6 untuk padding di mobile, md:p-8 untuk padding di layar lebih besar -->
            <div class="p-6 md:p-8">

                <!-- Judul form, menggunakan utility class untuk margin dan ukuran font -->
                <h2 class="text-2xl md:text-3xl font-bold mb-4 text-center">
                    Payment for Booking #<?= htmlspecialchars($booking->booking_id); ?>
                </h2>

                <!-- Detail Booking -->
                <div class="space-y-2 mb-6 text-medium-text">
                    <p><strong class="font-semibold text-light-text">Room:</strong> <?= htmlspecialchars($room->room_number); ?> (<?= htmlspecialchars($room->category_name); ?>)</p>
                    <p><strong class="font-semibold text-light-text">Check-in:</strong> <?= date('d M Y', strtotime($booking->check_in_date)); ?></p>
                    <p><strong class="font-semibold text-light-text">Check-out:</strong> <?= date('d M Y', strtotime($booking->check_out_date)); ?></p>
                    <p class="text-lg">
                        <strong class="font-semibold text-light-text">Total Amount:</strong> 
                        <span class="font-bold text-accent">Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                    </p>
                </div>
                
                <!-- Garis pemisah, menggunakan warna dari variabel tema -->
                <hr class="border-t border-dark-text my-6">

                <!-- Form Pembayaran -->
                <form action="<?= APP_URL; ?>/payment/process/<?= $booking->booking_id; ?>" method="POST">
                    <!-- Menggunakan komponen .form-group -->
                    <div class="form-group">
                        <!-- Menggunakan komponen .form-label -->
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <!-- Menggunakan komponen .form-control. ID 'payment_method' dipertahankan untuk JS. -->
                        <select id="payment_method" name="payment_method" class="form-control" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Pay at Hotel (Cash)</option>
                        </select>
                    </div>

                    <!-- Menggunakan komponen .btn, w-full untuk membuatnya selebar kontainer (btn-block) -->
                    <button type="submit" class="btn btn-primary w-full mt-6">Pay Now</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>