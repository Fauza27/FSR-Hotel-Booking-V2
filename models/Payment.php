<?php

class Payment
{
    private $db;

    // =================================================================
    // KONSTRUKTOR
    // =================================================================

    /**
     * Konstruktor untuk inisialisasi koneksi database.
     */
    public function __construct()
    {
        // Membuat instance baru dari kelas Database untuk koneksi.
        $this->db = new Database();
    }

    // =================================================================
    // OPERASI CRUD (CREATE, READ, UPDATE)
    // =================================================================

    /**
     * Membuat data pembayaran baru di database.
     * @param array $data Data pembayaran yang akan disimpan (booking_id, amount, payment_method, dll).
     * @return int|false ID dari pembayaran yang baru dibuat, atau false jika gagal.
     */
    public function createPayment($data)
    {
        $this->db->query("
            INSERT INTO payments (booking_id, amount, payment_method, payment_status, transaction_id, payment_date)
            VALUES (:booking_id, :amount, :payment_method, :payment_status, :transaction_id, NOW())
        ");

        // Mengikat nilai dari array $data ke placeholder dalam query.
        $this->db->bind(':booking_id', $data['booking_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':payment_method', $data['payment_method']);
        $this->db->bind(':payment_status', $data['payment_status']);
        $this->db->bind(':transaction_id', $data['transaction_id']);

        // Menjalankan query dan mengembalikan ID terakhir jika berhasil.
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    /*
        Penjelasan Query:
        INSERT INTO payments (...) VALUES (...) → Perintah SQL untuk menambahkan baris baru ke tabel 'payments'.
        NOW() → Fungsi SQL untuk menyisipkan waktu dan tanggal saat ini secara otomatis ke kolom 'payment_date'.
        :booking_id, :amount, dll. → Placeholder untuk prepared statement yang akan diisi dengan data aktual menggunakan metode bind().
                                      Ini adalah praktik keamanan penting untuk mencegah SQL Injection.
    */


    /**
     * Mengambil detail pembayaran berdasarkan ID uniknya.
     * @param int $paymentId ID dari pembayaran yang dicari.
     * @return object|false Objek data pembayaran, atau false jika tidak ditemukan.
     */
    public function getPaymentById($paymentId)
    {
        $this->db->query("
            SELECT * FROM payments
            WHERE payment_id = :payment_id
        ");

        $this->db->bind(':payment_id', $paymentId);

        return $this->db->single();
    }
    /*
        Penjelasan Query:
        SELECT * FROM payments → Mengambil semua kolom dari tabel 'payments'.
        WHERE payment_id = :payment_id → Klausa filter untuk mencari baris yang cocok dengan 'payment_id' yang diberikan.
    */


    /**
     * Mengambil semua data pembayaran yang terkait dengan satu ID booking.
     * @param int $bookingId ID dari booking.
     * @return array Array objek pembayaran.
     */
    public function getPaymentsByBookingId($bookingId)
    {
        $this->db->query("
            SELECT * FROM payments
            WHERE booking_id = :booking_id
            ORDER BY created_at DESC
        ");

        $this->db->bind(':booking_id', $bookingId);

        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        SELECT * FROM payments → Mengambil semua kolom dari tabel 'payments'.
        WHERE booking_id = :booking_id → Filter untuk mendapatkan semua pembayaran yang terkait dengan booking tertentu.
        ORDER BY created_at DESC → Mengurutkan hasil dari yang paling baru, berguna untuk melihat riwayat pembayaran.
    */


    /**
     * Memperbarui status pembayaran (misal: dari 'pending' ke 'completed').
     * @param int $paymentId ID pembayaran yang akan diupdate.
     * @param string $status Status baru ('pending', 'completed', 'failed', 'refunded').
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updatePaymentStatus($paymentId, $status)
    {
        $this->db->query("
            UPDATE payments
            SET payment_status = :payment_status,
                updated_at = CURRENT_TIMESTAMP
            WHERE payment_id = :payment_id
        ");

        $this->db->bind(':payment_status', $status);
        $this->db->bind(':payment_id', $paymentId);

        return $this->db->execute();
    }
    /*
        Penjelasan Query:
        UPDATE payments SET ... → Perintah untuk memperbarui data di tabel 'payments'.
        payment_status = :payment_status → Mengatur kolom 'payment_status' dengan nilai baru.
        updated_at = CURRENT_TIMESTAMP → Secara otomatis memperbarui kolom 'updated_at' dengan waktu saat ini.
        WHERE payment_id = :payment_id → Memastikan hanya baris dengan ID yang cocok yang diperbarui.
    */


    /**
     * Mengambil data pembayaran dengan filter dan paginasi untuk halaman daftar admin.
     * @param int $limit Jumlah data per halaman.
     * @param int $offset Posisi awal pengambilan data.
     * @param array $filters Filter yang diterapkan (misal: 'method', 'status').
     * @return array Array objek pembayaran.
     */
    public function getPaymentsPaginatedFiltered($limit, $offset, $filters = [])
    {
        $query = "SELECT p.* FROM payments p WHERE 1=1";
        $params = [];

        if (!empty($filters['method'])) {
            $query .= " AND p.payment_method LIKE :method_filter";
            $params[':method_filter'] = "%" . $filters['method'] . "%";
        }
        if (!empty($filters['status'])) {
            $query .= " AND p.payment_status = :status_filter";
            $params[':status_filter'] = $filters['status'];
        }

        $query .= " ORDER BY p.payment_date DESC LIMIT :limit OFFSET :offset";

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
        SELECT p.* FROM payments p → Mengambil semua kolom dari tabel payments dengan alias 'p'.
        WHERE 1=1 → Kondisi awal yang selalu benar, memudahkan penambahan kondisi filter 'AND' secara dinamis.
        AND p.payment_method LIKE :method_filter → Menambahkan filter untuk metode pembayaran jika ada.
        AND p.payment_status = :status_filter → Menambahkan filter untuk status pembayaran jika ada.
        ORDER BY p.payment_date DESC → Mengurutkan pembayaran dari yang terbaru.
        LIMIT :limit OFFSET :offset → Klausa untuk paginasi, membatasi jumlah hasil dan menentukan titik awal.
    */


    /**
     * Menghitung total jumlah pembayaran berdasarkan filter untuk keperluan paginasi.
     * @param array $filters Filter yang diterapkan (misal: 'method', 'status').
     * @return int Jumlah total pembayaran.
     */
    public function getTotalPaymentsFiltered($filters = [])
    {
        $query = "SELECT COUNT(*) as total FROM payments p WHERE 1=1";
        $params = [];

        if (!empty($filters['method'])) {
            $query .= " AND p.payment_method LIKE :method_filter";
            $params[':method_filter'] = "%" . $filters['method'] . "%";
        }
        if (!empty($filters['status'])) {
            $query .= " AND p.payment_status = :status_filter";
            $params[':status_filter'] = $filters['status'];
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
        SELECT COUNT(*) as total FROM payments p → Menghitung jumlah total baris di tabel payments dan menamakannya 'total'.
        WHERE 1=1 AND ... → Kondisi filter yang sama dengan fungsi getPaymentsPaginatedFiltered untuk memastikan
                            jumlah total sesuai dengan data yang ditampilkan.
    */


    // =================================================================
    // FUNGSI LAPORAN & STATISTIK
    // =================================================================

    /**
     * Mengambil laporan pendapatan yang komprehensif berdasarkan periode atau rentang tanggal.
     * @param string $period Periode default ('daily', 'monthly', 'yearly').
     * @param string|null $startDate Tanggal mulai kustom.
     * @param string|null $endDate Tanggal akhir kustom.
     * @return object Objek yang berisi data untuk grafik, rincian, dan ringkasan pendapatan.
     */
    public function getRevenueReport($period = 'monthly', $startDate = null, $endDate = null)
    {
        $response = (object) [
            'chartData' => [],
            'breakdownData' => [],
            'currentPeriodRevenue' => 0,
            'previousPeriodRevenue' => 0,
            'periodLabel' => '',
            'actualStartDate' => null,
            'actualEndDate' => null,
        ];

        // Logika penentuan tanggal dan filter tetap sama seperti kode asli...
        // ... (Kode untuk menentukan tanggal dan parameter tidak diubah)

        // Di sini kita hanya akan fokus pada penjelasan query-query utamanya
        
        // Contoh penjelasan untuk query pertama (Chart Data):
        /*
            Penjelasan Query (Chart Data):
            SELECT {$selectDate}, SUM(p.amount) as revenue → Memilih label tanggal (harian, bulanan, atau tahunan) dan menjumlahkan total pendapatan ('revenue').
            FROM payments p WHERE {$whereSql} → Mengambil data dari tabel payments dengan filter status 'completed' dan rentang tanggal yang ditentukan.
            GROUP BY {$groupBy} → Mengelompokkan data berdasarkan label tanggal untuk agregasi.
            ORDER BY {$orderBy} → Mengurutkan data berdasarkan waktu untuk memastikan grafik ditampilkan dengan benar.
        */

        // Contoh penjelasan untuk query kedua (Breakdown Data):
        /*
            Penjelasan Query (Breakdown Data):
            SELECT rc.name as category_name, SUM(p.amount) as revenue → Memilih nama kategori kamar dan total pendapatan per kategori.
            FROM payments p JOIN bookings ... JOIN rooms ... JOIN room_categories ... → Melakukan JOIN antar tabel untuk menghubungkan pembayaran
                                                                                      dengan kategori kamar.
            WHERE {$whereSql} → Menerapkan filter yang sama untuk memastikan konsistensi data.
            GROUP BY rc.category_id, rc.name → Mengelompokkan pendapatan berdasarkan kategori kamar.
            ORDER BY revenue DESC → Mengurutkan hasil dari pendapatan tertinggi ke terendah.
        */

        // (Kode lengkap fungsi ini tidak disertakan kembali untuk keringkasan, karena strukturnya sudah kompleks dan benar)
        // Namun, jika Anda ingin, saya bisa menulis ulang seluruh fungsi ini dengan komentar di dalamnya.
        // Untuk saat ini, saya akan menggunakan implementasi yang ada di file Anda karena sudah cukup baik.
        // --- START OF ORIGINAL CODE FOR getRevenueReport ---
        $whereClauses = ["p.payment_status = 'completed'"];
        $params = [];
        if ($startDate && $endDate) {
            $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate;
            $response->actualStartDate = $startDate;
            $response->actualEndDate = $endDate;
        } else {
            switch ($period) {
                case 'daily':
                    $defaultStartDate = date('Y-m-d', strtotime('-29 days'));
                    $defaultEndDate = date('Y-m-d');
                    $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
                    $params[':startDate'] = $defaultStartDate;
                    $params[':endDate'] = $defaultEndDate;
                    $response->actualStartDate = $defaultStartDate;
                    $response->actualEndDate = $defaultEndDate;
                    break;
                case 'yearly':
                    $currentYear = date('Y');
                    $defaultStartDate = $currentYear . '-01-01';
                    $defaultEndDate = $currentYear . '-12-31';
                    $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
                    $params[':startDate'] = $defaultStartDate;
                    $params[':endDate'] = $defaultEndDate;
                    $response->actualStartDate = $defaultStartDate;
                    $response->actualEndDate = $defaultEndDate;
                    break;
                case 'monthly':
                default:
                    $defaultStartDate = date('Y-m-01', strtotime('-11 months'));
                    $defaultEndDate = date('Y-m-t');
                    $whereClauses[] = "p.payment_date >= :startDate AND p.payment_date < DATE_ADD(:endDate, INTERVAL 1 DAY)";
                    $params[':startDate'] = $defaultStartDate;
                    $params[':endDate'] = $defaultEndDate;
                    $response->actualStartDate = $defaultStartDate;
                    $response->actualEndDate = $defaultEndDate;
                    break;
            }
        }
        $whereSql = implode(" AND ", $whereClauses);
        $groupBy = ""; $selectDate = ""; $orderBy = "";
        $dateDiff = null;
        if($response->actualStartDate && $response->actualEndDate) {
            $startDt = new DateTime($response->actualStartDate);
            $endDt = new DateTime($response->actualEndDate);
            $dateDiff = $startDt->diff($endDt)->days;
        }
        if ($period === 'yearly' || ($dateDiff !== null && $dateDiff > 365 * 1.5)) {
            $selectDate = "YEAR(p.payment_date) as label";
            $groupBy = "YEAR(p.payment_date)";
            $orderBy = "label ASC";
            $response->periodLabel = 'Tahunan';
        } elseif ($period === 'monthly' || ($dateDiff !== null && $dateDiff > 60)) {
            $selectDate = "DATE_FORMAT(p.payment_date, '%Y-%m') as label";
            $groupBy = "label";
            $orderBy = "label ASC";
            $response->periodLabel = 'Bulanan';
        } else {
            $selectDate = "DATE(p.payment_date) as label";
            $groupBy = "label";
            $orderBy = "label ASC";
            $response->periodLabel = 'Harian';
        }
        $chartQuery = "SELECT {$selectDate}, SUM(p.amount) as revenue FROM payments p WHERE {$whereSql} GROUP BY {$groupBy} ORDER BY {$orderBy}";
        $this->db->query($chartQuery);
        foreach($params as $key => $value) { $this->db->bind($key, $value); }
        $response->chartData = $this->db->resultSet();
        $breakdownQuery = "SELECT rc.name as category_name, SUM(p.amount) as revenue FROM payments p JOIN bookings b ON p.booking_id = b.booking_id JOIN rooms r ON b.room_id = r.room_id JOIN room_categories rc ON r.category_id = rc.category_id WHERE {$whereSql} GROUP BY rc.category_id, rc.name ORDER BY revenue DESC";
        $this->db->query($breakdownQuery);
        foreach($params as $key => $value) { $this->db->bind($key, $value); }
        $response->breakdownData = $this->db->resultSet();
        $currentPeriodRevenueQuery = "SELECT SUM(p.amount) as total_revenue FROM payments p WHERE {$whereSql}";
        $this->db->query($currentPeriodRevenueQuery);
        foreach($params as $key => $value) { $this->db->bind($key, $value); }
        $currentResult = $this->db->single();
        $response->currentPeriodRevenue = $currentResult ? (float)$currentResult->total_revenue : 0;
        if ($response->actualStartDate && $response->actualEndDate) {
            try {
                $startDt = new DateTimeImmutable($response->actualStartDate);
                $endDt = new DateTimeImmutable($response->actualEndDate);
                $intervalSpec = 'P' . ($startDt->diff($endDt)->days + 1) . 'D';
                $interval = new DateInterval($intervalSpec);
                $prevEndDate = $startDt->sub(new DateInterval('P1D'));
                $prevStartDate = $prevEndDate->sub($interval)->add(new DateInterval('P1D'));
                $prevWhereClauses = ["p.payment_status = 'completed'"];
                $prevParams = [];
                $prevWhereClauses[] = "p.payment_date >= :prevStartDate AND p.payment_date < DATE_ADD(:prevEndDate, INTERVAL 1 DAY)";
                $prevParams[':prevStartDate'] = $prevStartDate->format('Y-m-d');
                $prevParams[':prevEndDate'] = $prevEndDate->format('Y-m-d');
                $prevWhereSql = implode(" AND ", $prevWhereClauses);
                $previousPeriodRevenueQuery = "SELECT SUM(p.amount) as total_revenue FROM payments p WHERE {$prevWhereSql}";
                $this->db->query($previousPeriodRevenueQuery);
                foreach($prevParams as $key => $value) { $this->db->bind($key, $value); }
                $previousResult = $this->db->single();
                $response->previousPeriodRevenue = $previousResult ? (float)$previousResult->total_revenue : 0;
            } catch (Exception $e) {
                $response->previousPeriodRevenue = 0;
                error_log("Error calculating previous period revenue: " . $e->getMessage());
            }
        }
        return $response;
        // --- END OF ORIGINAL CODE FOR getRevenueReport ---
    }


    /**
     * Mengambil statistik ringkas pembayaran untuk ditampilkan di dashboard.
     * @return object Objek berisi total pembayaran, status, jumlah, dan rata-rata.
     */
    public function getPaymentStats()
    {
        $this->db->query("
            SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
                SUM(CASE WHEN payment_status = 'completed' THEN 1 ELSE 0 END) as completed_payments,
                SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_payments,
                SUM(CASE WHEN payment_status = 'refunded' THEN 1 ELSE 0 END) as refunded_payments,
                SUM(amount) as total_amount,
                SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as completed_amount,
                AVG(CASE WHEN payment_status = 'completed' THEN amount ELSE NULL END) as avg_payment_amount
            FROM payments
        ");
        return $this->db->single();
    }
    /*
        Penjelasan Query:
        COUNT(*) as total_payments → Menghitung semua transaksi pembayaran.
        SUM(CASE WHEN ... END) → Teknik untuk menghitung jumlah baris berdasarkan kondisi. Ini digunakan untuk
                                 menghitung pembayaran 'pending', 'completed', 'failed', dan 'refunded' dalam satu query.
        SUM(amount) as total_amount → Menjumlahkan nilai dari semua pembayaran, terlepas dari statusnya.
        SUM(CASE WHEN ... amount ELSE 0 END) → Menjumlahkan nilai dari pembayaran yang statusnya 'completed' saja.
        AVG(CASE WHEN ... amount ELSE NULL END) → Menghitung rata-rata nilai pembayaran yang 'completed'. Menggunakan NULL
                                                  agar pembayaran non-completed tidak memengaruhi hasil rata-rata.
    */


    /**
     * Mengambil data distribusi metode pembayaran untuk laporan atau grafik pie chart.
     * @return array Array objek yang berisi metode pembayaran, jumlah transaksi, total nominal, dan persentase.
     */
    public function getPaymentMethodDistribution()
    {
        $this->db->query("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                ROUND((COUNT(*) / (SELECT COUNT(*) FROM payments WHERE payment_status = 'completed')) * 100, 2) as percentage
            FROM payments
            WHERE payment_status = 'completed'
            GROUP BY payment_method
            ORDER BY count DESC
        ");
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        SELECT payment_method, COUNT(*), SUM(amount) → Memilih metode pembayaran, menghitung jumlah penggunaannya, dan total nominalnya.
        ROUND((...), 2) as percentage → Menghitung persentase penggunaan setiap metode terhadap total pembayaran yang 'completed'.
                                        Subquery `(SELECT COUNT(*) FROM ...)` digunakan untuk mendapatkan total pembagi.
        WHERE payment_status = 'completed' → Filter untuk hanya menganalisis pembayaran yang berhasil.
        GROUP BY payment_method → Mengelompokkan hasil berdasarkan metode pembayaran.
        ORDER BY count DESC → Mengurutkan dari metode pembayaran yang paling sering digunakan.
    */


    /**
     * Menghitung total pendapatan dari pembayaran yang statusnya 'completed'.
     * @return float Total pendapatan.
     */
    public function getTotalRevenue()
    {
        $this->db->query("
            SELECT SUM(amount) as total_revenue 
            FROM payments 
            WHERE payment_status = 'completed'
        ");
        $result = $this->db->single();
        return $result ? (float)$result->total_revenue : 0;
    }
    /*
        Penjelasan Query:
        SELECT SUM(amount) as total_revenue → Menjumlahkan kolom 'amount' dan memberinya alias 'total_revenue'.
        FROM payments → Dari tabel 'payments'.
        WHERE payment_status = 'completed' → Hanya untuk pembayaran yang sudah berhasil (completed).
    */


    /**
     * Mengambil data tren pendapatan harian untuk grafik selama N hari terakhir.
     * @param int $days Jumlah hari ke belakang yang akan dianalisis (default 30).
     * @return array Array objek berisi tanggal dan total pendapatan per tanggal.
     */
    public function getRevenueTrend($days = 30)
    {
        $this->db->query("
            SELECT 
                DATE(payment_date) as date, 
                SUM(amount) as revenue
            FROM payments
            WHERE payment_status = 'completed'
            AND payment_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(payment_date)
            ORDER BY date ASC
        ");
        $this->db->bind(':days', $days);
        return $this->db->resultSet();
    }
    /*
        Penjelasan Query:
        SELECT DATE(payment_date) as date, SUM(amount) as revenue → Memilih tanggal (tanpa waktu) dan total pendapatan per tanggal.
        WHERE payment_status = 'completed' → Hanya menghitung pendapatan dari pembayaran yang berhasil.
        AND payment_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY) → Filter untuk mengambil data dari N hari terakhir dari tanggal saat ini.
        GROUP BY DATE(payment_date) → Mengelompokkan data per hari untuk menjumlahkan pendapatan harian.
        ORDER BY date ASC → Mengurutkan hasil berdasarkan tanggal agar data grafik urut dari kiri ke kanan.
    */


    /**
     * Mengambil data pendapatan bulanan untuk grafik di dashboard selama N bulan terakhir.
     * @param int $numberOfMonths Jumlah bulan ke belakang (default 12).
     * @return array Array objek berisi bulan dan total pendapatan per bulan.
     */
    public function getMonthlyRevenueForDashboard($numberOfMonths = 12)
    {
        $this->db->query("
            SELECT
                DATE_FORMAT(payment_date, '%b %Y') AS month,
                SUM(amount) AS revenue
            FROM payments
            WHERE payment_status = 'completed'
              AND payment_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY month
            ORDER BY MIN(payment_date) ASC
        ");
        $this->db->bind(':months', (int)$numberOfMonths, PDO::PARAM_INT);
        return $this->db->resultSet() ?: [];
    }
    /*
        Penjelasan Query:
        SELECT DATE_FORMAT(payment_date, '%b %Y') AS month → Memformat tanggal menjadi nama bulan singkat dan tahun (misal: 'Jan 2023') sebagai label.
        WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH) → Filter untuk mengambil data N bulan terakhir.
        GROUP BY month → Mengelompokkan data berdasarkan label bulan dan tahun yang sudah diformat.
        ORDER BY MIN(payment_date) ASC → Mengurutkan berdasarkan tanggal asli paling awal di setiap grup, memastikan urutan bulan
                                         kronologis (misal: Des 2022, Jan 2023, Feb 2023).
    */
}