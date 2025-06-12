<?php

/*
Fungsi: Mengelola alur kerja yang berkaitan dengan otentikasi pengguna.

Tugas Spesifik:
    register():
        Menampilkan halaman registrasi (memanggil loadView() yang me-render views/auth/register.php).
        Memproses data dari form registrasi: mengambil input $_POST, melakukan validasi.
        Jika valid, memanggil metode register() di User model.
        Berdasarkan hasil dari Model, membuat session, menampilkan pesan sukses/gagal, dan mengarahkan pengguna.
    login():
        Menampilkan halaman login (memanggil loadLoginView() yang me-render views/auth/login.php).
        Memproses data dari form login: mengambil input $_POST, validasi dasar.
        Memanggil metode login() di User model.
        Jika berhasil, membuat session, membuat cookie "remember me" (jika dicentang) dengan bantuan Auth model, dan mengarahkan pengguna (ke dashboard admin atau halaman utama).
    logout(): 
        Menghapus session, menghapus cookie "remember me" (dengan bantuan Auth model), dan mengarahkan ke halaman login.
    forgotPassword(): 
        Menampilkan halaman lupa password dan (secara konseptual) akan memproses permintaan reset password (saat ini hanya menampilkan pesan sukses).

Ketergantungan: Membuat instance dari Auth model dan User model untuk berinteraksi dengan mereka.
*/

class AuthController {
    private $authModel;
    private $userModel;
    
    public function __construct() {
        $this->authModel = new Auth();
        $this->userModel = new User();
    }
    
