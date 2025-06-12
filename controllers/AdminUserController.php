<?php
// AdminUserController

class AdminUserController {
    private $userModel;
    // private $bookingModel; // Tidak lagi dibutuhkan di sini jika User Model sudah menghitung total_booking

    public function __construct() {
        // Pastikan path ini benar dan konsisten dengan config.php
        require_once ROOT_PATH . '/models/User.php';
        // require_once ROOT_PATH . '/models/Booking.php'; // Tidak dibutuhkan jika User Model handle total_booking
        
        $this->userModel = new User();
        // $this->bookingModel = new Booking();
        
        // Dianjurkan memanggil checkAdminAuth di setiap metode yang memerlukan otentikasi
        // atau jika semua metode private, bisa di constructor.
    }

    public function index() {
        $this->checkAdminAuth(); // Pastikan admin sudah login

        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Ambil dari config atau default 10
        $itemsPerPage = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 10; 
        
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($role) && in_array($role, ['user', 'admin'])) { // Validasi role
            $filters['role'] = $role;
        }

        $totalUsers = $this->userModel->countUsers($filters);
        $totalPages = ceil($totalUsers / $itemsPerPage);
        
        // Validasi currentPage
        if ($currentPage < 1) {
            $currentPage = 1;
        } elseif ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $itemsPerPage;
        
        // getUsers sekarang juga akan mengembalikan total_booking
        $users = $this->userModel->getUsers($filters, $itemsPerPage, $offset);
        
        // Data untuk view
        $data = [
            'users' => $users,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'itemsPerPage' => $itemsPerPage,
            'search' => $search,
            'role' => $role,
            'startNo' => $offset + 1 // Nomor urut awal untuk tabel
        ];

        require VIEW_PATH . 'admin/users/index.php';
    }

    public function view($id) {
        $this->checkAdminAuth();
        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan.';
            header('Location: ' . base_url('admin/users'));
            exit;
        }
        // Anda mungkin masih memerlukan BookingModel di sini jika Anda ingin menampilkan detail booking
        require_once ROOT_PATH . '/models/Booking.php';
        $bookingModel = new Booking();
        $bookings = $bookingModel->getBookingsByUserId($id);

        $data = [
            'user' => $user,
            'bookings' => $bookings
        ];
        require VIEW_PATH . 'admin/users/view.php';
    }

    // Metode block dan unblock memerlukan kolom 'status' di tabel 'users'
    // Jika kolom tersebut belum ada, fitur ini tidak akan berfungsi sebagaimana mestinya.
    // Asumsikan Anda akan menambahkan kolom `status` ENUM('active', 'blocked') DEFAULT 'active'
    public function block($id) {
        $this->checkAdminAuth();
         // Anda perlu menambahkan metode seperti updateUserStatus di UserModel
        // if ($this->userModel->updateUserStatus($id, 'blocked')) { 
        //     $_SESSION['success'] = 'User berhasil diblokir.';
        // } else {
        //     $_SESSION['error'] = 'Gagal memblokir user.';
        // }
        // Untuk saat ini, karena tidak ada kolom status:
        $_SESSION['info'] = 'Fitur blokir user memerlukan kolom status pada tabel users.';
        header('Location: ' . base_url('admin/users/view/' . $id));
        exit;
    }

    public function unblock($id) {
        $this->checkAdminAuth();
        // if ($this->userModel->updateUserStatus($id, 'active')) {
        //     $_SESSION['success'] = 'Blokir user berhasil dibuka.';
        // } else {
        //     $_SESSION['error'] = 'Gagal membuka blokir user.';
        // }
        $_SESSION['info'] = 'Fitur unblock user memerlukan kolom status pada tabel users.';
        header('Location: ' . base_url('admin/users/view/' . $id));
        exit;
    }

    public function makeAdmin($id = null) {
        $this->checkAdminAuth();
        
        error_log("makeAdmin method called in AdminUserController");
        error_log("ID parameter (path): " . print_r($id, true));
        error_log("GET parameters: " . print_r($_GET, true));
        
        // Ambil ID dari path jika ada, jika tidak dari query string
        if ($id === null && isset($_GET['id'])) {
            $id = $_GET['id'];
            error_log("Got ID from query string: $id");
        }
        
        if (!$id || !is_numeric($id)) {
            error_log("Invalid user ID: $id");
            $_SESSION['error'] = 'User ID tidak valid.';
            header('Location: ' . base_url('admin/users'));
            exit;
        }

        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            error_log("User not found: $id");
            $_SESSION['error'] = 'User tidak ditemukan.';
            header('Location: ' . base_url('admin/users'));
            exit;
        }

        if ($user->role === 'admin') {
            error_log("User $id is already admin");
            $_SESSION['info'] = 'User ini sudah menjadi admin.'; // Gunakan 'info' atau 'warning'
            header('Location: ' . base_url('admin/users'));
            exit;
        }

        error_log("Attempting to make user $id an admin");
        if ($this->userModel->makeAdmin($id)) {
            error_log("Successfully made user $id an admin");
            $_SESSION['success'] = 'User berhasil dijadikan admin.';
        } else {
            error_log("Failed to make user $id an admin");
            $_SESSION['error'] = 'Gagal mengubah role user.';
        }

        header('Location: ' . base_url('admin/users'));
        exit;
    }

    private function checkAdminAuth() {
        if (session_status() == PHP_SESSION_NONE) { // Pastikan session sudah dimulai
            session_start();
        }

        $isAdminLoggedIn = false;
        if (isset($_SESSION['user_id'])) {
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            if ($user && $user->role === 'admin') {
                $isAdminLoggedIn = true;
                // Set session admin_id jika belum ada, untuk konsistensi
                if (!isset($_SESSION['admin_id'])) {
                    $_SESSION['admin_id'] = $_SESSION['user_id'];
                }
            }
        } elseif (isset($_SESSION['admin_id'])) { // Fallback jika hanya admin_id yang di-set
             $admin = $this->userModel->getUserById($_SESSION['admin_id']); // Verifikasi admin_id dari DB
             if ($admin && $admin->role === 'admin') {
                $isAdminLoggedIn = true;
             }
        }


        if (!$isAdminLoggedIn) {
            $_SESSION['error'] = 'Anda harus login sebagai admin untuk mengakses halaman ini.';
            // Gunakan base_url() dari config.php
            header('Location: ' . base_url('admin/login')); 
            exit;
        }
    }
}
?>