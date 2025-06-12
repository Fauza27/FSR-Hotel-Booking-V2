<?php
// ...
?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-darker-bg pt-16 pb-8 mt-auto">
        <div class="container">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 mb-8">
                <!-- About Section -->
                <div class="footer-about">
                    <a href="<?= APP_URL ?>" class="text-xl font-bold text-accent mb-4 inline-block">FSR<span class="text-light-text">Hotel</span></a>
                    <p class="text-medium-text mb-6">
                        Experience luxury and comfort in our modern hotel. Book your stay with us for an unforgettable experience.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-dark-bg rounded-full text-light-accent hover:bg-primary hover:text-light-text transition-all duration-300 transform hover:-translate-y-1"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-dark-bg rounded-full text-light-accent hover:bg-primary hover:text-light-text transition-all duration-300 transform hover:-translate-y-1"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-dark-bg rounded-full text-light-accent hover:bg-primary hover:text-light-text transition-all duration-300 transform hover:-translate-y-1"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-dark-bg rounded-full text-light-accent hover:bg-primary hover:text-light-text transition-all duration-300 transform hover:-translate-y-1"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-links">
                    <h3 class="text-lg font-semibold text-light-text mb-4 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-12 after:h-0.5 after:bg-accent">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="<?= APP_URL ?>" class="text-medium-text hover:text-accent">Home</a></li>
                        <li><a href="<?= APP_URL ?>/rooms" class="text-medium-text hover:text-accent">Rooms</a></li>
                        <li><a href="<?= APP_URL ?>/about" class="text-medium-text hover:text-accent">About Us</a></li>
                        <li><a href="<?= APP_URL ?>/contact" class="text-medium-text hover:text-accent">Contact</a></li>
                    </ul>
                </div>
                
                <!-- Contact Us -->
                <div class="footer-contact">
                    <h3 class="text-lg font-semibold text-light-text mb-4 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-12 after:h-0.5 after:bg-accent">Contact Us</h3>
                    <ul class="space-y-4 text-medium-text">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-light-accent mt-1 mr-4"></i>
                            <span>FSR Hotel Street, Samarinda City, Indonesia</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt text-light-accent mt-1 mr-4"></i>
                            <span>+62 123 4567 890</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-light-accent mt-1 mr-4"></i>
                            <span>info@fsrhotel.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-8 border-t border-dark-text text-medium-text text-sm">
                <p>© <?= date('Y'); ?> FSR Hotel. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>