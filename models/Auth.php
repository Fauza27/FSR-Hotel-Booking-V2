<?php
/*
Fungsi: Mengelola aspek otentikasi dan otorisasi pengguna.

Tugas Spesifik:
    Membuat, memverifikasi, dan menghapus token "remember me" (disimpan di tabel user_tokens).
    Mengecek apakah pengguna sedang login (berdasarkan session).
    Mengambil data pengguna yang sedang login (menggunakan User model).
    Mengecek apakah pengguna yang login adalah admin (berdasarkan session).

Ketergantungan: Menggunakan Database.php untuk token dan User.php untuk mendapatkan detail pengguna.
*/

class Auth { // Deklarasi kelas Auth. Kelas ini akan menangani logika terkait otentikasi pengguna.
    private $db; // Properti privat bernama $db. Ini akan menyimpan instance dari kelas Database kita.

    public function __construct() { // Metode konstruktor, otomatis dipanggil saat objek Auth dibuat.
        $this->db = new Database(); // Membuat instance baru dari kelas Database (dari config/database.php)
                                    // dan menugaskannya ke properti $this->db.
                                    // Ini memberikan objek Auth akses ke metode-metode database (query, bind, execute, dll.).
    }
    
    //FITUR DIBAWAH INI MASIH BELUM BERFUNGSI DAN AKAN DI PERBAIKI DI UPDATE BERIKUTNYA
    /*
    // Create remember me token
    public function createRememberToken($userId) { // Metode publik untuk membuat token "remember me". // Menerima $userId sebagai argumen.
        // Generate a random token
        $token = bin2hex(random_bytes(32)); // Menghasilkan token acak yang kuat secara kriptografis. random_bytes(32) menghasilkan 32 byte data biner acak. bin2hex() mengubah data biner tersebut menjadi representasi string heksadesimal. Token ini akan disimpan di cookie pengguna (bagian yang tidak di-hash).
        $hashedToken = password_hash($token, PASSWORD_DEFAULT); // Melakukan hashing pada token acak ($token) menggunakan algoritma default PHP (saat ini bcrypt). $hashedToken ini yang akan disimpan di database untuk perbandingan yang aman. Ini adalah praktik keamanan yang baik, mirip dengan hashing password.

        // Store token in database // Komentar yang menjelaskan blok kode berikutnya.
        $this->db->query("INSERT INTO user_tokens (user_id, token, expires_at, token_type) VALUES (:user_id, :token, DATE_ADD(NOW(), INTERVAL 30 DAY), 'remember_me')
        "); // Query untuk memasukkan data token baru ke dalam tabel 'user_tokens'. Kolom: user_id, token (yang di-hash), expires_at (waktu kadaluarsa), token_type. DATE_ADD(NOW(), INTERVAL 30 DAY) adalah fungsi SQL untuk mengatur waktu kadaluarsa token menjadi 30 hari dari sekarang. 'remember_me' adalah tipe token yang dibuat.

        $this->db->bind(':user_id', $userId); // Mengikat nilai $userId ke placeholder :user_id dalam query.
        $this->db->bind(':token', $hashedToken); // Mengikat nilai $hashedToken ke placeholder :token dalam query.

        if($this->db->execute()) { // Mengeksekusi query INSERT yang sudah disiapkan dan diikat.
            // Return the unhashed token to be stored in cookie // Jika query berhasil dieksekusi.
            return $userId . ':' . $token; // Mengembalikan string yang terdiri dari ID pengguna, diikuti titik dua, diikuti token ASLI (yang belum di-hash). Format ini akan disimpan di cookie pengguna. ID pengguna disertakan untuk memudahkan pencarian di database saat verifikasi.
        }

        return false; // Jika eksekusi query gagal, kembalikan false.
    }

    // Verify remember token // Komentar yang menjelaskan fungsi metode di bawah.
    public function verifyRememberToken($tokenString) { // Metode publik untuk memverifikasi token "remember me" yang diambil dari cookie. $tokenString adalah nilai dari cookie (misal: "123:abc123xyz").
        // Split token string into user_id and token // Komentar untuk langkah berikutnya.
        $parts = explode(':', $tokenString); // Memecah $tokenString berdasarkan karakter ':' menjadi sebuah array. $parts[0] akan berisi user_id, $parts[1] akan berisi token mentah dari cookie.

        if(count($parts) != 2) { // Memeriksa apakah $tokenString memiliki format yang benar (terdiri dari dua bagian setelah dipecah).
            return false; // Jika formatnya salah, token tidak valid, kembalikan false.
        }

        $userId = $parts[0]; // Mengambil ID pengguna dari bagian pertama array.
        $token = $parts[1];  // Mengambil token mentah (belum di-hash) dari bagian kedua array.

        // Get stored token from database // Komentar untuk langkah berikutnya.
        $this->db->query("SELECT * FROM user_tokens WHERE user_id = :user_id AND token_type = 'remember_me' AND expires_at > NOW()"); // Query untuk mencari token di tabel 'user_tokens' yang: - Milik user_id yang sesuai. - Bertipe 'remember_me'. - Belum kadaluarsa (expires_at lebih besar dari waktu saat ini NOW()).

        $this->db->bind(':user_id', $userId); // Mengikat $userId ke placeholder :user_id.

        $row = $this->db->single(); // Mengeksekusi query dan mengambil satu baris hasil (jika ada token yang cocok).

        if($row && password_verify($token, $row->token)) { // Memeriksa dua kondisi: 1. $row: Apakah token ditemukan di database (artinya $row tidak false/null). 2. password_verify($token, $row->token): Apakah token mentah dari cookie ($token)    cocok dengan token yang di-hash di database ($row->token).    password_verify() adalah fungsi yang aman untuk perbandingan ini.
            // Token is valid, return user ID // Jika kedua kondisi terpenuhi, token valid.
            return $userId; // Kembalikan ID pengguna, menandakan otentikasi berhasil melalui "remember me".
        }

        return false; // Jika token tidak ditemukan atau tidak cocok, kembalikan false.
    }

    // Delete remember token // Komentar yang menjelaskan fungsi metode di bawah.
    public function deleteRememberToken($tokenString) { // Metode publik untuk menghapus token "remember me" dari database. Biasanya dipanggil saat pengguna logout atau jika token dicurigai bocor.
        // Split token string into user_id and token // Langkah awal, sama seperti verify.
        $parts = explode(':', $tokenString);

        if(count($parts) != 2) { // Validasi format.
            return false;
        }

        $userId = $parts[0]; // Ambil ID pengguna. Token mentahnya tidak perlu di sini, hanya user_id.

        // Delete token from database // Komentar untuk langkah berikutnya.
        $this->db->query("DELETE FROM user_tokens WHERE user_id = :user_id AND token_type = 'remember_me'"); // Query untuk menghapus semua token bertipe 'remember_me' yang milik $userId tertentu.
            // Bisa jadi ada beberapa token "remember_me" untuk satu user jika login dari banyak device,
            // jadi query ini akan menghapus semua yang terkait dengan user tersebut jika logout dari satu device (tergantung strategi).
            // Atau, jika ingin menghapus token spesifik, perlu juga membandingkan hash tokennya, tapi di sini hanya berdasarkan user_id.

        $this->db->bind(':user_id', $userId); // Mengikat $userId.

        return $this->db->execute(); // Mengeksekusi query DELETE dan mengembalikan status eksekusi (true jika berhasil, false jika gagal).
    }
    */

