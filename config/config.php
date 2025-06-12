<?php
// Informasi dasar aplikasi // Komentar yang mengelompokkan konstanta terkait informasi aplikasi.
define('APP_NAME', 'FSR Hotel'); // Mendefinisikan sebuah konstanta bernama APP_NAME. Konstanta ini menyimpan nama aplikasi. Berguna untuk ditampilkan di judul halaman, email, dll.
define('APP_URL', 'http://localhost/ProjectAkhirWebV2'); // Mendefinisikan konstanta APP_URL.Ini adalah URL dasar (root URL) dari aplikasi web. Penting untuk membuat link absolut, redirect, dan path aset.
define('APP_VERSION', '1.0.0'); // Mendefinisikan konstanta APP_VERSION. Menyimpan nomor versi aplikasi, berguna untuk tracking atau informasi.

// Konfigurasi database // Komentar yang mengelompokkan konstanta terkait koneksi database.
define('DB_HOST', 'localhost'); // Mendefinisikan konstanta DB_HOST. Ini adalah alamat server tempat database berjalan (hostname atau IP address).'localhost' berarti database berjalan di mesin yang sama dengan server web.
define('DB_NAME', 'hotel_bookingv5'); // Mendefinisikan konstanta DB_NAME. Nama database yang akan digunakan oleh aplikasi ini.
define('DB_USER', 'root'); // Mendefinisikan konstanta DB_USER. Username yang digunakan untuk terhubung ke server database. 'root' adalah username default dengan hak akses penuh di banyak instalasi MySQL/MariaDB.
define('DB_PASS', ''); // Mendefinisikan konstanta DB_PASS. Password untuk username database yang didefinisikan di DB_USER. String kosong berarti tidak ada password (umum untuk lingkungan pengembangan lokal).
define('DB_PORT', 3307); // Mendefinisikan konstanta DB_PORT. Nomor port yang digunakan oleh server database untuk menerima koneksi. Port default MySQL adalah 3306. Penggunaan 3307 menunjukkan konfigurasi custom.

// Konfigurasi path // Komentar yang mengelompokkan konstanta terkait path direktori.
define('ROOT_PATH', dirname(__DIR__)); // Mendefinisikan konstanta ROOT_PATH. __DIR__ adalah "magic constant" PHP yang mengembalikan path absolut ke direktori tempat file ini berada (yaitu, 'config/'). dirname() adalah fungsi PHP yang mengembalikan path direktori induk. Jadi, dirname(__DIR__) akan menghasilkan path absolut ke direktori root proyek Anda (direktori yang berisi folder 'config').
define('CONTROLLER_PATH', ROOT_PATH . '/controllers/'); // Mendefinisikan konstanta CONTROLLER_PATH. Path absolut ke direktori 'controllers'. Dibuat dengan menggabungkan ROOT_PATH dan string '/controllers/'.
define('MODEL_PATH', ROOT_PATH . '/models/'); // Mendefinisikan konstanta MODEL_PATH. Path absolut ke direktori 'models'.
define('VIEW_PATH', ROOT_PATH . '/views/'); // Mendefinisikan konstanta VIEW_PATH. Path absolut ke direktori 'views'.
define('ASSET_PATH', ROOT_PATH . '/assets/'); // Mendefinisikan konstanta ASSET_PATH. Path absolut ke direktori 'assets' (untuk CSS, JS, gambar, dll.).

// Konfigurasi session // Komentar yang mengelompokkan pengaturan terkait session PHP.
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7); // Menggunakan fungsi ini_set() untuk mengubah konfigurasi PHP saat runtime.'session.cookie_lifetime' mengatur berapa lama (dalam detik) cookie session akan disimpan di browser pengguna. 60 * 60 * 24 * 7 = 604800 detik, yaitu 1 minggu. Jika 0, cookie berlaku sampai browser ditutup.
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7); // 'session.gc_maxlifetime' mengatur berapa lama (dalam detik) data session di sisi server dianggap valid sebelum bisa dihapus oleh Garbage Collector (GC). Juga diatur ke 1 minggu. Sebaiknya sama atau lebih besar dari cookie_lifetime.
session_start(); // Memulai session baru atau melanjutkan session yang sudah ada. Ini harus dipanggil sebelum ada output HTML/teks lain ke browser dan sebelum mengakses variabel $_SESSION.

// Konfigurasi error reporting // Komentar yang mengelompokkan pengaturan terkait pelaporan error PHP.
ini_set('display_errors', 1); // Mengatur apakah error PHP akan ditampilkan langsung di browser. '1' berarti error akan ditampilkan. Berguna saat development. Di lingkungan produksi, ini sebaiknya diatur ke '0' dan error di-log ke file.
error_reporting(E_ALL); // Mengatur level error PHP yang akan dilaporkan. E_ALL berarti semua jenis error, warning, dan notice akan dilaporkan.

// Time zone // Komentar untuk pengaturan zona waktu.
date_default_timezone_set('Asia/Jakarta'); // Mengatur zona waktu default yang akan digunakan oleh semua fungsi tanggal dan waktu di PHP.Penting untuk konsistensi data waktu, terutama jika aplikasi menangani jadwal atau timestamp.

// Konfigurasi upload // Komentar yang mengelompokkan konstanta terkait upload file.
define('UPLOAD_PATH', ROOT_PATH . '/assets/images/rooms/'); // Mendefinisikan konstanta UPLOAD_PATH. Path absolut ke direktori tempat file yang di-upload (misalnya gambar kamar) akan disimpan.
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']); // Mendefinisikan konstanta ALLOWED_EXTENSIONS. Sebuah array yang berisi daftar ekstensi file yang diizinkan untuk di-upload.
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // Mendefinisikan konstanta MAX_FILE_SIZE. Ukuran file maksimum yang diizinkan untuk di-upload, dalam byte. 5 * 1024 * 1024 = 5,242,880 byte, yaitu 5 Megabyte (MB).

// Konfigurasi upload avatar pengguna
define('AVATAR_UPLOAD_DIR', 'images/avatars/'); // Direktori relatif terhadap ASSET_PATH
define('AVATAR_UPLOAD_PATH', ASSET_PATH . AVATAR_UPLOAD_DIR); // Path fisik server untuk avatar
define('ALLOWED_AVATAR_EXTENSIONS', ['jpg', 'jpeg', 'png']);
define('MAX_AVATAR_SIZE', 2 * 1024 * 1024); // 2MB untuk avatar

// Konfigurasi Email (PHPMailer)
define('MAIL_HOST', 'smtp.example.com'); // Ganti dengan SMTP host Anda (mis. smtp.gmail.com)
define('MAIL_PORT', 587); // Port SMTP (587 untuk TLS, 465 untuk SSL)
define('MAIL_USERNAME', 'your_email@example.com'); // Email pengirim
define('MAIL_PASSWORD', 'your_email_password'); // Password email pengirim
define('MAIL_ENCRYPTION', 'tls'); // 'tls' atau 'ssl'
define('MAIL_FROM_ADDRESS', 'noreply@fsrhotel.com');
define('MAIL_FROM_NAME', APP_NAME);

// Konfigurasi Reset Password
define('PASSWORD_RESET_TOKEN_LIFETIME', 15); // Menit
define('PASSWORD_RESET_OTP_LENGTH', 6); // Panjang OTP

// Helper function untuk URL aplikasi
function base_url($path = '') {
    return APP_URL . '/' . ltrim($path, '/');
}

// Helper function untuk URL aset
function asset_url($path = '') {
    return base_url('assets/' . ltrim($path, '/'));
}