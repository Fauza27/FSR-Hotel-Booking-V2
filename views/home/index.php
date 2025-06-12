<?php
$pageTitle = 'Welcome';
$currentPage = 'home';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Hero Section -->
<!-- Untuk background complex seperti ini, lebih mudah menggunakan div terpisah atau pseudo-element -->
<section class="hero relative text-center py-20 md:py-32 text-white overflow-hidden">
    <!-- Background Layer -->
    <div class="absolute inset-0 bg-cover bg-center bg-blend-overlay bg-[url('../images/hero-bg.jpg')] bg-gradient-to-r from-primary to-[#4a148c] z-0"></div>
    <!-- Content Layer -->
    <div class="container relative z-10">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 text-shadow-lg">Experience Luxury & Comfort</h1>
            <p class="text-lg md:text-xl mb-8 text-medium-text">Book your stay with us and enjoy a world-class hospitality experience in our modern and elegant rooms.</p>
            <a href="<?= APP_URL ?>/rooms" class="btn btn-primary !text-lg !px-8 !py-4">Explore Rooms</a>
        </div>
    </div>
</section>

<!-- Search Form -->
<section class="container -mt-12 mb-16">
    <div class="bg-darker-bg p-6 rounded-lg shadow-2xl">
        <form action="<?= APP_URL ?>/rooms" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="check_in" class="block mb-2 text-sm font-medium text-medium-text">Check In</label>
                    <input type="date" id="check_in" name="check_in" class="w-full p-3 border border-dark-text rounded bg-dark-bg text-light-text focus:outline-none focus:border-accent" min="<?= date('Y-m-d'); ?>" required>
                </div>
                <div>
                    <label for="check_out" class="block mb-2 text-sm font-medium text-medium-text">Check Out</label>
                    <input type="date" id="check_out" name="check_out" class="w-full p-3 border border-dark-text rounded bg-dark-bg text-light-text focus:outline-none focus:border-accent" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" required>
                </div>
                <div>
                    <label for="adults" class="block mb-2 text-sm font-medium text-medium-text">Adults</label>
                    <select id="adults" name="adults" class="w-full p-3 border border-dark-text rounded bg-dark-bg text-light-text focus:outline-none focus:border-accent">
                        <?php for ($i = 1; $i <= 10; $i++): ?><option value="<?= $i; ?>"><?= $i; ?></option><?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label for="children" class="block mb-2 text-sm font-medium text-medium-text">Children</label>
                    <select id="children" name="children" class="w-full p-3 border border-dark-text rounded bg-dark-bg text-light-text focus:outline-none focus:border-accent">
                        <?php for ($i = 0; $i <= 5; $i++): ?><option value="<?= $i; ?>"><?= $i; ?></option><?php endfor; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-4">Check Availability</button>
        </form>
    </div>
</section>

<!-- Featured Rooms -->
<section class="rooms-section py-16 md:py-24">
    <div class="container">
        <div class="section-title">
            <h2>Featured Rooms</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featuredRooms as $room): ?>
                <div class="card group transform transition duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <div class="relative h-56 overflow-hidden">
                        <img src="<?= !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . $room->image_url : APP_URL . '/assets/images/room-placeholder.jpg'; ?>" alt="<?= $room->room_number; ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-3 right-3 bg-accent text-light-text text-xs font-semibold px-3 py-1 rounded-full"><?= $room->category_name; ?></div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?= $room->room_number; ?></h3>
                        
                        <div class="flex justify-between text-medium-text text-sm mb-4">
                            <span><i class="fas fa-user mr-2"></i> Max <?= $room->capacity; ?> Persons</span>
                            <span><i class="fas fa-vector-square mr-2"></i> <?= $room->size_sqm; ?> sqm</span>
                        </div>
                        
                        <div class="text-2xl font-semibold text-light-accent mb-4">
                            Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?> <span class="text-sm font-normal text-medium-text">/ night</span>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-dark-text pt-4">
                            <div class="flex items-center text-warning">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                <span class="ml-2 text-medium-text text-sm">4.5</span>
                            </div>
                            
                            <a href="<?= APP_URL . '/room/view/' . $room->room_id; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?= APP_URL ?>/rooms" class="btn btn-secondary">View All Rooms</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="container pb-16 md:pb-24">
    <div class="section-title">
        <h2>Why Choose Us</h2>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Feature 1 -->
        <div class="card text-center p-8">
            <i class="fas fa-medal text-5xl text-accent mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Quality Service</h3>
            <p class="text-medium-text">We provide top-notch service to ensure your stay is comfortable and memorable.</p>
        </div>
        <!-- Feature 2 -->
        <div class="card text-center p-8">
            <i class="fas fa-map-marker-alt text-5xl text-accent mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Prime Location</h3>
            <p class="text-medium-text">Our hotel is located in the heart of the city, close to major attractions.</p>
        </div>
        <!-- Feature 3 -->
        <div class="card text-center p-8">
            <i class="fas fa-utensils text-5xl text-accent mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Fine Dining</h3>
            <p class="text-medium-text">Enjoy exquisite meals prepared by our talented chefs using the finest ingredients.</p>
        </div>
        <!-- Feature 4 -->
        <div class="card text-center p-8">
            <i class="fas fa-spa text-5xl text-accent mb-4"></i>
            <h3 class="text-xl font-bold mb-2">Wellness Facilities</h3>
            <p class="text-medium-text">Relax and rejuvenate with our wellness facilities including spa, gym, and pool.</p>
        </div>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>