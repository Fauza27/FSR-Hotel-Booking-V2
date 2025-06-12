<?php
$pageTitle = htmlspecialchars($room->room_number) . ' - ' . htmlspecialchars($room->category_name);
$currentPage = 'rooms';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Room Details Section -->
<div class="container py-8 md:py-12">
    <!-- Layout Grid Utama (Konten & Sidebar) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Konten Utama -->
        <main class="lg:col-span-2">
            <!-- ... (Konten galeri, fitur, deskripsi, dan review tetap sama persis) ... -->
             <!-- Room Gallery -->
            <div class="mb-8">
                <div class="main-image h-80 md:h-[450px] rounded-lg overflow-hidden mb-4 shadow-lg">
                    <?php 
                    $mainImageUrl = (!empty($roomImages) && isset($roomImages[0]->image_url) && !empty(trim($roomImages[0]->image_url)))
                        ? APP_URL . '/assets/images/rooms/' . htmlspecialchars($roomImages[0]->image_url)
                        : APP_URL . '/assets/images/room-placeholder.jpg';
                    ?>
                    <img src="<?= $mainImageUrl; ?>" alt="<?= htmlspecialchars($room->room_number); ?>" id="main-room-image" class="w-full h-full object-cover">
                </div>
                
                <?php if (!empty($roomImages) && count($roomImages) > 1): ?>
                    <div class="thumbnails flex flex-wrap gap-3">
                        <?php foreach ($roomImages as $index => $image): ?>
                            <?php if (isset($image->image_url) && !empty(trim($image->image_url))): ?>
                            <div class="thumbnail w-24 h-20 cursor-pointer rounded-md overflow-hidden border-2 transition-colors duration-300 <?= $index === 0 ? 'border-accent' : 'border-transparent hover:border-light-accent'; ?>" 
                                 data-image="<?= APP_URL . '/assets/images/rooms/' . htmlspecialchars($image->image_url); ?>">
                                <img src="<?= APP_URL . '/assets/images/rooms/' . htmlspecialchars($image->image_url); ?>" alt="Thumbnail <?= $index + 1; ?>" class="w-full h-full object-cover">
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Room Features & Description -->
            <div class="bg-darker-bg p-6 rounded-lg shadow-lg mb-8">
                <h3 class="text-2xl font-bold border-b border-dark-text pb-3 mb-4">Room Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                    <?php if (!empty($roomFacilities)): ?>
                        <?php foreach ($roomFacilities as $facility): ?>
                            <div class="flex items-center text-medium-text">
                                <i class="fas fa-check-circle text-accent mr-3"></i>
                                <span><?= htmlspecialchars($facility->name); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-medium-text">No specific facilities listed for this room.</p>
                    <?php endif; ?>
                </div>
                
                <div class="prose prose-invert max-w-none text-light-text">
                    <h4 class="text-xl font-semibold text-light-text">Description</h4>
                    <p class="text-medium-text"><?= nl2br(htmlspecialchars($room->description ?? 'No description available.')); ?></p>
                    <p class="mt-2 text-medium-text">
                        Category: <strong class="text-light-text"><?= htmlspecialchars($room->category_name ?? 'N/A'); ?></strong> - 
                        <?= nl2br(htmlspecialchars($room->category_description ?? '')); ?>
                    </p>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="bg-darker-bg p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold border-b border-dark-text pb-3 mb-4">Guest Reviews (<?= $totalReviews ?>)</h3>
                
                <?php if ($totalReviews > 0): ?>
                    <div class="bg-dark-bg p-4 rounded-lg border-l-4 border-warning mb-6">
                        <p class="font-semibold">Average Rating: 
                            <span class="inline-flex items-center gap-1 text-warning ml-2">
                                <?php for ($k = 1; $k <= 5; $k++): ?>
                                    <?php if ($k <= floor($averageRating)): ?><i class="fas fa-star"></i>
                                    <?php elseif ($k - 0.5 <= $averageRating): ?><i class="fas fa-star-half-alt"></i>
                                    <?php else: ?><i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </span>
                            <strong class="ml-2 text-light-text"><?= number_format($averageRating, 1); ?> out of 5</strong>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-b border-dark-text pb-4 last:border-b-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-bold text-light-text"><?= htmlspecialchars($review->user_fullname ?: 'Anonymous'); ?></h5>
                                    <div class="flex items-center gap-1 text-warning text-sm">
                                        <?php for ($j = 1; $j <= 5; $j++): ?>
                                            <i class="<?= ($j <= $review->rating) ? 'fas' : 'far'; ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-xs text-medium-text mb-2">Reviewed on: <?= htmlspecialchars(date('F j, Y', strtotime($review->created_at))); ?></p>
                                <p class="text-medium-text"><?= nl2br(htmlspecialchars($review->comment)); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-medium-text italic">No reviews yet for this room. Be the first to review!</p>
                    <?php endif; ?>
                </div>

                <!-- Review Form -->
                <div class="mt-8 pt-6 border-t border-dark-text">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (isset($userCanReview) && $userCanReview && isset($eligibleBookingIdForReview)): ?>
                            <h3 class="text-xl font-bold mb-4">Leave a Review</h3>
                            <form action="<?= APP_URL; ?>/review/create" method="POST">
                                <input type="hidden" name="room_id" value="<?= $room->room_id; ?>">
                                <input type="hidden" name="booking_id" value="<?= $eligibleBookingIdForReview; ?>">
                                <div class="form-group">
                                    <label class="form-label">Your Rating:</label>
                                    <div class="rating-stars flex flex-row-reverse justify-end items-center">
                                        <?php for($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="hidden peer" required/>
                                            <label for="star<?= $i ?>" title="<?= $i ?> stars" class="text-3xl text-medium-text cursor-pointer peer-hover:text-warning hover:text-warning peer-checked:text-warning">★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="comment" class="form-label">Your Review:</label>
                                    <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="Share your experience..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Submit Review</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info"><p><i class="fas fa-info-circle mr-2"></i>You can review this room after completing a booking for it.</p></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning"><p><i class="fas fa-sign-in-alt mr-2"></i><a href="<?= APP_URL; ?>/login?redirect=<?= urlencode(APP_URL . $_SERVER['REQUEST_URI']); ?>" class="font-bold underline">Login</a> to leave a review.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
        <!-- Kolom Sidebar Pemesanan -->
        <!-- PERUBAHAN DI SINI: tambahkan id="booking-sidebar" dan data-price-per-night -->
        <aside class="lg:col-span-1">
            <div id="booking-sidebar" class="bg-darker-bg p-6 rounded-lg shadow-lg sticky top-24" data-price-per-night="<?= $room->price_per_night ?? 0; ?>">
                <h3 class="text-xl font-bold text-center mb-4">Book This Room</h3>
                <div class="text-center mb-6">
                    <p class="text-3xl font-bold text-light-accent">Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></p>
                    <span class="text-medium-text">/ night</span>
                </div>
                
                <form action="<?= APP_URL; ?>/booking/create" method="POST" enctype="multipart/form-data">
                    <!-- ... (Isi form tetap sama) ... -->
                    <input type="hidden" name="room_id" value="<?= $room->room_id; ?>">
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="check_in" class="form-label">Check In</label>
                            <input type="date" id="check_in" name="check_in" class="form-control" min="<?= date('Y-m-d'); ?>" value="<?= htmlspecialchars($_GET['check_in'] ?? date('Y-m-d')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="check_out" class="form-label">Check Out</label>
                            <input type="date" id="check_out" name="check_out" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" value="<?= htmlspecialchars($_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'))); ?>" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div class="form-group">
                                <label for="adults" class="form-label">Adults</label>
                                <select id="adults" name="adults" class="form-control">
                                    <?php 
                                    $selectedAdults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
                                    $maxAdults = $room->capacity ?? 1;
                                    for ($i = 1; $i <= $maxAdults; $i++): ?>
                                        <option value="<?= $i; ?>" <?= ($selectedAdults == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="children" class="form-label">Children</label>
                                <select id="children" name="children" class="form-control">
                                    <?php 
                                    $selectedChildren = isset($_GET['children']) ? intval($_GET['children']) : 0;
                                    for ($i = 0; $i <= 5; $i++): ?>
                                        <option value="<?= $i; ?>" <?= ($selectedChildren == $i) ? 'selected' : ''; ?>><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="identity_file" class="form-label">Upload KTP <span class="text-error">*</span></label>
                            <input type="file" id="identity_file" name="identity_file" class="form-control file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-light-text hover:file:bg-secondary" accept="image/*,application/pdf" required>
                            <small class="text-medium-text text-xs mt-1 block">Format: JPG, PNG, PDF. Maks: 2MB</small>
                        </div>
                    </div>
                    
                    <div class="bg-primary/20 p-4 rounded-md my-6 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-medium-text">Price per night:</span>
                            <span class="font-semibold">Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-medium-text">Number of nights:</span>
                            <span id="num-nights" class="font-semibold">1</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 mt-2 border-t border-dark-text">
                            <span>Total:</span>
                            <span id="total-price">Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?></span>
                            <input type="hidden" name="total_price" id="total-price-input" value="<?= $room->price_per_night; ?>">
                        </div>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button type="submit" class="btn btn-primary w-full">Book Now</button>
                    <?php else: ?>
                        <a href="<?= APP_URL; ?>/login?redirect=<?= urlencode(APP_URL . $_SERVER['REQUEST_URI']); ?>" class="btn btn-primary w-full block">Login to Book</a>
                    <?php endif; ?>
                </form>
            </div>
        </aside>

    </div>
</div>

<!-- Similar Rooms (Kode tidak berubah, sudah benar) -->
<?php if (!empty($similarRooms) && count(array_filter($similarRooms, fn($sr) => $sr->room_id != $room->room_id)) > 0): ?>
<section class="container pb-12 md:pb-20">
    <div class="section-title">
        <h2>Similar Rooms</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php $similarRoomDisplayedCount = 0; ?>
        <?php foreach ($similarRooms as $sRoom): ?>
            <?php if ($sRoom->room_id != $room->room_id && $similarRoomDisplayedCount < 3): ?>
                <?php
                $sRoomImageUrl = !empty($sRoom->image_url) ? APP_URL . '/assets/images/rooms/' . htmlspecialchars($sRoom->image_url) : APP_URL . '/assets/images/room-placeholder.jpg';
                $currentParams = [];
                if (isset($_GET['check_in'])) $currentParams['check_in'] = $_GET['check_in'];
                if (isset($_GET['check_out'])) $currentParams['check_out'] = $_GET['check_out'];
                if (isset($_GET['adults'])) $currentParams['adults'] = $_GET['adults'];
                if (isset($_GET['children'])) $currentParams['children'] = $_GET['children'];
                $similarRoomQueryString = !empty($currentParams) ? '?' . http_build_query($currentParams) : '';
                $sRoomDetailUrl = APP_URL . '/room/view/' . $sRoom->room_id . $similarRoomQueryString;
                ?>
                <div class="card group flex flex-col transition-transform duration-300 hover:-translate-y-2">
                    <div class="relative">
                        <div class="h-56 overflow-hidden">
                            <img src="<?= $sRoomImageUrl; ?>" alt="<?= htmlspecialchars($sRoom->room_number); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="absolute top-3 right-3 bg-accent text-light-text text-xs font-semibold px-3 py-1 rounded-full">
                            <?= htmlspecialchars($sRoom->category_name); ?>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($sRoom->room_number); ?></h3>
                        <div class="flex justify-between text-medium-text text-sm mb-4">
                            <span class="flex items-center"><i class="fas fa-user mr-2"></i> Max <?= htmlspecialchars($sRoom->capacity); ?> Persons</span>
                            <span class="flex items-center"><i class="fas fa-vector-square mr-2"></i> <?= htmlspecialchars($sRoom->size_sqm); ?> sqm</span>
                        </div>
                        <div class="mb-4 mt-auto">
                            <p class="text-2xl font-semibold text-light-accent">
                                Rp <?= number_format($sRoom->price_per_night, 0, ',', '.'); ?>
                                <span class="text-base font-normal text-medium-text">/ night</span>
                            </p>
                        </div>
                        <div class="flex justify-between items-center border-t border-dark-text pt-4">
                            <div class="rating flex items-center gap-1 text-warning">
                                <?php
                                $sRoomAvgRating = $sRoom->average_rating ?? 0;
                                $sRoomRevCount = $sRoom->total_reviews ?? 0;
                                ?>
                                <?php if ($sRoomRevCount > 0): ?>
                                    <?php for ($k = 1; $k <= 5; $k++): ?>
                                        <?php if ($k <= floor($sRoomAvgRating)): ?><i class="fas fa-star"></i>
                                        <?php elseif ($k - 0.5 <= $sRoomAvgRating): ?><i class="fas fa-star-half-alt"></i>
                                        <?php else: ?><i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="text-medium-text text-sm ml-1">(<?= number_format($sRoomAvgRating, 1); ?>)</span>
                                <?php else: ?>
                                    <span class="text-medium-text text-sm italic">No reviews</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= $sRoomDetailUrl; ?>" class="btn btn-primary px-4 py-2 text-sm">View Details</a>
                        </div>
                    </div>
                </div>
            <?php $similarRoomDisplayedCount++; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>


<!-- <script>
// Script Anda tidak perlu diubah, karena ID elemen tidak berubah.
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const numNightsElement = document.getElementById('num-nights');
    const totalPriceElement = document.getElementById('total-price');
    const totalPriceInput = document.getElementById('total-price-input');
    const pricePerNight = parseFloat(<?= $room->price_per_night ?? 0; ?>);
    
    function updatePricing() {
        if (!checkInInput || !checkOutInput || !numNightsElement || !totalPriceElement || !totalPriceInput) {
            console.error('One or more pricing elements are missing for booking form.');
            return;
        }
        
        const checkInDateStr = checkInInput.value;
        const checkOutDateStr = checkOutInput.value;

        if (!checkInDateStr || !checkOutDateStr) {
            numNightsElement.textContent = '0';
            totalPriceElement.textContent = 'Rp 0';
            if (totalPriceInput) totalPriceInput.value = 0;
            return;
        }
        
        const checkIn = new Date(checkInDateStr);
        const checkOut = new Date(checkOutDateStr);

        if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime()) || checkOut <= checkIn) {
            numNightsElement.textContent = '0';
            totalPriceElement.textContent = 'Rp 0';
            if (totalPriceInput) totalPriceInput.value = 0;
            return;
        }

        const timeDiff = checkOut.getTime() - checkIn.getTime();
        const nightCount = Math.max(0, Math.ceil(timeDiff / (1000 * 3600 * 24)));
        
        numNightsElement.textContent = nightCount;
        const total = nightCount * pricePerNight;
        totalPriceElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        if (totalPriceInput) totalPriceInput.value = total;
    }
    
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            if (isNaN(checkInDate.getTime())) {
                 updatePricing();
                 return;
            }

            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            
            const year = nextDay.getFullYear();
            const month = (nextDay.getMonth() + 1).toString().padStart(2, '0');
            const day = nextDay.getDate().toString().padStart(2, '0');
            
            const minCheckoutDate = `${year}-${month}-${day}`;
            checkOutInput.min = minCheckoutDate;
            
            if (new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = minCheckoutDate;
            }
            updatePricing();
        });
        
        checkOutInput.addEventListener('change', updatePricing);
        
        if (checkInInput.value && checkOutInput.value) {
            updatePricing();
        }
    }
    
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-room-image');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Perbaiki logika untuk border aktif pada thumbnail
                thumbnails.forEach(thumb => {
                    thumb.classList.remove('border-accent');
                    thumb.classList.add('border-transparent');
                });
                this.classList.remove('border-transparent');
                this.classList.add('border-accent');
                
                const newImageSrc = this.getAttribute('data-image');
                if (newImageSrc) {
                    mainImage.src = newImageSrc;
                }
            });
        });
    }
});
</script> -->

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>