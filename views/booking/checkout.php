<?php
$pageTitle = 'Booking Checkout';
include_once(VIEW_PATH . 'layouts/header.php');

// Mapping status ke kelas Tailwind untuk kemudahan
$statusClasses = [
    'pending' => 'bg-warning/20 text-warning',
    'confirmed' => 'bg-success/20 text-success',
    'cancelled' => 'bg-error/20 text-error',
    'completed' => 'bg-info/20 text-info',
];
?>

<div class="container my-8 md:my-12">
    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-8">
        
        <!-- Kolom Utama -->
        <main>
            <h1 class="text-3xl font-bold mb-6">Booking Checkout</h1>
            
            <!-- Booking Details Section -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Booking Details</h2>
                <div class="card p-6">
                    <div class="grid grid-cols-1 md:grid-cols-[150px_1fr] gap-6 items-start">
                        <!-- Gambar Kamar -->
                        <div class="w-full h-[120px] rounded-lg overflow-hidden">
                            <img src="<?= !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . e($room->image_url) : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= e($room->room_number); ?>" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Info Pemesanan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <strong class="block text-medium-text text-sm">Room</strong>
                                <span class="text-light-text"><?= e($room->room_number); ?> (<?= e($room->category_name); ?>)</span>
                            </div>
                            <div>
                                <strong class="block text-medium-text text-sm">Check-in</strong>
                                <span class="text-light-text"><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                            </div>
                            <div>
                                <strong class="block text-medium-text text-sm">Check-out</strong>
                                <span class="text-light-text"><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                            </div>
                            <div>
                                <strong class="block text-medium-text text-sm">Duration</strong>
                                <span class="text-light-text"><?= $nights; ?> night<?= $nights > 1 ? 's' : ''; ?></span>
                            </div>
                            <div>
                                <strong class="block text-medium-text text-sm">Guests</strong>
                                <span class="text-light-text"><?= e($booking->adults); ?> Adult<?= $booking->adults > 1 ? 's' : ''; ?><?= $booking->children > 0 ? ', ' . e($booking->children) . ' Child' . ($booking->children > 1 ? 'ren' : '') : ''; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Payment Form Section -->
            <section>
                <h2 class="text-2xl font-semibold mb-4">Payment Information</h2>
                <div class="card p-6 md:p-8">
                    <!-- Payment Method Selection -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Select Payment Method</h3>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer p-4 rounded-lg bg-dark-bg border border-dark-text has-[:checked]:border-accent has-[:checked]:bg-primary/10">
                                <input type="radio" name="payment_method" value="credit_card" class="mr-4" checked>
                                <i class="fas fa-credit-card w-6 text-accent"></i>
                                <span class="ml-3 font-medium">Credit / Debit Card</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-4 rounded-lg bg-dark-bg border border-dark-text has-[:checked]:border-accent has-[:checked]:bg-primary/10">
                                <input type="radio" name="payment_method" value="bank_transfer" class="mr-4">
                                <i class="fas fa-university w-6 text-accent"></i>
                                <span class="ml-3 font-medium">Bank Transfer</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-4 rounded-lg bg-dark-bg border border-dark-text has-[:checked]:border-accent has-[:checked]:bg-primary/10">
                                <input type="radio" name="payment_method" value="cash" class="mr-4">
                                <i class="fas fa-money-bill-wave w-6 text-accent"></i>
                                <span class="ml-3 font-medium">Pay at Hotel (Cash)</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Credit Card Form (ID preserved for JS) -->
                    <div id="credit-card-form">
                        <div class="form-group">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" id="card_number" name="card_number" class="form-control" placeholder="XXXX XXXX XXXX XXXX" maxlength="19">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_1fr] gap-4">
                            <div class="form-group">
                                <label for="card_holder" class="form-label">Card Holder Name</label>
                                <input type="text" id="card_holder" name="card_holder" class="form-control" placeholder="Name on card">
                            </div>
                            <div class="form-group">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="XXX" maxlength="4">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bank Transfer Form (ID preserved for JS) -->
                    <div id="bank-transfer-form" style="display: none;">
                        <div class="alert alert-info">
                            <p><strong>Bank Account Details:</strong></p>
                            <p>Bank Name: Example Bank</p>
                            <p>Account Name: PurpleStay Hotel</p>
                            <p>Account Number: 1234567890</p>
                            <p>Reference: BOOK-<?= e($booking->booking_id); ?></p>
                        </div>
                        <div class="form-group mt-4">
                            <label for="transfer_proof" class="form-label">Upload Transfer Proof</label>
                            <input type="file" id="transfer_proof" name="transfer_proof" class="form-control" accept="image/*,.pdf">
                            <small class="text-medium-text mt-1 block">Accepted formats: JPG, PNG, PDF. Max size: 2MB</small>
                        </div>
                    </div>
                    
                    <!-- Cash Payment Form (ID preserved for JS) -->
                    <div id="cash-form" style="display: none;">
                        <div class="alert alert-info">
                            <p>You've selected to pay at the hotel. Your booking will be held for 24 hours. Payment will be required upon check-in.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        
        <!-- Kolom Sidebar -->
        <aside class="sticky top-24 h-fit">
            <div class="card p-6">
                <h3 class="text-xl font-semibold mb-4 text-center">Booking Summary</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-medium-text">Room Type:</span> <span><?= e($room->category_name); ?></span></div>
                    <div class="flex justify-between"><span class="text-medium-text">Room Number:</span> <span><?= e($room->room_number); ?></span></div>
                    <div class="flex justify-between"><span class="text-medium-text">Check-in:</span> <span><?= date('d M Y', strtotime($booking->check_in_date)); ?></span></div>
                    <div class="flex justify-between"><span class="text-medium-text">Check-out:</span> <span><?= date('d M Y', strtotime($booking->check_out_date)); ?></span></div>
                    <div class="flex justify-between"><span class="text-medium-text">Nights:</span> <span><?= $nights; ?></span></div>
                    <div class="flex justify-between"><span class="text-medium-text">Guests:</span> <span><?= e($booking->adults + $booking->children); ?></span></div>
                </div>

                <div class="my-6 p-4 bg-primary/10 rounded-lg space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Room Rate (<?= $nights; ?> night<?= $nights > 1 ? 's' : ''; ?>):</span>
                        <span>Rp <?= number_format($room->price_per_night * $nights, 0, ',', '.'); ?></span>
                    </div>
                    <?php $taxes = $booking->total_price - ($room->price_per_night * $nights); ?>
                    <?php if(isset($taxes) && $taxes > 0): ?>
                    <div class="flex justify-between text-sm">
                        <span>Taxes & Fees:</span>
                        <span>Rp <?= number_format($taxes, 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="pt-2 border-t border-dark-text/50 flex justify-between font-bold text-lg">
                        <span>Total Amount:</span>
                        <span class="text-light-accent">Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <!-- PERUBAHAN DI SINI: Menambahkan data-success-url -->
                    <button type="button" 
                            id="complete-payment-btn" 
                            class="btn btn-primary w-full"
                            data-success-url="<?= APP_URL; ?>/payment/success/<?= e($booking->booking_id); ?>">
                        Complete Payment
                    </button>
                    <a href="<?= APP_URL; ?>/booking/cancel/<?= e($booking->booking_id); ?>" class="btn btn-secondary w-full" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</a>
                </div>

                <div class="text-center mt-4">
                    <p class="text-medium-text text-sm">Booking ID: <?= e($booking->booking_id); ?></p>
                    <p class="text-medium-text text-sm mt-1">Status: <span class="font-semibold px-2 py-1 rounded-full <?= $statusClasses[$booking->status] ?? '' ?>"><?= ucfirst(e($booking->status)); ?></span></p>
                </div>
            </div>
        </aside>

    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>