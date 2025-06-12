<?php
$pageTitle = 'About Us';
$currentPage = 'about';
include_once(VIEW_PATH . 'layouts/header.php');
?>

<section class="container my-8 md:my-16">
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold">About FSR Hotel</h1>
    </div>

    <!-- Owner Card -->
    <div class="card max-w-4xl mx-auto mb-8 p-6 md:p-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="flex-shrink-0">
                <img src="assets/images/owner fsr hotel v3.png" alt="Foto Owner FSR Hotel" class="w-48 h-48 md:w-64 md:h-64 object-cover rounded-full border-4 border-accent shadow-lg">
            </div>
            <div class="text-center md:text-left">
                <h3 class="text-2xl text-light-text">Fauza Sapri Rizky</h3>
                <p class="text-lg text-light-accent mb-4">Founder, CEO, Commissioner</p>
                <p class="text-medium-text text-base leading-relaxed">
                    Selamat datang di FSR Hotel! Kami, owner FSR Hotel, merasa terhormat menyambut Anda. Visi kami adalah menciptakan oase kenyamanan dan kemewahan yang tak terlupakan. Kami berharap pengalaman Anda melebihi ekspektasi.
                </p>
            </div>
        </div> 
    </div>

    <!-- Hotel Description Card -->
    <div class="card max-w-4xl mx-auto p-6 md:p-8">
        <div class="text-medium-text space-y-4">
            <p>FSR Hotel adalah hotel modern yang menawarkan kenyamanan, kemewahan, dan pelayanan terbaik. Kami berlokasi strategis di pusat kota, dekat dengan berbagai destinasi wisata dan bisnis.</p>
            <p>Dengan kamar elegan, fasilitas lengkap, dan staf profesional, kami berkomitmen memberikan pengalaman menginap yang tak terlupakan.</p>
            <ul class="list-disc pl-5 space-y-2 text-medium-text">
                <li>Kamar modern & nyaman</li>
                <li>Restoran & layanan kamar 24 jam</li>
                <li>Fasilitas spa, gym, dan kolam renang</li>
                <li>Wi-Fi gratis di seluruh area hotel</li>
                <li>Lokasi strategis di pusat kota</li>
            </ul>
        </div>
    </div>
</section>

<?php include_once(VIEW_PATH . 'layouts/footer.php'); ?>