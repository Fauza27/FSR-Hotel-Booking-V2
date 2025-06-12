<?php
// controllers/AdminFacilityController.php

// Pastikan config.php di-load sekali di entry point aplikasi Anda (misal index.php)
// agar base_url() tersedia dan session sudah dimulai.
// Jika belum, Anda bisa require_once __DIR__ . '/../config/config.php'; di sini
// tapi lebih baik di entry point.

class AdminFacilityController {
    private $facilityModel;

    public function __construct() {
        // Memastikan session sudah dimulai (seharusnya sudah di config.php)
        if (session_status() == PHP_SESSION_NONE) {
            // Ini sebagai fallback, idealnya session_start() hanya sekali di config.php
            // dan config.php di-include di awal script (misal index.php)
            require_once __DIR__ . '/../config/config.php'; 
        }
        require_once __DIR__ . '/../models/Facility.php';
        $this->facilityModel = new Facility();
        $this->checkAdminAuth();
    }

    // Tampilkan daftar fasilitas
    public function index() {
        $facilities = $this->facilityModel->getAllFacilities();
        require_once VIEW_PATH . 'admin/facilities/index.php'; // Gunakan konstanta VIEW_PATH
    }

    // Form tambah fasilitas
    public function create() {
        // Data untuk form jika ada error sebelumnya
        $name = '';
        $icon = '';
        $description = '';
        $errors = [];
        require_once VIEW_PATH . 'admin/facilities/create.php';
    }

    // Simpan fasilitas baru (dipanggil oleh form dari create())
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];

            if (empty($name)) $errors[] = 'Nama fasilitas wajib diisi';
            if (empty($icon)) $errors[] = 'Icon wajib diisi';
            // Anda bisa menambahkan validasi lain di sini

            if (empty($errors)) {
                // Menggunakan metode yang benar: createFacility
                if ($this->facilityModel->createFacility(['name' => $name, 'icon' => $icon, 'description' => $description])) {
                    $_SESSION['success'] = 'Fasilitas berhasil ditambahkan';
                    header('Location: ' . base_url('admin/facilities')); // Gunakan base_url()
                    exit;
                } else {
                    $errors[] = 'Gagal menambahkan fasilitas ke database.';
                }
            }
            // Jika ada error atau gagal simpan, tampilkan form lagi dengan error dan data lama
            $_SESSION['error_message'] = implode(', ', $errors); // Simpan error di session jika mau ditampilkan setelah redirect
                                                                 // atau langsung pass ke view jika tidak redirect
            // Untuk menampilkan error di form create lagi tanpa redirect:
            require_once VIEW_PATH . 'admin/facilities/create.php'; // Kirim $errors, $name, $icon, $description
        } else {
            // Jika bukan POST, redirect ke form create
            header('Location: ' . base_url('admin/facilities/create'));
            exit;
        }
    }

    // Form edit fasilitas
    public function edit($id) {
        $facility = $this->facilityModel->getFacilityById($id);
        if (!$facility) {
            $_SESSION['error'] = 'Fasilitas tidak ditemukan.';
            header('Location: ' . base_url('admin/facilities'));
            exit;
        }
        $errors = []; // Untuk menampilkan error jika ada
        require_once VIEW_PATH . 'admin/facilities/edit.php';
    }

    // Update data fasilitas
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $icon = trim($_POST['icon'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];

            if (empty($name)) $errors[] = 'Nama fasilitas wajib diisi';
            if (empty($icon)) $errors[] = 'Icon wajib diisi';
            // Validasi lain

            if (empty($errors)) {
                if ($this->facilityModel->updateFacility($id, ['name' => $name, 'icon' => $icon, 'description' => $description])) {
                    $_SESSION['success'] = 'Fasilitas berhasil diperbarui';
                    header('Location: ' . base_url('admin/facilities')); // Gunakan base_url()
                    exit;
                } else {
                    $errors[] = 'Gagal memperbarui fasilitas di database.';
                }
            }
            // Jika ada error atau gagal update, tampilkan form edit lagi dengan error
            $facility = (object) ['facility_id' => $id, 'name' => $name, 'icon' => $icon, 'description' => $description]; // Buat ulang objek facility untuk view
            $_SESSION['error_message'] = implode(', ', $errors);
            require_once VIEW_PATH . 'admin/facilities/edit.php'; // Kirim $errors dan $facility
        } else {
            // Jika bukan POST, redirect ke form edit
            header('Location: ' . base_url('admin/facilities/edit/' . $id));
            exit;
        }
    }

    // Hapus fasilitas (bisa dengan konfirmasi)
    public function delete($id) {
        // Jika metode GET, tampilkan halaman konfirmasi
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $facility = $this->facilityModel->getFacilityById($id);
            if (!$facility) {
                $_SESSION['error'] = 'Fasilitas tidak ditemukan.';
                header('Location: ' . base_url('admin/facilities'));
                exit;
            }
            // Sekarang facility adalah objek, pastikan view delete.php menggunakan object-style access
            require_once VIEW_PATH . 'admin/facilities/delete.php'; // Kirim $facility ke view
            exit;
        }

        // Jika metode POST (dari form konfirmasi), proses penghapusan
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->facilityModel->deleteFacility($id)) {
                $_SESSION['success'] = 'Fasilitas berhasil dihapus';
            } else {
                $_SESSION['error'] = 'Gagal menghapus fasilitas. Mungkin fasilitas ini masih terkait dengan data lain.';
            }
            header('Location: ' . base_url('admin/facilities')); // Gunakan base_url()
            exit;
        }
        
        // Jika bukan GET atau POST, redirect
        header('Location: ' . base_url('admin/facilities'));
        exit;
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            // Pastikan base_url() tersedia
            if (function_exists('base_url')) {
                header('Location: ' . base_url('admin/login'));
            } else {
                // Fallback jika base_url tidak ada, ini menunjukkan masalah konfigurasi lebih dalam
                header('Location: /ProjectAkhirWeb/admin/login'); 
            }
            exit;
        }
    }
}