    // Register page
    public function register() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL);
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'full_name' => trim($_POST['full_name']),
                'phone' => trim($_POST['phone']),
                'address' => trim($_POST['address']),
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'full_name_err' => '',
                'phone_err' => ''
            ];
            
            // Validate username
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username';
            } else {
                // Check if username already exists
                if($this->userModel->findUserByUsername($data['username'])) {
                    $data['username_err'] = 'Username is already taken';
                }
            }
            
            // Validate email
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                // Check if email already exists
                if($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already registered';
                }
            }
            
            // Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }
            
            // Validate confirm password
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }
            
            // Validate full name
            if(empty($data['full_name'])) {
                $data['full_name_err'] = 'Please enter your full name';
            }
            
            // Validate phone
            if(empty($data['phone'])) {
                $data['phone_err'] = 'Please enter your phone number';
            }
            
            // Make sure errors are empty
            if(empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && 
               empty($data['confirm_password_err']) && empty($data['full_name_err']) && empty($data['phone_err'])) {
                
                // Register user
                $user_id = $this->userModel->register($data);
                
                if($user_id) {
                    $_SESSION['flash_message'] = 'Registration successful! You can now log in.';
                    $_SESSION['flash_type'] = 'success';
                    
                    header('Location: ' . APP_URL . '/login');
                    exit;
                } else {
                    // Something went wrong
                    $_SESSION['flash_message'] = 'Something went wrong. Please try again.';
                    $_SESSION['flash_type'] = 'danger';
                    
                    $this->loadView($data);
                }
            } else {
                // Load view with errors
                $this->loadView($data);
            }
        } else {
            // Initialize data array
            $data = [
                'username' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'full_name' => '',
                'phone' => '',
                'address' => '',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'full_name_err' => '',
                'phone_err' => ''
            ];
            
            // Load view
            $this->loadView($data);
        }
    }
    
    // Login page
    public function login() {
        // Check if user is already logged in
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL);
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'username' => trim($_POST['username']),
                'password' => $_POST['password'],
                'remember' => isset($_POST['remember']) ? true : false,
                'username_err' => '',
                'password_err' => ''
            ];
            
            // Validate username
            if(empty($data['username'])) {
                $data['username_err'] = 'Please enter username or email';
            }
            
            // Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }
            
            // Check if all errors are empty
            if(empty($data['username_err']) && empty($data['password_err'])) {
                // Check if login is successful
                $user = $this->userModel->login($data['username'], $data['password']);
                  if($user) {
                    // User authenticated, create session
                    $_SESSION['user_id'] = $user->user_id;
                    $_SESSION['user_username'] = $user->username;
                    $_SESSION['user_email'] = $user->email;
                    $_SESSION['user_name'] = $user->full_name;
                    
                    // Set remember me cookie if requested
                    if($data['remember']) {
                        $token = $this->authModel->createRememberToken($user->user_id);
                        if($token) {
                            setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                        }
                    }
                    
                    $_SESSION['flash_message'] = 'Login successful! Welcome back, ' . $user->full_name;
                    $_SESSION['flash_type'] = 'success';
                    
                    // Check user role and redirect accordingly
                    if($user->role === 'admin') {
                        // Set admin session
                        $_SESSION['admin_id'] = $user->user_id;
                        $_SESSION['admin_username'] = $user->username;
                        $_SESSION['admin_role'] = $user->role;
                        header('Location: ' . APP_URL . '/admin');
                    } else {
                        // Redirect to intended page if set, otherwise to home page
                        if(isset($_SESSION['intended_url'])) {
                            $intended_url = $_SESSION['intended_url'];
                            unset($_SESSION['intended_url']);
                            header('Location: ' . $intended_url);
                        } else {
                            header('Location: ' . APP_URL);
                        }
                    }
                    exit;
                } else {
                    // Login failed
                    $data['password_err'] = 'Invalid username/email or password';
                    
                    $this->loadLoginView($data);
                }
            } else {
                // Load view with errors
                $this->loadLoginView($data);
            }
        } else {
            // Initialize data array
            $data = [
                'username' => '',
                'password' => '',
                'remember' => false,
                'username_err' => '',
                'password_err' => ''
            ];
            
            // Load view
            $this->loadLoginView($data);
        }
    }
    
    // Logout
    public function logout() {
        // Unset all session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        
        // Delete remember me cookie if exists
        if(isset($_COOKIE['remember_token'])) {
            $this->authModel->deleteRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Flash message
        $_SESSION['flash_message'] = 'You have been logged out';
        $_SESSION['flash_type'] = 'success';
        
        // Redirect to login page
        header('Location: ' . APP_URL . '/login');
        exit;
    }
    
    // Load register view
    private function loadView($data) {
        $pageTitle = 'Register';
        
        require_once(VIEW_PATH . 'auth/register.php');
    }
    
    // Load login view
    private function loadLoginView($data) {
        $pageTitle = 'Login';
        
        require_once(VIEW_PATH . 'auth/login.php');
    }

    // Add this method to your AuthController.php class

// Forgot Password page
public function forgotPassword() {
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL);
            exit;
        }
        
        $data = [
            'email' => '',
            'email_err' => '',
            'show_password_form' => false // Flag untuk view
        ];

        // Jika ada email di session (dari tahap validasi email sebelumnya)
        // dan kita kembali ke halaman ini karena error validasi password
        if (isset($_SESSION['reset_email_stage']) && $_SESSION['reset_email_stage']) {
            $data['email'] = $_SESSION['reset_email_stage'];
            $data['show_password_form'] = true;
            // Ambil error password jika ada dari session flash (akan diatur di resetPassword)
            if (isset($_SESSION['password_errors'])) {
                $data = array_merge($data, $_SESSION['password_errors']);
                unset($_SESSION['password_errors']);
            }
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Tahap 1: Submit email
            if (isset($_POST['submit_email'])) {
                $data['email'] = trim($_POST['email']);
                
                if(empty($data['email'])) {
                    $data['email_err'] = 'Please enter email';
                } elseif(!$this->userModel->findUserByEmail($data['email'])) { // Menggunakan findUserByEmail yang return boolean
                    $data['email_err'] = 'No account found with that email';
                }
                
                if(empty($data['email_err'])) {
                    // Email valid, simpan di session untuk tahap berikutnya
                    $_SESSION['reset_email_stage'] = $data['email'];
                    $data['show_password_form'] = true; // Tampilkan form password
                    // Tidak perlu redirect, langsung tampilkan view dengan state baru
                }
            }
            // Jika POST tapi bukan 'submit_email', berarti itu dari form reset password
            // yang seharusnya ditangani oleh metode resetPassword() dan route 'reset-password'
            // Namun, jika ada kesalahan routing dan POST sampai di sini tanpa 'submit_email',
            // lebih baik jangan lakukan apa-apa atau redirect.
            // Untuk skenario ini, form reset password akan POST ke /reset-password.
        }
        
        $this->loadForgotPasswordView($data);
    }

    // Metode baru untuk menangani submisi password baru
    public function resetPassword() {
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL);
            exit;
        }

        // Pastikan kita punya email dari tahap sebelumnya
        if (!isset($_SESSION['reset_email_stage'])) {
            $_SESSION['flash_message'] = 'Invalid session. Please start over.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/forgot-password');
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'email' => $_SESSION['reset_email_stage'], // Ambil email dari session
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'password_err' => '',
                'confirm_password_err' => '',
                'show_password_form' => true // Agar view tetap menampilkan form password
            ];

            // Validasi password
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter new password';
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }
            
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm new password';
            } elseif($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Passwords do not match';
            }

            if(empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Dapatkan user berdasarkan email untuk mendapatkan ID
                $user = $this->userModel->getUserByEmail($data['email']);
                if ($user) {
                    if($this->userModel->updatePassword($user->user_id, $data['password'])) {
                        unset($_SESSION['reset_email_stage']); // Hapus session email reset
                        $_SESSION['flash_message'] = 'Password has been reset successfully. You can now login.';
                        $_SESSION['flash_type'] = 'success';
                        header('Location: ' . APP_URL . '/login');
                        exit;
                    } else {
                        $_SESSION['flash_message'] = 'Failed to reset password. Please try again.';
                        $_SESSION['flash_type'] = 'danger';
                        // Kembali ke form forgot password (dengan state reset password)
                        // Simpan error di session flash untuk ditampilkan oleh forgotPassword
                        $_SESSION['password_errors'] = ['password_err' => 'Failed to update password in database.'];
                        header('Location: ' . APP_URL . '/forgot-password');
                        exit;
                    }
                } else {
                    // Seharusnya tidak terjadi jika validasi email awal benar
                    $_SESSION['flash_message'] = 'User not found. Please start over.';
                    $_SESSION['flash_type'] = 'danger';
                    unset($_SESSION['reset_email_stage']);
                    header('Location: ' . APP_URL . '/forgot-password');
                    exit;
                }
            } else {
                // Ada error validasi password, kembali ke halaman forgot-password
                // dengan state menampilkan form password dan errornya.
                // Simpan error di session flash untuk ditampilkan oleh forgotPassword
                $_SESSION['password_errors'] = [
                    'password_err' => $data['password_err'],
                    'confirm_password_err' => $data['confirm_password_err']
                ];
                header('Location: ' . APP_URL . '/forgot-password');
                exit;
            }
        } else {
            // Jika bukan POST, redirect ke awal
            header('Location: ' . APP_URL . '/forgot-password');
            exit;
        }
    }
    // Load forgot password view
    private function loadForgotPasswordView($data) {
        require_once(VIEW_PATH . 'auth/forgot_password.php');
    }
}