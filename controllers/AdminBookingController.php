<?php
// AdminBookingController
class AdminBookingController {
    private $bookingModel;
    private $roomModel;
    private $paymentModel;
    public function __construct() {
        require_once __DIR__ . '/../models/Booking.php';
        require_once __DIR__ . '/../models/Room.php';
        require_once __DIR__ . '/../models/Payment.php';
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
        $this->paymentModel = new Payment(); 
        $this->checkAdminAuth();
    }

    // Tampilkan daftar booking dengan filter status dan tanggal
    public function index() {
        // Pengaturan Pagination
        $limit = 10; // Jumlah item per halaman
        $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Mengambil parameter filter
        $status = $_GET['status'] ?? '';
        $dateStart = $_GET['date_start'] ?? '';
        $dateEnd = $_GET['date_end'] ?? '';
        
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }
        if ($dateStart) {
            $filters['date_start'] = $dateStart;
        }
        if ($dateEnd) {
            $filters['date_end'] = $dateEnd;
        }
        
        // Mengambil data booking dengan filter dan pagination
        $bookings = $this->bookingModel->getAllBookingsPaginatedFiltered($limit, $offset, $filters);
        
        // Mengambil total booking untuk pagination
        $totalBookings = $this->bookingModel->getTotalBookingsFiltered($filters);
        $totalPages = ceil($totalBookings / $limit);

        // Data untuk diteruskan ke view
        $data = [
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalBookings,
            'limit' => $limit,
            'filters' => $filters // Untuk mempertahankan filter di URL pagination
        ];

        require __DIR__ . '/../views/admin/bookings/index.php';
    }

    // Tampilkan detail booking lengkap
    public function view($id = null) {
        if ($id === null) {
            // Pastikan ID ada di query string jika tidak diberikan di URL
            $id = $_GET['id'] ?? null;
        }

        if ($id) {
            $booking = $this->bookingModel->getBookingById($id);

            if ($booking) {
                // Ambil data pembayaran terkait booking_id
                // getPaymentsByBookingId mengembalikan array, kita asumsikan 1 booking bisa punya >1 payment attempt
                // atau hanya 1 payment. Jika hanya 1, kita ambil yang pertama.
                $payments = $this->paymentModel->getPaymentsByBookingId($booking->booking_id);
                $payment = !empty($payments) ? $payments[0] : null; // Ambil pembayaran pertama jika ada

                // Untuk $statusHistory:
                // Saat ini tidak ada tabel untuk menyimpan histori perubahan status.
                // Jadi, $statusHistory akan kosong.
                // Jika Anda ingin menampilkan status saat ini dan kapan terakhir diupdate,
                // Anda bisa menggunakan $booking->status dan $booking->updated_at
                $statusHistory = []; // Kosongkan untuk saat ini, atau implementasikan logic jika ada tabelnya

                require __DIR__ . '/../views/admin/bookings/view.php';
            } else {
                $_SESSION['error'] = 'Booking tidak ditemukan.';
                header('Location: ' . base_url('admin/bookings'));
                exit;
            }
        } else {
            // Redirect ke daftar booking jika ID tidak valid
            $_SESSION['error'] = 'ID booking tidak ditemukan';
            header('Location: ' . base_url('admin/bookings'));
            exit;
        }
    }

    // Update status booking (confirmed/cancelled)
    public function updateStatus($id) { // $id akan diisi oleh router dari path URL
        $status = $_GET['status'] ?? ''; // Ambil status baru dari query string

        if ($id && $status && in_array($status, ['confirmed', 'cancelled', 'pending', 'completed'])) {
            $success = $this->bookingModel->updateBookingStatus($id, $status);
            
            // Model Booking->updateBookingStatus sudah menangani update status kamar.
            // Jadi, tidak perlu logika update status kamar di sini lagi.

            if ($success) {
                $_SESSION['success'] = 'Status booking #' . $id . ' berhasil diperbarui menjadi ' . ucfirst($status) . '.';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui status booking #' . $id . '.';
            }
        } else {
            $_SESSION['error'] = 'Data tidak valid untuk pembaruan status booking. ID: '.$id.', Status: '.$status;
        }

        // Redirect kembali ke halaman daftar booking
        header('Location: ' . base_url('admin/bookings'));
        exit;
    }

    // Batalkan booking dan update status kamar
    // Metode ini akan dipanggil jika ada route khusus ke admin/bookings/cancel/{id}
    // Jika semua pembatalan dari index.php menggunakan updateStatus?status=cancelled, maka metode ini mungkin tidak terpakai dari sana.
    public function cancel($id) {
        if ($id) {
            $success = $this->bookingModel->updateBookingStatus($id, 'cancelled');
            // Model Booking->updateBookingStatus sudah menangani update status kamar.

            if ($success) {
                $_SESSION['success'] = 'Booking #' . $id . ' berhasil dibatalkan.';
            } else {
                $_SESSION['error'] = 'Gagal membatalkan booking #' . $id . '.';
            }
        } else {
             $_SESSION['error'] = 'ID booking tidak valid untuk pembatalan.';
        }
        header('Location: ' . base_url('admin/bookings'));
        exit;
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }
}
