<?php
/**
 * Model Category
 * Bertanggung jawab untuk semua interaksi dengan tabel `room_categories` di database.
 * Ini mencakup operasi CRUD (Create, Read, Update, Delete) dan query kustom lainnya
 * untuk mendapatkan data yang berhubungan dengan kategori kamar.
 */
class Category {
    // Properti untuk menampung data kategori (opsional, bisa digunakan untuk mapping)
    public $categoryId;
    public $name;
    public $description;
    
    // Properti privat untuk koneksi database
    private $db;

    /**
     * Constructor
     * Inisialisasi koneksi database saat objek Category dibuat.
     */
    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = new Database();
    }

    /*
    |--------------------------------------------------------------------------
    | Operasi Create (Membuat Data Baru)
    |--------------------------------------------------------------------------
    */

    // Menambahkan kategori baru ke dalam database.
    public function createCategory($data) {
        $this->db->query('INSERT INTO room_categories (name, description) VALUES (:name, :description)');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null); // Menggunakan null coalescing operator untuk default value
        
        return $this->db->execute();
    }
    /*
        Query: INSERT INTO room_categories (name, description) VALUES (:name, :description)
        - INSERT INTO room_categories: Perintah untuk menyisipkan baris baru ke dalam tabel `room_categories`.
        - (name, description): Menentukan kolom-kolom yang akan diisi.
        - VALUES (:name, :description): Menentukan nilai yang akan disisipkan, menggunakan placeholder (:name, :description)
          untuk mencegah SQL Injection. Nilai sebenarnya diikat (bind) secara terpisah.
    */


    /*
    |--------------------------------------------------------------------------
    | Operasi Read (Membaca Data)
    |--------------------------------------------------------------------------
    */

    // Mengambil satu data kategori spesifik berdasarkan ID-nya.
    public function getCategoryById($id) {
        $this->db->query('SELECT * FROM room_categories WHERE category_id = :category_id');
        $this->db->bind(':category_id', $id);
        return $this->db->single();
    }
    /*
        Query: SELECT * FROM room_categories WHERE category_id = :category_id
        - SELECT *: Mengambil semua kolom dari tabel.
        - FROM room_categories: Menentukan tabel sumber data, yaitu `room_categories`.
        - WHERE category_id = :category_id: Klausa filter untuk hanya mengambil baris yang `category_id`-nya cocok
          dengan ID yang diberikan.
    */

    // Mengambil semua data kategori dari database, diurutkan berdasarkan nama.
    public function getAllCategories() {
        $this->db->query('SELECT * FROM room_categories ORDER BY name ASC');
        return $this->db->resultSet();
    }
    /*
        Query: SELECT * FROM room_categories ORDER BY name ASC
        - SELECT *: Mengambil semua kolom.
        - FROM room_categories: Mengambil data dari tabel `room_categories`.
        - ORDER BY name ASC: Mengurutkan hasil query berdasarkan kolom `name` secara menaik (Ascending/A-Z).
    */

    // Mengambil semua kategori beserta jumlah kamar yang terkait pada setiap kategori.
    public function getAllCategoriesWithRoomCount() {
        $this->db->query('
            SELECT rc.*, COUNT(r.room_id) as room_count 
            FROM room_categories rc 
            LEFT JOIN rooms r ON rc.category_id = r.category_id 
            GROUP BY rc.category_id 
            ORDER BY rc.name ASC
        ');
        return $this->db->resultSet();
    }
    /*
        Query:
        - SELECT rc.*, COUNT(r.room_id) as room_count
          - rc.*: Mengambil semua kolom dari tabel `room_categories` (dengan alias `rc`).
          - COUNT(r.room_id) as room_count: Menghitung jumlah `room_id` dari tabel `rooms` (alias `r`) untuk setiap
            grup dan menamainya `room_count`. Ini adalah jumlah kamar per kategori.
        
        - FROM room_categories rc
          Menjadikan `room_categories` sebagai tabel utama dengan alias `rc`.
        
        - LEFT JOIN rooms r ON rc.category_id = r.category_id
          Menggabungkan dengan tabel `rooms` (alias `r`) berdasarkan `category_id`.
          LEFT JOIN digunakan agar kategori yang belum memiliki kamar (jumlah kamar = 0) tetap ditampilkan di hasil query.

        - GROUP BY rc.category_id
          Mengelompokkan hasil berdasarkan `category_id` agar fungsi agregat `COUNT()` dapat menghitung jumlah kamar
          untuk setiap kategori secara terpisah.

        - ORDER BY rc.name ASC
          Mengurutkan hasil akhir berdasarkan nama kategori dari A hingga Z.
    */


    /*
    |--------------------------------------------------------------------------
    | Operasi Update (Memperbarui Data)
    |--------------------------------------------------------------------------
    */

    // Memperbarui data kategori yang sudah ada berdasarkan ID.
    public function updateCategory($id, $data) {
        $this->db->query('UPDATE room_categories SET name = :name, description = :description WHERE category_id = :category_id');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':category_id', $id);
        
        return $this->db->execute();
    }
    /*
        Query: UPDATE room_categories SET name = :name, description = :description WHERE category_id = :category_id
        - UPDATE room_categories: Perintah untuk memperbarui data di tabel `room_categories`.
        - SET name = :name, description = :description: Menentukan kolom mana yang akan diubah beserta nilai barunya
          (menggunakan placeholder).
        - WHERE category_id = :category_id: Klausa filter yang memastikan hanya baris dengan ID yang cocok yang akan
          diperbarui. Ini sangat penting untuk mencegah pembaruan data yang salah.
    */


    /*
    |--------------------------------------------------------------------------
    | Operasi Delete (Menghapus Data)
    |--------------------------------------------------------------------------
    */
    
    // Menghapus data kategori dari database berdasarkan ID.
    public function deleteCategory($id) {
        $this->db->query('DELETE FROM room_categories WHERE category_id = :category_id');
        $this->db->bind(':category_id', $id);
        return $this->db->execute();
    }
    /*
        Query: DELETE FROM room_categories WHERE category_id = :category_id
        - DELETE FROM room_categories: Perintah untuk menghapus baris dari tabel `room_categories`.
        - WHERE category_id = :category_id: Klausa filter untuk menentukan baris mana yang akan dihapus. Tanpa klausa
          WHERE, semua data di tabel akan terhapus.
    */


    /*
    |--------------------------------------------------------------------------
    | Fungsi Relasional & Pembantu (Helper & Relational Functions)
    |--------------------------------------------------------------------------
    */

    // Mengambil semua kamar yang termasuk dalam kategori tertentu.
    public function getRoomsByCategory($categoryId) {
        $this->db->query('SELECT * FROM rooms WHERE category_id = :category_id');
        $this->db->bind(':category_id', $categoryId);
        return $this->db->resultSet();
    }
    /*
        Query: SELECT * FROM rooms WHERE category_id = :category_id
        - SELECT *: Mengambil semua kolom.
        - FROM rooms: Mengambil data dari tabel `rooms`.
        - WHERE category_id = :category_id: Klausa filter untuk hanya menampilkan kamar yang memiliki `category_id`
          yang sesuai.
    */

    // Menghitung jumlah kamar yang termasuk dalam kategori tertentu secara efisien.
    public function getRoomCountByCategory($categoryId) {
        $this->db->query('SELECT COUNT(*) as count FROM rooms WHERE category_id = :category_id');
        $this->db->bind(':category_id', $categoryId);
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }
    /*
        Query: SELECT COUNT(*) as count FROM rooms WHERE category_id = :category_id
        - SELECT COUNT(*) as count: Perintah untuk menghitung semua baris (`*`) yang cocok dengan kriteria dan
          memberi nama kolom hasil hitungan sebagai `count`. Ini jauh lebih efisien daripada mengambil semua data
          dan menghitungnya di PHP.
        - FROM rooms: Menghitung dari tabel `rooms`.
        - WHERE category_id = :category_id: Kriteria filter untuk hanya menghitung kamar dalam kategori yang spesifik.
    */
}