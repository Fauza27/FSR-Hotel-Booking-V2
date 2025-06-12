<?php
$pageTitle = 'Contact Us';
$currentPage = 'contact';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<section class="container my-8 md:my-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold">Contact Us</h1>
    </div>

    <div class="card max-w-2xl mx-auto p-6 md:p-8">
        <p class="text-center text-medium-text mb-6">Jika Anda memiliki pertanyaan, kritik, atau saran, silakan hubungi kami melalui form di bawah ini.</p>
        
        <form action="<?= APP_URL ?>/contact" method="POST">
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-full mt-2">Send Message</button>
        </form>
        
        <div class="mt-10 pt-6 border-t border-dark-text text-center">
            <h4 class="text-xl font-semibold mb-4">Hotel Contact</h4>
            <div class="space-y-2 text-medium-text">
                <p><i class="fas fa-map-marker-alt text-accent mr-2"></i> FSR Hotel Street , Samarinda City, Indonesia</p>
                <p><i class="fas fa-phone-alt text-accent mr-2"></i> +62 123 4567 890</p>
                <p><i class="fas fa-envelope text-accent mr-2"></i> info@fsrhotel.com</p>
            </div>
        </div>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>