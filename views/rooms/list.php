<?php
$pageTitle = 'Our Rooms';
$currentPage = 'rooms';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<!-- Rooms Banner -->
<section class="bg-gradient-to-r from-primary to-[#4a148c] py-12 md:py-20">
    <div class="container text-center">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-shadow-md mb-4">Our Rooms</h1>
            <p class="text-lg text-medium-text">Choose from our selection of comfortable and luxurious rooms.</p>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="container py-8 md:py-12">
    <div class="bg-darker-bg p-6 rounded-lg shadow-xl">
        <form action="<?= APP_URL ?>/rooms" method="GET">
            <!-- Grid untuk input filter yang responsif -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                <div class="form-group">
                    <label for="check_in" class="form-label">Check In</label>
                    <input type="date" id="check_in" name="check_in" class="form-control" min="<?= date('Y-m-d'); ?>" value="<?= htmlspecialchars(isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d')); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="check_out" class="form-label">Check Out</label>
                    <input type="date" id="check_out" name="check_out" class="form-control" min="<?= date('Y-m-d', strtotime('+1 day')); ?>" value="<?= htmlspecialchars(isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'))); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="adults" class="form-label">Adults</label>
                    <select id="adults" name="adults" class="form-control">
                        <?php 
                        $selectedAdults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
                        for ($i = 1; $i <= 10; $i++): ?>
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
                
                <div class="form-group">
                    <label for="category" class="form-label">Room Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php 
                        $selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : '';
                        foreach ($categories as $category): ?>
                            <option value="<?= $category->category_id; ?>" <?= ($selectedCategory == $category->category_id) ? 'selected' : ''; ?>><?= htmlspecialchars($category->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-full mt-2">Search Available Rooms</button>
        </form>
    </div>
</section>

<!-- Rooms List -->
<section class="container pb-12 md:pb-20">
    <?php if (empty($rooms)): ?>
        <div class="alert alert-info">
            <p><i class="fas fa-info-circle mr-2"></i>No rooms available for the selected dates or criteria. Please try different dates or criteria.</p>
        </div>
    <?php else: ?>
        <div class="section-title">
            <h2>Available Rooms</h2>
        </div>
        
        <!-- Grid untuk daftar kamar yang responsif -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($rooms as $room): ?>
                <?php
                // Logika untuk URL gambar dan query string tetap sama
                $imageUrl = !empty($room->image_url) ? APP_URL . '/assets/images/rooms/' . $room->image_url : APP_URL . '/assets/images/room-placeholder.jpg';
                $queryParams = [];
                if (isset($_GET['check_in'])) $queryParams['check_in'] = $_GET['check_in'];
                if (isset($_GET['check_out'])) $queryParams['check_out'] = $_GET['check_out'];
                if (isset($_GET['adults'])) $queryParams['adults'] = $_GET['adults'];
                if (isset($_GET['children'])) $queryParams['children'] = $_GET['children'];
                $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
                $detailUrl = APP_URL . '/room/view/' . $room->room_id . $queryString;
                ?>
                <!-- Menggunakan komponen .card yang sudah didefinisikan -->
                <div class="card group flex flex-col transition-transform duration-300 hover:-translate-y-2">
                    <div class="relative">
                        <div class="h-56 overflow-hidden">
                            <img src="<?= $imageUrl; ?>" alt="<?= htmlspecialchars($room->room_number); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div class="absolute top-3 right-3 bg-accent text-light-text text-xs font-semibold px-3 py-1 rounded-full">
                            <?= htmlspecialchars($room->category_name); ?>
                        </div>
                    </div>
                    
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($room->room_number); ?></h3>
                        
                        <div class="flex justify-between text-medium-text text-sm mb-4">
                            <span class="flex items-center"><i class="fas fa-user mr-2"></i> Max <?= htmlspecialchars($room->capacity); ?> Persons</span>
                            <span class="flex items-center"><i class="fas fa-vector-square mr-2"></i> <?= htmlspecialchars($room->size_sqm); ?> sqm</span>
                        </div>
                        
                        <div class="mb-4 mt-auto">
                            <p class="text-2xl font-semibold text-light-accent">
                                Rp <?= number_format($room->price_per_night, 0, ',', '.'); ?>
                                <span class="text-base font-normal text-medium-text">/ night</span>
                            </p>
                        </div>
                        
                        <div class="flex justify-between items-center border-t border-dark-text pt-4">
                            <div class="rating flex items-center gap-1 text-warning">
                                <?php
                                $avgRating = $room->average_rating ?? 0;
                                $revCount = $room->total_reviews ?? 0;
                                ?>
                                <?php if ($revCount > 0): ?>
                                    <?php for ($k = 1; $k <= 5; $k++): ?>
                                        <i class="<?= ($k <= $avgRating) ? 'fas' : 'far'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                    <span class="text-medium-text text-sm ml-1">(<?= number_format($avgRating, 1); ?>)</span>
                                <?php else: ?>
                                    <span class="text-medium-text text-sm italic">No reviews</span>
                                <?php endif; ?>
                            </div>
                            
                            <a href="<?= $detailUrl; ?>" class="btn btn-primary px-4 py-2 text-sm">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>