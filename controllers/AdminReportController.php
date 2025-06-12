<?php
class AdminReportController {
    private $bookingModel;
    private $roomModel;
    private $paymentModel;

    public function __construct() {
        $this->checkAdminAuth();
        require_once MODEL_PATH . 'Booking.php'; // Menggunakan konstanta path
        require_once MODEL_PATH . 'Room.php';
        require_once MODEL_PATH . 'Payment.php';
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->paymentModel = new Payment();
    }

    // Dashboard laporan utama
    public function index() {
        $roomStatsSummary = $this->roomModel->getRoomAvailabilityStats(); // metode yang lebih sesuai untuk summary
        $totalRooms = array_sum($roomStatsSummary);
        $occupiedRooms = ($roomStatsSummary['occupied'] ?? 0) + ($roomStatsSummary['maintenance'] ?? 0); // Asumsi maintenance juga tidak available
        
        $overallOccupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;


        $summary = [
            'totalBookings' => $this->bookingModel->getTotalBookings(),
            'totalRevenue' => $this->paymentModel->getTotalRevenue(),
            'roomOccupancyRate' => number_format($overallOccupancyRate, 2) . '%', // Contoh kalkulasi sederhana
            'roomAvailabilityStats' => $roomStatsSummary
        ];
        require VIEW_PATH . 'admin/reports/index.php'; // Menggunakan konstanta path
    }

    // Laporan booking (harian/bulanan/tahunan)
    public function bookings() {
        $period = $_GET['period'] ?? 'daily';
        // Anda mungkin juga ingin menambahkan filter tanggal start/end di sini
        $data = $this->bookingModel->getBookingReport($period);
        require VIEW_PATH . 'admin/reports/bookings.php';
    }

    // Laporan pendapatan dengan grafik
    public function revenue() {
        $period = $_GET['period'] ?? 'monthly'; // Digunakan sebagai default jika start/end tidak ada
        $startDate = !empty($_GET['start']) ? $_GET['start'] : null;
        $endDate = !empty($_GET['end']) ? $_GET['end'] : null;

        $data = $this->paymentModel->getRevenueReport($period, $startDate, $endDate);
        
        // Untuk debugging, Anda bisa uncomment baris ini:
        // header('Content-Type: application/json'); echo json_encode($data); exit;

        require VIEW_PATH . 'admin/reports/revenue.php';
    }

    // Laporan okupansi kamar
    public function rooms() {
        $data = [
            'average_occupancy_rate' => '0%', // Diubah nama untuk kejelasan
            'most_popular_room' => ['room_number' => '-', 'total_bookings' => 0],
            'best_performance' => ['room_number' => '-', 'total_revenue' => 0], // Diperbaiki key
            'rooms' => []
        ];

        // 1. Ambil Laporan Okupansi Detail per Kamar
        // getRoomOccupancyReport() di Room.php mengembalikan array objek stdClass karena PDO::FETCH_OBJ.
        $roomDetailsReport = $this->roomModel->getRoomOccupancyReport();
        
        $processedRoomDetails = [];
        $totalOccupancyPercentageSum = 0;
        $roomCountForAverage = 0;

        if (!empty($roomDetailsReport)) {
            foreach ($roomDetailsReport as $roomObject) {
                // Konversi objek stdClass dari PDO ke array asosiatif
                $roomArray = (array)$roomObject;

                // Standardisasi nama kunci untuk view. View mengharapkan 'occupancy_rate'.
                // Model (getRoomOccupancyReport) menyediakan 'occupancy_percentage'.
                if (isset($roomArray['occupancy_percentage'])) {
                    $roomArray['occupancy_rate'] = $roomArray['occupancy_percentage'];
                    // unset($roomArray['occupancy_percentage']); // Opsional: hapus kunci lama

                    $totalOccupancyPercentageSum += (float)$roomArray['occupancy_rate'];
                    $roomCountForAverage++;
                } else {
                    $roomArray['occupancy_rate'] = 0; // Default jika tidak ada
                }
                
                // Pastikan key 'total_revenue' ada, karena view mengharapkannya (walaupun di $data['best_performance'])
                // Model (getRoomOccupancyReport) sudah menyediakan 'total_revenue'.
                // View di loop mengharapkan 'revenue', jadi kita tambahkan alias jika perlu, atau ubah view.
                // Untuk konsistensi, kita akan sesuaikan view saja.
                // Jadi, $roomArray sudah punya 'total_revenue' dan 'total_bookings'.

                $processedRoomDetails[] = $roomArray;
            }
        }
        $data['rooms'] = $processedRoomDetails;

        // Hitung rata-rata tingkat okupansi keseluruhan dari detail kamar
        if ($roomCountForAverage > 0) {
            $data['average_occupancy_rate'] = number_format($totalOccupancyPercentageSum / $roomCountForAverage, 2) . '%';
        }

        // Tentukan Kamar Paling Populer dan Performa Terbaik dari $processedRoomDetails
        if (!empty($processedRoomDetails)) {
            // Kamar Paling Populer (berdasarkan total_bookings)
            $mostPopular = array_reduce($processedRoomDetails, function ($a, $b) {
                return ($a['total_bookings'] ?? 0) >= ($b['total_bookings'] ?? 0) ? $a : $b;
            });
            if ($mostPopular) {
                $data['most_popular_room'] = [
                    'room_number' => $mostPopular['room_number'] ?? '-',
                    'total_bookings' => $mostPopular['total_bookings'] ?? 0
                ];
            }

            // Performa Terbaik (berdasarkan total_revenue)
            $bestPerformance = array_reduce($processedRoomDetails, function ($a, $b) {
                // Kunci yang benar adalah 'total_revenue' dari query getRoomOccupancyReport
                return ($a['total_revenue'] ?? 0) >= ($b['total_revenue'] ?? 0) ? $a : $b;
            });
            if ($bestPerformance) {
                $data['best_performance'] = [
                    'room_number' => $bestPerformance['room_number'] ?? '-',
                    'total_revenue' => $bestPerformance['total_revenue'] ?? 0 // Kunci yang benar
                ];
            }
        }
        
        if (!defined('VIEW_PATH')) {
            define('VIEW_PATH', __DIR__ . '/../views/'); 
        }
        require_once VIEW_PATH . 'admin/reports/rooms.php';
    }

