<?php
// models/Booking.php

class Booking {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    // ===================================================================================
    // MANAJEMEN DATA BOOKING (CRUD - CREATE, UPDATE)
    // ===================================================================================

    /**
     * Membuat data booking baru di dalam database.
     * Fungsi ini menggunakan transaksi untuk memastikan integritas data antara tabel 'bookings' dan 'rooms'.
     */
    public function createBooking($data) {
        // Memulai transaksi untuk menjaga konsistensi data
        $this->db->beginTransaction();
        
        try {
            // Query untuk memasukkan data booking baru
            $this->db->query("
                INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, 
                                    total_price, adults, children, status, identity_file)
                VALUES (:user_id, :room_id, :check_in_date, :check_out_date, 
                        :total_price, :adults, :children, :status, :identity_file)
            ");
            
            // Bind semua nilai dari data yang diterima
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':room_id', $data['room_id']);
            $this->db->bind(':check_in_date', $data['check_in_date']);
            $this->db->bind(':check_out_date', $data['check_out_date']);
            $this->db->bind(':total_price', $data['total_price']);
            $this->db->bind(':adults', $data['adults']);
            $this->db->bind(':children', $data['children']);
            $this->db->bind(':status', $data['status']); // contoh: 'pending'
            $this->db->bind(':identity_file', $data['identity_file']);
            
            $this->db->execute();
            
            $bookingId = $this->db->lastInsertId();
            
            // Jika status booking adalah 'confirmed', update juga status kamar menjadi 'occupied'
            if($data['status'] == 'confirmed') {
                $this->db->query("
                    UPDATE rooms
                    SET status = 'occupied'
                    WHERE room_id = :room_id
                ");
                $this->db->bind(':room_id', $data['room_id']);
                $this->db->execute();
            }
            
            // Jika semua query berhasil, konfirmasi transaksi
            $this->db->endTransaction();
            
            return $bookingId;
            
        } catch(Exception $e) {
            // Jika terjadi error, batalkan semua perubahan dalam transaksi
            $this->db->cancelTransaction();
            error_log("Booking creation failed: " . $e->getMessage()); // Catat error
            return false;
        }
    }
    /*
        Penjelasan Query:
        1. INSERT INTO bookings ...
           - Query ini bertanggung jawab untuk memasukkan satu baris data baru ke dalam tabel `bookings`.
           - Menggunakan named parameters (contoh: :user_id) untuk mencegah SQL Injection.

        2. UPDATE rooms SET status = 'occupied' WHERE room_id = :room_id
           - Query ini dijalankan secara kondisional. Tujuannya adalah mengubah status kamar menjadi 'occupied' (terisi).
           - Ini penting untuk memastikan kamar yang sudah dibooking dan dikonfirmasi tidak dapat dibooking lagi oleh orang lain.

        Transaksi (beginTransaction, endTransaction, cancelTransaction):
        - Digunakan untuk mengelompokkan kedua query (INSERT dan UPDATE) menjadi satu unit kerja.
        - Jika salah satu query gagal, semua perubahan yang sudah terjadi akan dibatalkan (`cancelTransaction`), sehingga data tetap konsisten.
        - Jika semua berhasil, perubahan akan disimpan secara permanen (`endTransaction`).
    */


