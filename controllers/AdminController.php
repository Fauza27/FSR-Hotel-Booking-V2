<?php
// controllers/AdminController.php
require_once 'models/Admin.php';
require_once 'models/Room.php';
require_once 'models/Booking.php';
require_once 'models/User.php';
require_once MODEL_PATH . 'Payment.php';

// Tambahkan session_start() di paling atas file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdminController {
    private $adminModel;
    private $roomModel;
    private $bookingModel;
    private $userModel;
    private $paymentModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
        $this->roomModel = new Room();
        $this->bookingModel = new Booking();
        $this->userModel = new User();
        $this->paymentModel = new Payment();
    }
    
    // Dashboard Admin
    public function index() {
        $this->checkAdminAuth();
        
        $data = [
            'title' => 'Dashboard Admin',
            'totalRooms' => $this->roomModel->getTotalRooms(),
            'totalBookings' => $this->bookingModel->getTotalBookings(),
            'totalUsers' => $this->userModel->getTotalUsers(),
            'pendingBookings' => $this->bookingModel->getPendingBookings(),
            'recentBookings' => $this->bookingModel->getRecentBookings(5),
            'roomAvailability' => $this->roomModel->getRoomAvailabilityStats(),
            // GANTI SUMBER DATA PENDAPATAN BULANAN
            'monthlyRevenue' => $this->paymentModel->getMonthlyRevenueForDashboard(12) // Ambil 12 bulan terakhir
        ];
        
        // Untuk debugging, Anda bisa cek isi $data['monthlyRevenue'] di sini:
        // error_log('Monthly Revenue Data for Dashboard: ' . print_r($data['monthlyRevenue'], true));

        $this->view('admin/dashboard', $data); // $data sudah ada 'index'
    }
    
    // Login Admin
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Hardcoded admin bypass (check raw POST first, then trim)
            // if (isset($_POST['username'], $_POST['password']) && $_POST['username'] === 'admin10' && $_POST['password'] === 'admin1234') {
            //     $_SESSION['admin_id'] = -10;
            //     $_SESSION['admin_username'] = 'admin10';
            //     $_SESSION['admin_role'] = 'superadmin';
            //     $_SESSION['admin_name'] = 'Super Admin';
            //     $_SESSION['admin_email'] = 'admin10@example.com';
            //     $_SESSION['admin_debug'] = 'hardcoded';
            //     header('Location: /admin');
            //     exit;
            // }
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            // Hardcoded admin bypass (after trim, just in case)
            // if ($username === 'admin10' && $password === 'admin1234') {
            //     $_SESSION['admin_id'] = -10;
            //     $_SESSION['admin_username'] = 'admin10';
            //     $_SESSION['admin_role'] = 'superadmin';
            //     $_SESSION['admin_name'] = 'Super Admin';
            //     $_SESSION['admin_email'] = 'admin10@example.com';
            //     $_SESSION['admin_debug'] = 'hardcoded-trim';
            //     header('Location: /admin');
            //     exit;
            // }
            if ($this->adminModel->login($username, $password)) {
                $_SESSION['admin_id'] = (int)$this->adminModel->getAdminId();
                $_SESSION['admin_username'] = $this->adminModel->getUsername();
                $_SESSION['admin_role'] = $this->adminModel->getRole();
                $_SESSION['admin_name'] = $this->adminModel->getFullName();
                $_SESSION['admin_email'] = $this->adminModel->getEmail();
                $_SESSION['admin_debug'] = 'db';
                header('Location: /admin');
                exit;
            } else {
                $data['error'] = 'Username atau password salah';
            }
        }
        $data['title'] = 'Login Admin';
        // Tambahkan debug info ke view jika ada
        if (isset($_SESSION['admin_debug'])) {
            $data['admin_debug'] = $_SESSION['admin_debug'];
        }
        $this->loadLoginView($data);
    }
    
    // Logout Admin
    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        $_SESSION['flash_message'] = 'You have been logged out';
        $_SESSION['flash_type'] = 'success';
        
        // Redirect to login page
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    private function loadView($data) {
        $pageTitle = 'Register';
        
        require_once(VIEW_PATH . 'auth/register.php');
    }
    
    // Load login view
    private function loadLoginView($data) {
        $pageTitle = 'Login';
        
        require_once(VIEW_PATH . 'auth/login.php');
    }
    
    // Manajemen Kamar
    public function rooms() {
        $this->checkAdminAuth();
        
        $data = [
            'title' => 'Manajemen Kamar',
            'rooms' => $this->roomModel->getAllRoomsWithCategory()
        ];
        
        $this->view('admin/rooms/index', $data);
    }
    
    public function addRoom() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $roomData = [
                'room_number' => trim($_POST['room_number']),
                'category_id' => $_POST['category_id'],
                'price_per_night' => $_POST['price_per_night'],
                'capacity' => $_POST['capacity'],
                'size_sqm' => $_POST['size_sqm'],
                'description' => trim($_POST['description']),
                'status' => $_POST['status']
            ];
            
            if ($this->roomModel->addRoom($roomData)) {
                $_SESSION['success'] = 'Kamar berhasil ditambahkan';
                header('Location: /admin/rooms');
                exit;
            } else {
                $data['error'] = 'Gagal menambahkan kamar';
            }
        }
        
        $data = [
            'title' => 'Tambah Kamar',
            'categories' => $this->roomModel->getAllCategories(),
            'facilities' => $this->roomModel->getAllFacilities()
        ];
        
        $this->view('admin/rooms/add', $data);
    }
    
    public function editRoom($id) {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $roomData = [
                'room_number' => trim($_POST['room_number']),
                'category_id' => $_POST['category_id'],
                'price_per_night' => $_POST['price_per_night'],
                'capacity' => $_POST['capacity'],
                'size_sqm' => $_POST['size_sqm'],
                'description' => trim($_POST['description']),
                'status' => $_POST['status']
            ];
            
            if ($this->roomModel->updateRoom($id, $roomData)) {
                $_SESSION['success'] = 'Kamar berhasil diperbarui';
                header('Location: /admin/rooms');
                exit;
            } else {
                $data['error'] = 'Gagal memperbarui kamar';
            }
        }
        
        $data = [
            'title' => 'Edit Kamar',
            'room' => $this->roomModel->getRoomById($id),
            'categories' => $this->roomModel->getAllCategories(),
            'facilities' => $this->roomModel->getAllFacilities(),
            'roomFacilities' => $this->roomModel->getRoomFacilities($id)
        ];
        
        $this->view('admin/rooms/edit', $data);
    }
    
    public function deleteRoom($id) {
        $this->checkAdminAuth();
        
        if ($this->roomModel->deleteRoom($id)) {
            $_SESSION['success'] = 'Kamar berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kamar';
        }
        
        header('Location: /admin/rooms');
        exit;
    }
    
    // Manajemen Booking
    public function bookings() {
        $this->checkAdminAuth();
        
        $data = [
            'title' => 'Manajemen Booking',
            'bookings' => $this->bookingModel->getAllBookingsWithDetails()
        ];
        
        $this->view('admin/bookings/index', $data);
    }
    
    public function bookingDetails($id) {
        $this->checkAdminAuth();
        
        $data = [
            'title' => 'Detail Booking',
            'booking' => $this->bookingModel->getBookingDetailsById($id)
        ];
        
        $this->view('admin/bookings/details', $data);
    }
    
    public function updateBookingStatus($id) {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = $_POST['status'];
            
            if ($this->bookingModel->updateBookingStatus($id, $status)) {
                $_SESSION['success'] = 'Status booking berhasil diperbarui';
            } else {
                $_SESSION['error'] = 'Gagal memperbarui status booking';
            }
        }
        
        header('Location: /admin/bookings');
        exit;
    }
    
    // Manajemen User
    public function users() {
        $this->checkAdminAuth();
        
        $data = [
            'title' => 'Manajemen User',
            'users' => $this->userModel->getAllUsers()
        ];
        
        $this->view('admin/users/index', $data);
    }
    
    // Laporan
    public function reports() {
        $this->checkAdminAuth();
        
        $data = [
            'title' => 'Laporan',
            'dailyBookings' => $this->bookingModel->getDailyBookingReport(),
            'monthlyRevenue' => $this->bookingModel->getMonthlyRevenueReport(),
            'roomOccupancy' => $this->roomModel->getRoomOccupancyReport(),
            'topRooms' => $this->roomModel->getTopBookedRooms()
        ];
        
        $this->view('admin/reports/index', $data);
    }
    
    // Kategori Kamar
    public function categories() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $categoryData = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'])
            ];
            
            if ($this->roomModel->addCategory($categoryData)) {
                $_SESSION['success'] = 'Kategori berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan kategori';
            }
        }
        
        $data = [
            'title' => 'Manajemen Kategori',
            'categories' => $this->roomModel->getAllCategories()
        ];
        
        $this->view('admin/categories/index', $data);
    }
    
    // Fasilitas
    public function facilities() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $facilityData = [
                'name' => trim($_POST['name']),
                'icon' => trim($_POST['icon']),
                'description' => trim($_POST['description'])
            ];
            
            if ($this->roomModel->addFacility($facilityData)) {
                $_SESSION['success'] = 'Fasilitas berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan fasilitas';
            }
        }
        
        $data = [
            'title' => 'Manajemen Fasilitas',
            'facilities' => $this->roomModel->getAllFacilities()
        ];
        
        $this->view('admin/facilities/index', $data);
    }
    
    // Profile Admin
    public function profile() {
        $this->checkAdminAuth();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $profileData = [
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email'])
            ];
            
            if (!empty($_POST['password'])) {
                $profileData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            // Only update DB if not hardcoded admin
            if ($_SESSION['admin_id'] != -10) {
                if ($this->adminModel->updateProfile($_SESSION['admin_id'], $profileData)) {
                    $_SESSION['success'] = 'Profile berhasil diperbarui';
                } else {
                    $_SESSION['error'] = 'Gagal memperbarui profile';
                }
            } else {
                $_SESSION['success'] = 'Profile berhasil diperbarui (session only)';
                $_SESSION['admin_name'] = $profileData['full_name'];
                $_SESSION['admin_email'] = $profileData['email'];
            }
        }
        
        $data = [
            'title' => 'Profile Admin',
            'admin' => $_SESSION['admin_id'] == -10 ? [
                'full_name' => $_SESSION['admin_name'],
                'email' => $_SESSION['admin_email'],
                'username' => $_SESSION['admin_username'],
                'role' => $_SESSION['admin_role']
            ] : $this->adminModel->getAdminById($_SESSION['admin_id'])
        ];
        
        $this->view('admin/profile/index', $data);
    }
    
    // Helper Methods
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] === '' || $_SESSION['admin_id'] === null) {
            header('Location: /admin/login');
            exit;
        }
    }
    
    private function view($view, $data = []) {
        extract($data);
        // Fix: support subfolders and index.php
        $viewPath = "views/" . str_replace('.', '/', $view) . ".php";
        if (!file_exists($viewPath)) {
            // Try index.php in subfolder if not found
            $viewPath = "views/" . str_replace('.', '/', $view) . "/index.php";
        }
        require_once $viewPath;
    }
}
?>