    // Export laporan ke PDF/Excel
    public function export($type = 'pdf', $report = 'bookings') {
        $dataToExport = [];
        $period = $_GET['period'] ?? 'all'; // 'all' untuk export semua data
        $startDate = !empty($_GET['start']) ? $_GET['start'] : null;
        $endDate = !empty($_GET['end']) ? $_GET['end'] : null;

        if ($report === 'bookings') {
            $dataToExport = $this->bookingModel->getBookingReport($period /*, $startDate, $endDate */); // Sesuaikan jika getBookingReport dimodifikasi
        } elseif ($report === 'revenue') {
            // Untuk revenue, kita mungkin ingin data mentah, bukan data terstruktur untuk chart
            // Bisa buat fungsi baru di model, misal getAllRevenueData($startDate, $endDate)
            // atau gunakan 'all' di getRevenueReport jika itu mengembalikan semua transaksi
             $revenueData = $this->paymentModel->getRevenueReport('all', $startDate, $endDate); // Gunakan 'all' atau sesuaikan
             $dataToExport = $revenueData->chartData; // Atau struktur data lain yang sesuai untuk export
        } elseif ($report === 'rooms') {
            $dataToExport = $this->roomModel->getRoomOccupancyReport();
        } else {
            $dataToExport = [];
        }

        if ($type === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="report_' . $report . '_' . date('Ymd') . '.xls"');
            
            // Sederhana output tabel HTML untuk Excel
            if (!empty($dataToExport)) {
                echo '<table border="1">';
                // Header
                echo '<tr>';
                foreach (array_keys((array) $dataToExport[0]) as $key) {
                    echo '<th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th>';
                }
                echo '</tr>';
                // Data
                foreach ($dataToExport as $row) {
                    echo '<tr>';
                    foreach ((array) $row as $value) {
                        echo '<td>' . htmlspecialchars($value) . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo 'Tidak ada data untuk diexport.';
            }

        } else { // PDF (Placeholder - Anda perlu library seperti TCPDF atau FPDF)
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="report_' . $report . '_' . date('Ymd') . '.pdf"');
            echo 'Export PDF Placeholder. Data: ' . count($dataToExport) . ' baris.';
            // Implementasi PDF akan lebih kompleks
        }
        exit;
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            // Sebaiknya gunakan APP_URL dari config
            header('Location: ' . APP_URL . '/admin/login');
            exit;
        }
    }
}
