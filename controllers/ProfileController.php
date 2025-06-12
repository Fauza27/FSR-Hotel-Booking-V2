<?php

/*
Fungsi: Mengelola alur kerja yang berkaitan dengan profil pengguna.

Tugas Spesifik:
    index():
        Memastikan pengguna login.
        Memanggil User model untuk mendapatkan detail pengguna.
        Memanggil Booking model (diasumsikan ada) dan Review model (diasumsikan ada) untuk mendapatkan jumlah booking dan review.
        Memuat view profile/index.php dan mengirimkan data pengguna, jumlah booking, dan review ke view tersebut.
    edit():
        Menampilkan halaman edit profil dan memproses perubahannya.
        Memvalidasi input.
        Memanggil User model untuk memperbarui data profil dan/atau password.
    bookings(): 
        Menampilkan riwayat booking pengguna.
    changePassword(): 
        Menampilkan halaman ganti password dan memproses perubahannya.

Ketergantungan: Membuat instance dari User model, Booking model, dan Review model.
*/

class ProfileController {
    private $userModel;
    private $bookingModel;
    private $reviewModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->bookingModel = new Booking();
        $this->reviewModel = new Review();
    }
    
    // Profile index page
    public function index() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile';
            $_SESSION['flash_message'] = 'Please login to view your profile';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Get user's bookings count
        $bookings = $this->bookingModel->getUserBookings($_SESSION['user_id']);
        $bookingsCount = count($bookings);
        
        // Get user's reviews count
        $reviewsCount = $this->reviewModel->getUserReviewsCount($_SESSION['user_id']);
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'My Profile';
        $currentPage = 'profile';
        $activeMenu = 'profile';
        
        // Load view
        require_once(VIEW_PATH . 'profile/index.php');
    }
    
    // Edit profile page
    public function edit() {
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = base_url('profile/edit');
            $_SESSION['flash_message'] = 'Please login to edit your profile';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . base_url('login'));
            exit;
        }
        
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . base_url('logout'));
            exit;
        }
        
        $data = [
            'user_id' => $user->user_id,
            'full_name' => $user->full_name,
            'email' => $user->email, // Tambahkan email untuk ditampilkan di form
            'phone' => $user->phone,
            'address' => $user->address,
            'avatar' => $user->avatar,
            'current_password' => '',
            'new_password' => '',
            'confirm_password' => '',
            'update_password' => false, // Akan di-set true jika current_password diisi
            'errors' => []
        ];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data['full_name'] = trim($_POST['full_name']);
            $data['phone'] = trim($_POST['phone']);
            $data['address'] = trim($_POST['address']);
            
            // Hanya proses password jika current_password diisi
            if (!empty(trim($_POST['current_password']))) {
                $data['update_password'] = true;
                $data['current_password'] = $_POST['current_password']; // Jangan trim password
                $data['new_password'] = $_POST['new_password'] ?? '';
                $data['confirm_password'] = $_POST['confirm_password'] ?? '';
            } else {
                $data['update_password'] = false;
            }


            // Validasi dasar
            if(empty($data['full_name'])) $data['errors']['full_name'] = 'Please enter your full name.';
            if(empty($data['phone'])) $data['errors']['phone'] = 'Please enter your phone number.';

            // Handle avatar upload
            $newAvatarName = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar'];
                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($fileExt, ALLOWED_AVATAR_EXTENSIONS)) {
                    $data['errors']['avatar'] = 'Invalid file type. Only ' . implode(', ', ALLOWED_AVATAR_EXTENSIONS) . ' are allowed.';
                } elseif ($fileSize > MAX_AVATAR_SIZE) {
                    $data['errors']['avatar'] = 'File size exceeds maximum limit of ' . (MAX_AVATAR_SIZE / 1024 / 1024) . 'MB.';
                } else {
                    $newAvatarName = 'avatar_user_' . $data['user_id'] . '_' . uniqid('', true) . '.' . $fileExt;
                    $avatarDestination = AVATAR_UPLOAD_PATH . $newAvatarName;

                    if (move_uploaded_file($fileTmpName, $avatarDestination)) {
                        // Hapus avatar lama jika ada, bukan placeholder, dan file ada
                        if ($user->avatar && $user->avatar !== 'user-placeholder.png' && file_exists(AVATAR_UPLOAD_PATH . $user->avatar)) {
                            unlink(AVATAR_UPLOAD_PATH . $user->avatar);
                        }
                    } else {
                        $data['errors']['avatar'] = 'Failed to move uploaded file. Check permissions on ' . AVATAR_UPLOAD_PATH;
                        $newAvatarName = null; 
                    }
                }
            } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $data['errors']['avatar'] = 'Error uploading file. Code: ' . $_FILES['avatar']['error'];
            }

            // Validasi password (hanya jika pengguna mencoba mengubahnya)
            if($data['update_password']) {
                if(empty($data['current_password'])) { // Sebenarnya ini sudah ditangani oleh !empty(trim($_POST['current_password'])) di atas, tapi sebagai double check
                    $data['errors']['current_password'] = 'Please enter your current password.';
                } elseif(!password_verify($data['current_password'], $user->password)) {
                    $data['errors']['current_password'] = 'Current password is incorrect.';
                }
                
                if(empty($data['new_password'])) {
                    $data['errors']['new_password'] = 'Please enter a new password.';
                } elseif(strlen($data['new_password']) < 6) {
                    $data['errors']['new_password'] = 'New password must be at least 6 characters.';
                }
                
                if(empty($data['confirm_password'])) {
                    $data['errors']['confirm_password'] = 'Please confirm new password.';
                } elseif($data['new_password'] != $data['confirm_password']) {
                    $data['errors']['confirm_password'] = 'New passwords do not match.';
                }
            }
            
            if(empty($data['errors'])) {
                // Semua validasi (profil, avatar, dan password jika ada) lolos
                $profileUpdatedSuccessfully = false;
                $passwordChangedSuccessfully = false;
                $anyChangeMade = false;

                // 1. Update data profil (selain password)
                $updateProfileData = [
                    'user_id' => $data['user_id'],
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address']
                ];
                if ($newAvatarName) {
                    $updateProfileData['avatar'] = $newAvatarName;
                }

                // Cek apakah ada perubahan data profil
                $profileDataHasChanged = (
                    $updateProfileData['full_name'] !== $user->full_name ||
                    $updateProfileData['phone'] !== $user->phone ||
                    $updateProfileData['address'] !== $user->address ||
                    (isset($updateProfileData['avatar']) && $updateProfileData['avatar'] !== $user->avatar)
                );

                if ($profileDataHasChanged) {
                    if ($this->userModel->updateUser($updateProfileData)) {
                        $profileUpdatedSuccessfully = true;
                        $anyChangeMade = true;
                    } else {
                        // Gagal update profil, set error umum
                        $_SESSION['flash_message'] = 'Failed to update profile information.';
                        $_SESSION['flash_type'] = 'danger';
                        // Biarkan view dirender dengan data saat ini
                        require_once(VIEW_PATH . 'profile/edit.php');
                        exit;
                    }
                } else {
                    // Tidak ada perubahan data profil, anggap "berhasil" untuk langkah ini
                    $profileUpdatedSuccessfully = true; // Tidak ada error, jadi true
                }

                // 2. Update password jika diminta dan validasi password lolos
                if($data['update_password'] && $profileUpdatedSuccessfully) { // Hanya jika profil ok
                    if($this->userModel->updatePassword($data['user_id'], $data['new_password'])) {
                        $passwordChangedSuccessfully = true;
                        $anyChangeMade = true;
                    } else {
                         // Gagal update password, set error umum
                        $_SESSION['flash_message'] = 'Failed to change password.';
                        $_SESSION['flash_type'] = 'danger';
                        // Biarkan view dirender dengan data saat ini (password fields akan kosong)
                        $data['current_password'] = $data['new_password'] = $data['confirm_password'] = '';
                        require_once(VIEW_PATH . 'profile/edit.php');
                        exit;
                    }
                }

                // Memberikan feedback berdasarkan hasil
                if ($anyChangeMade) {
                    $flashMessages = [];
                    if ($profileDataHasChanged && $profileUpdatedSuccessfully) {
                        $flashMessages[] = 'Profile details updated.';
                    }
                    if ($data['update_password'] && $passwordChangedSuccessfully) {
                        $flashMessages[] = 'Password changed.';
                    }
                    $_SESSION['flash_message'] = implode(' ', $flashMessages) . ' Successfully.';
                    $_SESSION['flash_type'] = 'success';
                } else {
                     $_SESSION['flash_message'] = 'No changes were made to your profile.';
                     $_SESSION['flash_type'] = 'info';
                }
                header('Location: ' . base_url('profile'));
                exit;
                
            }
            // Jika ada $data['errors'], maka akan jatuh ke render view di bawah
        }
        
        $pageTitle = 'Edit Profile';
        $currentPage = 'profile';
        $activeMenu = 'edit';
        
        // Jika ada error dan ini adalah POST request, data yang diinput pengguna akan tetap ada di $data
        // Jika ini GET request, $data diinisialisasi dengan data dari $user
        require_once(VIEW_PATH . 'profile/edit.php');
    }


    
    // User bookings page
    public function bookings() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile/bookings';
            $_SESSION['flash_message'] = 'Please login to view your bookings';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details for the sidebar
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Get user bookings
        $bookings = $this->bookingModel->getUserBookings($_SESSION['user_id']);
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'My Bookings';
        $currentPage = 'profile';
        $activeMenu = 'booking_history';
        
        // Load view directly, not through an include in another view
        require_once(VIEW_PATH . 'profile/booking_history.php');
    }
    
    // Change password page
    public function changePassword() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile/change-password';
            $_SESSION['flash_message'] = 'Please login to change your password';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // If user not found, redirect to logout
        if(!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/logout');
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'user_id' => $_SESSION['user_id'],
                'current_password' => $_POST['current_password'],
                'new_password' => $_POST['new_password'],
                'confirm_password' => $_POST['confirm_password'],
                'errors' => []
            ];
            
            // Validate current password
            if(empty($data['current_password'])) {
                $data['errors']['current_password'] = 'Please enter your current password';
            } elseif(!password_verify($data['current_password'], $user->password)) {
                $data['errors']['current_password'] = 'Current password is incorrect';
            }
            
            // Validate new password
            if(empty($data['new_password'])) {
                $data['errors']['new_password'] = 'Please enter a new password';
            } elseif(strlen($data['new_password']) < 6) {
                $data['errors']['new_password'] = 'Password must be at least 6 characters';
            }
            
            // Validate confirm password
            if($data['new_password'] != $data['confirm_password']) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }
            
            // If no errors, update password
            if(empty($data['errors'])) {
                if($this->userModel->updatePassword($data['user_id'], $data['new_password'])) {
                    $_SESSION['flash_message'] = 'Password updated successfully';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . APP_URL . '/profile');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Failed to update password';
                    $_SESSION['flash_type'] = 'danger';
                }
            }
            
            // If we get here, there were errors
            // We'll fall through to the view with the data
        } else {
            // Initialize data array
            $data = [
                'user_id' => $user->user_id,
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'errors' => []
            ];
        }
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'Change Password';
        $currentPage = 'profile';
        $activeMenu = 'change_password';
        
        // Load view
        require_once(VIEW_PATH . 'profile/change_password.php');
    }
}