    /**
     * Memperbarui status sebuah booking (misal: 'confirmed', 'cancelled', 'completed').
     * Fungsi ini juga mengelola status kamar terkait secara otomatis dalam satu transaksi.
     */
    public function updateBookingStatus($bookingId, $status) {
        $this->db->beginTransaction();
        
        try {
            // Langkah 1: Ambil data booking untuk mendapatkan room_id
            $this->db->query("SELECT room_id FROM bookings WHERE booking_id = :booking_id");
            $this->db->bind(':booking_id', $bookingId);
            $booking = $this->db->single();

            if (!$booking) {
                throw new Exception("Booking not found.");
            }
            
            // Langkah 2: Update status di tabel bookings
            $this->db->query("
                UPDATE bookings
                SET status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE booking_id = :booking_id
            ");
            $this->db->bind(':status', $status);
            $this->db->bind(':booking_id', $bookingId);
            $this->db->execute();
            
            // Langkah 3: Update status di tabel rooms berdasarkan status booking yang baru
            if($status == 'confirmed') {
                $newRoomStatus = 'occupied';
            } 
            else if($status == 'cancelled' || $status == 'completed') {
                $newRoomStatus = 'available';
            }

            // Jika ada perubahan status kamar yang perlu dilakukan
            if (isset($newRoomStatus)) {
                $this->db->query("
                    UPDATE rooms
                    SET status = :status
                    WHERE room_id = :room_id
                ");
                $this->db->bind(':status', $newRoomStatus);
                $this->db->bind(':room_id', $booking->room_id);
                $this->db->execute();
            }
            
            $this->db->endTransaction();
            return true;
            
        } catch(Exception $e) {
            $this->db->cancelTransaction();
            error_log("Update booking status failed: " . $e->getMessage()); // Catat error
            return false;
        }
    }
    /*
        Penjelasan Query:
        1. SELECT room_id FROM bookings ...
           - Query ini mengambil `room_id` dari booking yang akan diupdate. ID kamar ini dibutuhkan untuk query selanjutnya.

        2. UPDATE bookings SET status = :status ...
           - Query utama yang mengubah status booking (misal dari 'pending' ke 'confirmed').
           - `updated_at = CURRENT_TIMESTAMP` secara otomatis mencatat waktu perubahan terjadi.

        3. UPDATE rooms SET status = :status ...
           - Query ini menyesuaikan status kamar berdasarkan status booking yang baru.
           - Jika booking 'confirmed', kamar menjadi 'occupied'.
           - Jika booking 'cancelled' atau 'completed', kamar menjadi 'available' lagi.
           - Ini menjaga sinkronisasi data antara booking dan ketersediaan kamar.
    */


    // ===================================================================================
    // PENGAMBILAN DATA BOOKING (READ)
    // ===================================================================================

    /**
     * Mengambil detail lengkap dari satu booking berdasarkan ID-nya.
     * Menggabungkan data dari tabel bookings, rooms, dan users.
     */
    public function getBookingById($bookingId) {
        $this->db->query("
            SELECT b.*, 
                   r.room_number, r.price_per_night, r.image_url,
                   u.full_name AS user_name, u.email AS user_email, u.phone AS user_phone
            FROM bookings AS b
            JOIN rooms AS r ON b.room_id = r.room_id
            JOIN users AS u ON b.user_id = u.user_id
            WHERE b.booking_id = :booking_id
        ");
        
        $this->db->bind(':booking_id', $bookingId);
        return $this->db->single();
    }
    /*
        Penjelasan Query:
        - SELECT b.*, r. ..., u. ...: Mengambil semua kolom dari tabel `bookings` (alias `b`), beberapa kolom dari `rooms` (alias `r`), dan beberapa kolom dari `users` (alias `u`).
        - JOIN rooms ...: Menghubungkan booking dengan data kamar berdasarkan `room_id`.
        - JOIN users ...: Menghubungkan booking dengan data pengguna yang memesan berdasarkan `user_id`.
        - WHERE b.booking_id = :booking_id: Filter untuk memastikan hanya data dari booking dengan ID yang spesifik yang diambil.
    */


    /**
     * Mengambil seluruh riwayat booking dari seorang pengguna (untuk halaman profil pengguna).
     * Termasuk informasi kamar dan kategori kamar, diurutkan dari yang terbaru.
     */
    public function getUserBookings($userId) {
        $this->db->query("
            SELECT b.*, r.room_number, r.price_per_night, r.image_url, c.name as category_name
            FROM bookings AS b
            JOIN rooms AS r ON b.room_id = r.room_id
            JOIN room_categories AS c ON r.category_id = c.category_id
            WHERE b.user_id = :user_id
            ORDER BY b.created_at DESC
        ");
        
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        - JOIN room_categories ...: Selain join ke tabel `rooms`, query ini juga join ke `room_categories` untuk mendapatkan nama kategori kamar.
        - WHERE b.user_id = :user_id: Filter untuk mengambil semua booking yang dimiliki oleh satu pengguna.
        - ORDER BY b.created_at DESC: Mengurutkan hasil berdasarkan tanggal pembuatan booking, dari yang paling baru ke yang paling lama.
    */


    /**
     * Mengambil semua data booking milik user ID tertentu (versi sederhana untuk admin).
     */
    public function getBookingsByUserId($userId) {
        $this->db->query("SELECT * FROM bookings WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        - SELECT * FROM bookings: Mengambil semua kolom langsung dari tabel `bookings` tanpa join.
        - WHERE user_id = :user_id: Filter untuk mendapatkan semua booking dari user tertentu.
        - Fungsi ini lebih ringan daripada `getUserBookings` karena tidak melakukan JOIN. Cocok digunakan jika hanya data dasar booking yang diperlukan.
    */

    /**
     * Mengambil data booking untuk halaman admin dengan filter dan paginasi.
     */
    public function getAllBookingsPaginatedFiltered($limit, $offset, $filters = []) {
        $query = "SELECT b.*, r.room_number, u.full_name AS user_name 
                  FROM bookings AS b 
                  JOIN rooms AS r ON b.room_id = r.room_id 
                  JOIN users AS u ON b.user_id = u.user_id";
        
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "b.status = :status_filter";
            $params[':status_filter'] = $filters['status'];
        }
        if (!empty($filters['date_start'])) {
            $conditions[] = "b.check_in_date >= :date_start_filter";
            $params[':date_start_filter'] = $filters['date_start'];
        }
        if (!empty($filters['date_end'])) {
            $conditions[] = "b.check_out_date <= :date_end_filter";
            $params[':date_end_filter'] = $filters['date_end'];
        }

        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset";

        $this->db->query($query);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        - WHERE ...: Bagian ini dibuat secara dinamis. Kondisi filter (`status`, `date_start`, `date_end`) hanya akan ditambahkan ke query jika nilainya disediakan.
        - `implode(" AND ", $conditions)`: Menggabungkan semua kondisi filter dengan `AND`.
        - ORDER BY b.created_at DESC: Mengurutkan hasil agar booking terbaru tampil di atas.
        - LIMIT :limit OFFSET :offset: Ini adalah kunci dari paginasi. `LIMIT` membatasi jumlah baris yang diambil, dan `OFFSET` menentukan dari baris ke berapa data mulai diambil.
    */


    /**
     * Mengambil booking yang masih aktif ('pending' atau 'confirmed') untuk sebuah kamar.
     * Berguna untuk validasi ketersediaan kamar.
     */
    public function getActiveBookingsByRoomId($roomId) {
        $this->db->query("
            SELECT * FROM bookings
            WHERE room_id = :room_id
            AND status IN ('pending', 'confirmed')
        ");
        $this->db->bind(':room_id', $roomId);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        - WHERE room_id = :room_id: Memfilter booking hanya untuk kamar yang spesifik.
        - AND status IN ('pending', 'confirmed'): Memfilter lebih lanjut untuk hanya menyertakan booking yang statusnya masih relevan dengan ketersediaan kamar (belum 'completed' atau 'cancelled').
    */


    // ===================================================================================
    // AGREGASI & STATISTIK (COUNT, SUM, AVG)
    // ===================================================================================

    /**
     * Menghitung total booking yang cocok dengan filter yang diberikan.
     * Digunakan untuk menentukan jumlah halaman pada paginasi.
     */
    public function getTotalBookingsFiltered($filters = []) {
        $query = "SELECT COUNT(b.booking_id) AS total FROM bookings AS b";
        
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "b.status = :status_filter";
            $params[':status_filter'] = $filters['status'];
        }
        if (!empty($filters['date_start'])) {
            $conditions[] = "b.check_in_date >= :date_start_filter";
            $params[':date_start_filter'] = $filters['date_start'];
        }
        if (!empty($filters['date_end'])) {
            $conditions[] = "b.check_out_date <= :date_end_filter";
            $params[':date_end_filter'] = $filters['date_end'];
        }

        if ($conditions) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $this->db->query($query);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }
    /*
        Penjelasan Query:
        - SELECT COUNT(b.booking_id) AS total: Alih-alih mengambil semua data, query ini hanya menghitung jumlah baris (`booking_id`) yang cocok dan mengembalikannya sebagai satu nilai `total`.
        - WHERE ...: Logika filternya identik dengan `getAllBookingsPaginatedFiltered` untuk memastikan hasil hitungan konsisten dengan data yang ditampilkan.
        - Ini jauh lebih efisien daripada mengambil semua data lalu menghitungnya di PHP.
    */


    /**
     * Mengambil statistik ringkas dari seluruh data booking untuk dashboard admin.
     */
    public function getBookingStats() {
        $this->db->query("
            SELECT 
                COUNT(*) AS total_bookings,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_bookings,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_bookings,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_bookings,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_bookings,
                SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) AS total_revenue,
                AVG(DATEDIFF(check_out_date, check_in_date)) AS avg_stay_duration
            FROM bookings
        ");
        return $this->db->single();
    }
    /*
        Penjelasan Query:
        - COUNT(*): Menghitung total semua booking yang pernah ada.
        - SUM(CASE WHEN ...): Teknik ini digunakan untuk "conditional counting". Ia akan menjumlahkan `1` jika kondisi `status` terpenuhi, dan `0` jika tidak. Ini cara efisien untuk mendapatkan beberapa hitungan status dalam satu query.
        - SUM(CASE WHEN status = 'confirmed' ...): Menghitung total pendapatan hanya dari booking yang sudah dikonfirmasi.
        - AVG(DATEDIFF(check_out_date, check_in_date)): Menghitung rata-rata lama menginap. `DATEDIFF` mengembalikan selisih hari antara dua tanggal.
    */
    
    /**
     * Menghitung total seluruh booking.
     */
    public function getTotalBookings() {
        $this->db->query("SELECT COUNT(*) as total FROM bookings");
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }
    /*
        Penjelasan Query:
        - SELECT COUNT(*) as total FROM bookings: Query paling dasar untuk menghitung semua baris dalam tabel `bookings`.
    */

    /**
     * Menghitung jumlah booking yang statusnya 'pending'.
     */
    public function getPendingBookings() {
        $this->db->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }
    /*
        Penjelasan Query:
        - WHERE status = 'pending': Menambahkan filter untuk hanya menghitung booking yang menunggu konfirmasi.
    */

    /**
     * Menghitung total pendapatan dari semua booking yang statusnya 'confirmed'.
     */
    public function getTotalRevenue() {
        $this->db->query("
            SELECT SUM(total_price) AS total_revenue 
            FROM bookings 
            WHERE status = 'confirmed'
        ");
        $result = $this->db->single();
        return $result ? (float)$result->total_revenue : 0;
    }
    /*
        Penjelasan Query:
        - SELECT SUM(total_price): Menjumlahkan nilai dari kolom `total_price`.
        - WHERE status = 'confirmed': Memastikan hanya pendapatan dari booking yang valid (telah dikonfirmasi) yang dihitung.
    */
    
    /**
     * Mengambil beberapa booking terbaru (default 5) untuk ditampilkan di dashboard admin.
     */
    public function getRecentBookings($limit = 5) {
        $this->db->query("
            SELECT b.booking_id, b.status, b.created_at, u.full_name, r.room_number,
                   b.check_in_date, b.check_out_date, b.total_price  -- Tambahkan kolom ini
            FROM bookings AS b 
            JOIN users AS u ON b.user_id = u.user_id 
            JOIN rooms AS r ON b.room_id = r.room_id 
            ORDER BY b.created_at DESC 
            LIMIT :limit
        ");
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        - ORDER BY b.created_at DESC: Mengurutkan booking dari yang paling baru.
        - LIMIT :limit: Membatasi jumlah hasil yang dikembalikan, sehingga hanya mengambil booking terbaru sebanyak nilai `limit`.
    */


    // ===================================================================================
    // PELAPORAN & ANALITIK (REPORTING & ANALYTICS)
    // ===================================================================================

    /**
     * Menghitung total pendapatan per bulan untuk tahun berjalan (untuk grafik).
     */
    public function getMonthlyRevenue() {
        $this->db->query("
            SELECT MONTH(check_in_date) AS month, SUM(total_price) AS revenue 
            FROM bookings 
            WHERE status = 'confirmed' AND YEAR(check_in_date) = YEAR(CURDATE()) 
            GROUP BY MONTH(check_in_date)
            ORDER BY month ASC
        ");
        $results = $this->db->resultSet();
        $revenue = array_fill(1, 12, 0); // Buat array 12 bulan dengan nilai awal 0
        foreach ($results as $row) {
            $revenue[(int)$row->month] = (float)$row->revenue;
        }
        return $revenue;
    }
    /*
        Penjelasan Query:
        - SELECT MONTH(...) AS month, SUM(...) AS revenue: Mengambil nomor bulan dan menjumlahkan pendapatan untuk bulan tersebut.
        - WHERE status = 'confirmed' AND YEAR(...) = YEAR(CURDATE()): Memfilter data hanya untuk booking terkonfirmasi pada tahun saat ini. `CURDATE()` adalah fungsi MySQL untuk tanggal hari ini.
        - GROUP BY MONTH(check_in_date): Mengelompokkan semua baris berdasarkan bulan, sehingga `SUM` dapat menghitung total untuk setiap bulan secara terpisah.
    */

    /**
     * Mengambil data tren booking harian untuk periode tertentu (default 30 hari) untuk grafik.
     */
    public function getBookingTrend($days = 30) {
        $this->db->query("
            SELECT DATE(created_at) AS date, COUNT(*) AS count
            FROM bookings
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $this->db->bind(':days', $days);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        - SELECT DATE(created_at) as date, COUNT(*) as count: Mengambil tanggal (tanpa waktu) dan menghitung jumlah booking pada tanggal tersebut.
        - WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY): Memfilter data untuk rentang waktu tertentu, misalnya 30 hari terakhir dari hari ini. `DATE_SUB` adalah fungsi untuk mengurangi interval waktu dari tanggal.
        - GROUP BY DATE(created_at): Mengelompokkan data berdasarkan hari, sehingga `COUNT` bisa menghitung jumlah booking per hari.
    */

    /**
     * Menghasilkan data laporan booking berdasarkan periode yang dipilih.
     * Catatan: Query untuk 'all' bisa sangat berat jika data banyak.
     */
    public function getBookingReport($period = 'daily') {
        $query = "";
        switch($period) {
            case 'daily':
                $query = "
                    SELECT DATE(created_at) AS date, 
                           COUNT(*) AS total_bookings,
                           SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) AS total_revenue,
                           SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_bookings,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_bookings
                    FROM bookings
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date DESC
                ";
                break;
            case 'monthly':
                $query = "
                    SELECT YEAR(created_at) AS year, MONTH(created_at) AS month,
                           COUNT(*) AS total_bookings,
                           SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) AS total_revenue,
                           SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_bookings,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_bookings
                    FROM bookings
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY YEAR(created_at), MONTH(created_at)
                    ORDER BY year DESC, month DESC
                ";
                break;
            case 'yearly':
                $query = "
                    SELECT YEAR(created_at) AS year,
                           COUNT(*) AS total_bookings,
                           SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) AS total_revenue,
                           SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_bookings,
                           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_bookings
                    FROM bookings
                    GROUP BY YEAR(created_at)
                    ORDER BY year DESC
                ";
                break;
            case 'all':
                // PERINGATAN: Query ini bisa sangat lambat pada tabel besar.
                $query = "
                    SELECT b.*, r.room_number, u.full_name AS user_name, p.status AS payment_status, p.payment_method
                    FROM bookings AS b
                    LEFT JOIN rooms AS r ON b.room_id = r.room_id
                    LEFT JOIN users AS u ON b.user_id = u.user_id
                    LEFT JOIN payments AS p ON b.booking_id = p.booking_id
                    ORDER BY b.created_at DESC
                ";
                break;
        }
        
        $this->db->query($query);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query (General):
        - Fungsi ini menggunakan `switch` untuk memilih query yang tepat berdasarkan periode.
        - Laporan Harian, Bulanan, Tahunan: Menggunakan fungsi tanggal (`DATE`, `MONTH`, `YEAR`) dan `GROUP BY` untuk mengagregasi data. `SUM(CASE WHEN ...)` digunakan untuk menghitung sub-total (pendapatan, jumlah konfirmasi, dll) dalam setiap grup.
        - Laporan 'all': Adalah dump data besar yang menggabungkan informasi dari `bookings`, `rooms`, `users`, dan `payments`. `LEFT JOIN` digunakan agar booking tetap muncul meskipun data pembayaran (payments) tidak ada.
    */

    // ===================================================================================
    // FUNGSI UTILITAS (HELPERS)
    // ===================================================================================

    /**
     * Menghitung jumlah malam antara tanggal check-in dan check-out.
     */
    public function calculateNights($checkIn, $checkOut) {
        try {
            $checkInDate = new DateTime($checkIn);
            $checkOutDate = new DateTime($checkOut);
            $interval = $checkInDate->diff($checkOutDate);
            return $interval->days;
        } catch (Exception $e) {
            return 0; // Mengembalikan 0 jika format tanggal salah
        }
    }
    
    /**
     * Menghitung total harga berdasarkan harga per malam dan jumlah malam.
     */
    public function calculateTotalPrice($roomPrice, $nights) {
        return (float)$roomPrice * (int)$nights;
    }
    
}