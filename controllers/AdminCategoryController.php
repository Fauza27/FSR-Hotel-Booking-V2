<?php
// AdminCategoryController
class AdminCategoryController {
    private $categoryModel;
    private $roomModel;
    public function __construct() {
        require_once __DIR__ . '/../models/Category.php';
        require_once __DIR__ . '/../models/Room.php';
        $this->categoryModel = new Category();
        $this->roomModel = new Room();
        $this->checkAdminAuth();
    }

    // Tampilkan daftar kategori kamar
    public function index() {
        // Gunakan method baru yang sudah include jumlah kamar
        $categories = $this->categoryModel->getAllCategoriesWithRoomCount();
        require __DIR__ . '/../views/admin/categories/index.php';
    }


    // Form tambah kategori baru
    public function create() {
        require __DIR__ . '/../views/admin/categories/create.php';
    }

    // Simpan kategori ke database
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];
            
            if ($name === '') $errors[] = 'Nama kategori wajib diisi';
            
            if (empty($errors)) {
                // Gunakan method yang benar
                if ($this->categoryModel->createCategory(['name' => $name, 'description' => $description])) {
                    $_SESSION['success'] = 'Kategori berhasil ditambahkan';
                    header('Location: ' . APP_URL . '/admin/categories');
                    exit;
                } else {
                    $errors[] = 'Gagal menambahkan kategori';
                }
            }
            require __DIR__ . '/../views/admin/categories/create.php';
        }
    }

    // Form edit kategori
    public function edit($id) {
        $category = $this->categoryModel->getCategoryById($id);
        require __DIR__ . '/../views/admin/categories/edit.php';
    }

    // Update data kategori
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];
            if ($name === '') $errors[] = 'Nama kategori wajib diisi';
            if (empty($errors)) {
                if ($this->categoryModel->updateCategory($id, ['name' => $name, 'description' => $description])) {
                    $_SESSION['success'] = 'Kategori berhasil diperbarui';
                    header('Location: ' . APP_URL . '/admin/categories');
                    exit;
                } else {
                    $errors[] = 'Gagal memperbarui kategori';
                }
            }
            $category = $this->categoryModel->getCategoryById($id);
            require __DIR__ . '/../views/admin/categories/edit.php';
        }
    }

    // Hapus kategori (cek relasi dengan rooms)
    public function delete($id) {
        // Cek apakah ada kamar yang menggunakan kategori ini
        $roomCount = $this->categoryModel->getRoomCountByCategory($id);
        
        if ($roomCount > 0) {
            $_SESSION['error'] = "Kategori tidak dapat dihapus karena masih digunakan oleh $roomCount kamar.";
        } else {
            if ($this->categoryModel->deleteCategory($id)) {
                $_SESSION['success'] = 'Kategori berhasil dihapus';
            } else {
                $_SESSION['error'] = 'Gagal menghapus kategori';
            }
        }
        header('Location: ' . APP_URL . '/admin/categories');
        exit;
    }

    public function view($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $_SESSION['error'] = 'Kategori tidak ditemukan';
            header('Location: /admin/categories');
            exit;
        }
        $rooms = $this->roomModel->getRoomsByCategoryId($id);
        require __DIR__ . '/../views/admin/categories/view.php';
    }

    // Helper: autentikasi admin
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }
}