    // Metode publik untuk mengecek apakah pengguna saat ini sudah login.
    public function isLoggedIn() { 
        return isset($_SESSION['user_id']); // Mengembalikan true jika variabel session 'user_id' sudah di-set (ada nilainya). Mengembalikan false jika belum di-set. Ini adalah cara standar untuk mengecek status login berbasis session.
    }

    // Metode publik untuk mendapatkan detail data pengguna yang sedang login.
    public function getLoggedInUser() { 
        if($this->isLoggedIn()) { // Pertama, cek apakah pengguna memang sudah login menggunakan metode isLoggedIn() dari kelas ini.
            $userModel = new User(); // Membuat instance baru dari kelas User (models/User.php). Ini adalah dependensi. Dalam arsitektur yang lebih besar, ini bisa di-inject (Dependency Injection).
            return $userModel->getUserById($_SESSION['user_id']); // Memanggil metode getUserById() dari objek $userModel untuk mengambil data lengkap pengguna dari database berdasarkan user_id yang disimpan di session. Mengembalikan objek pengguna jika ditemukan, atau false.
        }

        return false; // Jika pengguna tidak login, kembalikan false.
    }

    // Metode publik untuk mengecek apakah pengguna yang sedang login memiliki peran sebagai admin.
    public function isAdmin() { 
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
        // Mengembalikan true jika:
        // 1. Variabel session 'user_role' sudah di-set, DAN
        // 2. Nilai dari 'user_role' adalah string 'admin'.
        // Jika salah satu kondisi tidak terpenuhi, akan mengembalikan false.
    }
}