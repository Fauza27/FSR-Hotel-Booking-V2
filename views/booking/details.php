<?php
$pageTitle = 'Booking Details';
include_once(VIEW_PATH . 'layouts/header.php');

// Helper untuk kelas status agar lebih rapi dan terpusat
function getStatusClasses($status) {
    $baseClasses = 'inline-block px-3 py-1 text-sm font-medium rounded-full';
    switch (strtolower($status)) {
        case 'confirmed':
            return $baseClasses . ' bg-success/20 text-success';
        case 'pending':
            return $baseClasses . ' bg-warning/20 text-warning';
        case 'cancelled':
            return $baseClasses . ' bg-error/20 text-error';
        case 'completed':
            return $baseClasses . ' bg-info/20 text-info';
        default:
            return $baseClasses . ' bg-dark-text/20 text-medium-text';
    }
}
?>

<!-- Container utama menggunakan kelas dari input.css -->
<div class="container my-8">

    <!-- 
      Layout Grid Utama:
      - 'grid' untuk mengaktifkan grid layout.
      - 'grid-cols-1' untuk default (tampilan mobile, satu kolom).
      - 'lg:grid-cols-[1fr_350px]' untuk layar besar (large), membuat dua kolom. Kolom pertama fleksibel, kolom kedua 350px.
      - 'gap-8' untuk jarak antar kolom.
    -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-8">

        <!-- Kolom Konten Utama -->
        <main class="flex flex-col gap-8">

            <h2 class="text-3xl font-semibold">Booking Details</h2>

            <!-- Bagian Informasi Booking -->
            <section>
                <h3 class="text-2xl font-semibold mb-4">Booking Information</h3>
                <!-- Menggunakan komponen .card dari input.css, dengan padding internal -->
                <div class="card p-6">
                    <!-- 
                        Layout Detail Booking:
                        - Grid dengan 1 kolom di mobile, dan 2 kolom (gambar dan info) di layar medium.
                    -->
                    <div class="grid grid-cols-1 md:grid-cols-[150px_1fr] gap-6">
                        <!-- Gambar Kamar -->
                        <div class="w-full h-[150px] rounded-lg overflow-hidden">
                            <img src="<?= !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . $room->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= $room->room_number; ?>" class="w-full h-full object-cover">
                        </div>
                        <!-- Info Detail Booking -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="flex flex-col">
                                <strong class="text-medium-text text-sm">Room:</strong>
                                <span><?= $room->room_number; ?> (<?= $room->category_name; ?>)</span>
                            </div>
                            <div class="flex flex-col">
                                <strong class="text-medium-text text-sm">Check-in Date:</strong>
                                <span><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                            </div>
                            <div class="flex flex-col">
                                <strong class="text-medium-text text-sm">Check-out Date:</strong>
                                <span><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                            </div>
                            <div class="flex flex-col">
                                <strong class="text-medium-text text-sm">Duration:</strong>
                                <span><?= $nights; ?> night<?= $nights > 1 ? 's' : ''; ?></span>
                            </div>
                            <div class="flex flex-col">
                                <strong class="text-medium-text text-sm">Guests:</strong>
                                <span><?= $booking->adults; ?> Adult<?= $booking->adults > 1 ? 's' : ''; ?><?= $booking->children > 0 ? ', ' . $booking->children . ' Child' . ($booking->children > 1 ? 'ren' : '') : ''; ?></span>
                            </div>
                            <div class="flex flex-col">
                                <strong class="text-medium-text text-sm">Booking Date:</strong>
                                <span><?= date('d M Y H:i', strtotime($booking->created_at)); ?></span>
                            </div>
                            <div class="flex flex-col sm:col-span-2"> <!-- Span 2 kolom agar status di baris baru jika perlu -->
                                <strong class="text-medium-text text-sm">Status:</strong>
                                <span class="<?= getStatusClasses($booking->status) ?>"><?= ucfirst($booking->status); ?></span>
                            </div>
                        </div>
                    </div>
                    <!-- Footer Kartu -->
                    <div class="mt-6 pt-6 border-t border-dark-text flex justify-end">
                        <div class="text-xl font-semibold text-light-accent">
                            Total: Rp <?= number_format($booking->total_price, 0, ',', '.'); ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bagian Informasi Tamu -->
            <section>
                <h3 class="text-2xl font-semibold mb-4">Guest Information</h3>
                <div class="card p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="flex flex-col">
                            <strong class="text-medium-text text-sm">Full Name:</strong>
                            <span><?= $user->full_name; ?></span>
                        </div>
                        <div class="flex flex-col">
                            <strong class="text-medium-text text-sm">Email:</strong>
                            <span><?= $user->email; ?></span>
                        </div>
                        <div class="flex flex-col">
                            <strong class="text-medium-text text-sm">Phone:</strong>
                            <span><?= $user->phone; ?></span>
                        </div>
                        <div class="flex flex-col">
                            <strong class="text-medium-text text-sm">Address:</strong>
                            <span><?= $user->address; ?></span>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- 
          Sidebar:
          - 'h-fit' agar tingginya sesuai konten.
          - 'sticky' dan 'top-24' agar tetap di layar saat scroll (posisi sticky mulai dari 96px dari atas).
        -->
        <aside class="bg-darker-bg rounded-lg p-6 h-fit lg:sticky lg:top-24">
            <h3 class="text-xl font-semibold text-center mb-6">Booking Summary</h3>

            <div class="space-y-3"> <!-- space-y-3 memberi margin atas otomatis ke semua elemen anak kecuali yang pertama -->
                <div class="flex justify-between items-center text-medium-text">
                    <span>Room Type:</span>
                    <span class="text-light-text"><?= $room->category_name; ?></span>
                </div>
                <div class="flex justify-between items-center text-medium-text">
                    <span>Room Number:</span>
                    <span class="text-light-text"><?= $room->room_number; ?></span>
                </div>
                <div class="flex justify-between items-center text-medium-text">
                    <span>Check-in:</span>
                    <span class="text-light-text"><?= date('d M Y', strtotime($booking->check_in_date)); ?></span>
                </div>
                <div class="flex justify-between items-center text-medium-text">
                    <span>Check-out:</span>
                    <span class="text-light-text"><?= date('d M Y', strtotime($booking->check_out_date)); ?></span>
                </div>
                <div class="flex justify-between items-center text-medium-text">
                    <span>Nights:</span>
                    <span class="text-light-text"><?= $nights; ?></span>
                </div>
                <div class="flex justify-between items-center text-medium-text">
                    <span>Price per night:</span>
                    <span class="text-light-text">Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-dark-text flex justify-between items-center font-bold text-lg">
                <span>Total:</span>
                <span class="text-light-accent">Rp <?= number_format($booking->total_price, 0, ',', '.'); ?></span>
            </div>

            <div class="text-center mt-6 text-medium-text text-sm">
                <p>Booking ID: <?= $booking->booking_id; ?></p>
                <div class="mt-2">
                    <span>Status: </span>
                    <span class="<?= getStatusClasses($booking->status) ?>"><?= ucfirst($booking->status); ?></span>
                </div>
            </div>

            <div class="mt-6">
                <!-- Tombol menggunakan kelas .btn dan .btn-secondary, w-full agar lebarnya 100% -->
                <a href="<?= APP_URL; ?>/profile/bookings" class="btn btn-secondary w-full">View All Bookings</a>
            </div>
        </aside>
    </div>
</div>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>