<?php
// views/booking/confirmation.php
$pageTitle = 'Booking Confirmation';
include_once(VIEW_PATH . 'layouts/header.php');

// Helper untuk memetakan status ke kelas Tailwind agar HTML lebih bersih
$statusClasses = [
    'pending' => 'bg-warning/20 text-warning',
    'confirmed' => 'bg-success/20 text-success',
    'cancelled' => 'bg-error/20 text-error',
    'completed' => 'bg-info/20 text-info',
];
$currentStatusClass = $statusClasses[$booking->status] ?? 'bg-medium-text/20 text-light-text';
?>

<div class="container my-8 md:my-16">
    <!-- Layout utama menggunakan Grid untuk konten utama dan sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8">
        
        <!-- Kolom Konten Utama -->
        <main class="flex flex-col gap-8">
            
            <!-- Alert Sukses -->
            <div class="alert alert-success">
                <h4 class="font-bold text-lg flex items-center gap-2"><i class="fas fa-check-circle"></i> Booking Created Successfully!</h4>
                <p class="mt-1">Your booking request has been received. Please proceed to payment to confirm your reservation.</p>
            </div>
            
            <!-- Detail Pemesanan -->
            <section>
                <h3 class="text-2xl font-semibold mb-4 text-light-text">Booking Details</h3>
                <!-- Menggunakan style card dari utility classes karena ada border-left spesifik -->
                <div class="bg-dark-bg rounded-lg p-6 border-l-4 border-primary shadow-lg">
                    <!-- Header Kartu -->
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4 pb-4 border-b border-dark-text">
                        <h4 class="text-xl font-bold">Booking #<?= htmlspecialchars($booking->booking_id); ?></h4>
                        <div class="px-4 py-1 rounded-full text-sm font-medium <?= $currentStatusClass ?>">
                            <?= ucfirst(htmlspecialchars($booking->status)); ?>
                        </div>
                    </div>
                    
                    <!-- Grid untuk Gambar dan Info -->
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_2fr] gap-6 items-center">
                        <div class="w-full h-40 rounded-lg overflow-hidden">
                            <img src="<?= !empty($roomImages) && count($roomImages) > 0 ? APP_URL . '/assets/images/rooms/' . htmlspecialchars($roomImages[0]->image_url) : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= htmlspecialchars($room->room_number); ?>" class="w-full h-full object-cover">
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <strong class="text-medium-text">Room:</strong>
                                <span class="block text-light-text"><?= htmlspecialchars($room->room_number); ?> (<?= htmlspecialchars($room->category_name); ?>)</span>
                            </div>
                            <div>
                                <strong class="text-medium-text">Check-in Date:</strong>
                                <span class="block text-light-text"><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                            </div>
                            <div>
                                <strong class="text-medium-text">Check-out Date:</strong>
                                <span class="block text-light-text"><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                            </div>
                            <div>
                                <strong class="text-medium-text">Duration:</strong>
                                <span class="block text-light-text"><?= $nights; ?> night<?= $nights > 1 ? 's' : ''; ?></span>
                            </div>
                            <div>
                                <strong class="text-medium-text">Guests:</strong>
                                <span class="block text-light-text"><?= $booking->adults; ?> Adult<?= $booking->adults > 1 ? 's' : ''; ?><?= $booking->children > 0 ? ', ' . $booking->children . ' Child' . ($booking->children > 1 ? 'ren' : '') : ''; ?></span>
                            </div>
                            <div>
                                <strong class="text-medium-text">Booking Date:</strong>
                                <span class="block text-light-text"><?= date('d M Y H:i', strtotime($booking->created_at)); ?></span>
                            </div>
                            <div>
                                <strong class="text-medium-text">Identity Document:</strong>
                                <span class="block text-light-text"><i class="fas fa-file-alt text-light-accent"></i> Uploaded</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Kartu dengan Tombol Aksi -->
                    <div class="mt-6 pt-6 border-t border-dark-text flex flex-wrap items-center justify-between gap-4">
                        <div class="text-xl font-bold text-light-accent">
                            Total: Rp <?= number_format($booking->total_price, 0, ',', '.'); ?>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="<?= APP_URL; ?>/booking/cancel/<?= $booking->booking_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                            <a href="<?= APP_URL; ?>/payment/process/<?= $booking->booking_id; ?>" class="btn btn-primary">Pay Now</a>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Informasi Tamu -->
            <section>
                <h3 class="text-2xl font-semibold mb-4 text-light-text">Guest Information</h3>
                <div class="card p-6"> <!-- Menggunakan komponen .card -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <strong class="text-medium-text">Full Name:</strong>
                            <span class="block text-light-text"><?= htmlspecialchars($user->full_name); ?></span>
                        </div>
                        <div>
                            <strong class="text-medium-text">Email:</strong>
                            <span class="block text-light-text"><?= htmlspecialchars($user->email); ?></span>
                        </div>
                        <div>
                            <strong class="text-medium-text">Phone:</strong>
                            <span class="block text-light-text"><?= htmlspecialchars($user->phone); ?></span>
                        </div>
                        <div>
                            <strong class="text-medium-text">Address:</strong>
                            <span class="block text-light-text"><?= htmlspecialchars($user->address); ?></span>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Informasi Pembayaran -->
            <section>
                <h3 class="text-2xl font-semibold mb-4 text-light-text">Payment Information</h3>
                <div class="card p-6"> <!-- Menggunakan komponen .card -->
                    <p class="text-medium-text">To confirm your booking, please complete the payment. You can pay using the following methods:</p>
                    <ul class="list-disc pl-5 my-4 space-y-2 text-medium-text">
                        <li>Credit Card</li>
                        <li>Bank Transfer</li>
                        <li>Cash at hotel reception (upon arrival)</li>
                    </ul>
                    <p class="mt-3 text-sm text-warning/80">Please note that your booking will be cancelled automatically if payment is not completed within 24 hours.</p>
                    <div class="text-center mt-6">
                        <a href="<?= APP_URL; ?>/payment/process/<?= $booking->booking_id; ?>" class="btn btn-primary">Proceed to Payment</a>
                    </div>
                </div>
            </section>
        </main>
        
        <!-- Kolom Sidebar -->
        <aside class="bg-darker-bg rounded-lg p-6 h-fit sticky top-24 shadow-lg">
            <h3 class="text-xl font-semibold mb-6 text-center text-light-text">Booking Summary</h3>
            
            <!-- Ringkasan Total Harga -->
            <div class="bg-primary/20 rounded-md p-4 space-y-2">
                <div class="flex justify-between text-sm"><span class="text-medium-text">Room Type:</span> <span><?= htmlspecialchars($room->category_name); ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-medium-text">Room Number:</span> <span><?= htmlspecialchars($room->room_number); ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-medium-text">Check-in:</span> <span><?= date('d M Y', strtotime($booking->check_in_date)); ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-medium-text">Check-out:</span> <span><?= date('d M Y', strtotime($booking->check_out_date)); ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-medium-text">Nights:</span> <span><?= $nights; ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-medium-text">Price/night:</span> <span>Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span></div>
                
                <div class="!mt-4 pt-2 border-t border-dark-text flex justify-between font-bold text-lg">
                    <span class="text-light-text">Total:</span>
                    <span class="text-light-accent">Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                </div>
            </div>
            
            <!-- Info Tambahan di Sidebar -->
            <div class="text-center mt-6 space-y-2 text-sm text-medium-text">
                <p>Booking ID: <?= htmlspecialchars($booking->booking_id); ?></p>
                <p>Status: <span class="font-semibold <?= $currentStatusClass ?> px-2 py-0.5 rounded"><?= ucfirst(htmlspecialchars($booking->status)); ?></span></p>
            </div>
            
            <div class="text-center mt-6">
                <a href="<?= APP_URL; ?>/profile/bookings" class="btn btn-secondary w-full">View All Bookings</a>
            </div>
        </aside>
        
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>