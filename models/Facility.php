<?php
// models/Facility.php

/**
 * Class Facility
 *
 * Bertanggung jawab untuk semua operasi database yang terkait dengan entitas 'fasilitas'.
 * Ini termasuk mengambil, membuat, memperbarui, dan menghapus data fasilitas.
 * Kelas ini berinteraksi langsung dengan tabel `facilities` dan `room_facilities` di database.
 */
class Facility {

    public $facilityId;
    public $name;
    public $icon;
    public $description;
    private $db;

    /**
     * Constructor untuk kelas Facility.
     * Menginisialisasi koneksi database.
     */
    public function __construct() {
        // Memastikan file konfigurasi database di-load untuk mendapatkan instance Database.
        // Sebaiknya, pemanggilan ini dikelola oleh autoloader atau file bootstrap utama (index.php)
        // agar tidak di-load berulang kali.
        require_once __DIR__ . '/../config/database.php';
        $this->db = new Database();
    }

    // =================================================================
    //  READ OPERATIONS (Operasi Membaca Data)
    // =================================================================

    /**
     * Mengambil semua data fasilitas dari database, diurutkan berdasarkan nama.
     * @return array Daftar objek fasilitas.
     */
    public function getAllFacilities() {
        $this->db->query('SELECT * FROM facilities ORDER BY name ASC');
        return $this->db->resultSet();
    }
    /*
        QUERY EXPLANATION:
        ------------------
        SELECT * FROM facilities
        - Mengambil semua kolom (`*`) dari tabel `facilities`.

        ORDER BY name ASC
        - Mengurutkan hasil query berdasarkan kolom `name` secara menaik (Ascending, A-Z).
        - Ini berguna untuk menampilkan daftar fasilitas secara alfabetis di halaman admin atau user,
          sehingga lebih mudah dicari.
    */

    /**
     * Mencari dan mengambil satu data fasilitas berdasarkan ID uniknya.
     * @param int $id ID dari fasilitas yang dicari.
     * @return object|false Objek fasilitas jika ditemukan, atau `false` jika tidak ada.
     */
    public function getFacilityById($id) {
        $this->db->query('SELECT * FROM facilities WHERE facility_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    /*
        QUERY EXPLANATION:
        ------------------
        SELECT * FROM facilities
        - Mengambil semua kolom dari tabel `facilities`.

        WHERE facility_id = :id
        - Klausa filter yang hanya akan mengambil baris data di mana nilai kolom `facility_id`
          sama dengan nilai yang diikat ke placeholder `:id`.
        - Penggunaan named placeholder (`:id`) adalah praktik keamanan yang sangat penting
          untuk mencegah SQL Injection.
    */

    /**
     * Mengambil daftar fasilitas yang dimiliki oleh sebuah kamar tertentu (berdasarkan room_id).
     * Berguna untuk menampilkan fasilitas apa saja yang ada di detail sebuah kamar.
     * @param int $roomId ID dari kamar.
     * @return array Daftar objek fasilitas yang terkait dengan kamar tersebut.
     */
    public function getRoomFacilities($roomId) {
        $this->db->query('
            SELECT f.*
            FROM facilities f
            JOIN room_facilities rf ON f.facility_id = rf.facility_id
            WHERE rf.room_id = :room_id
        ');
        $this->db->bind(':room_id', $roomId);
        return $this->db->resultSet();
    }
    /*
        QUERY EXPLANATION:
        ------------------
        SELECT f.*
        - Mengambil semua kolom dari tabel `facilities` yang diberi alias `f`.

        FROM facilities f
        - Menentukan tabel utama query adalah `facilities` dengan alias `f`.

        JOIN room_facilities rf ON f.facility_id = rf.facility_id
        - Melakukan INNER JOIN ke tabel `room_facilities` (sebagai tabel penghubung/pivot)
          dengan alias `rf`.
        - Penggabungan terjadi jika `facility_id` di tabel `facilities` sama dengan
          `facility_id` di tabel `room_facilities`. Ini menghubungkan fasilitas dengan relasinya ke kamar.

        WHERE rf.room_id = :room_id
        - Memfilter hasil gabungan untuk hanya menyertakan baris di mana `room_id` pada tabel
          penghubung (`room_facilities`) cocok dengan ID kamar yang diberikan.
    */


    // =================================================================
    //  CREATE OPERATION (Operasi Membuat Data)
    // =================================================================

    /**
     * Menyimpan data fasilitas baru ke dalam database.
     * @param array $data Data fasilitas yang berisi 'name', 'icon', dan 'description'.
     * @return bool `true` jika berhasil, `false` jika gagal.
     */
    public function createFacility($data) {
        $this->db->query('INSERT INTO facilities (name, icon, description) VALUES (:name, :icon, :description)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':icon', $data['icon']);
        $this->db->bind(':description', $data['description'] ?? null); // Gunakan null jika deskripsi tidak ada
        return $this->db->execute();
    }
    /*
        QUERY EXPLANATION:
        ------------------
        INSERT INTO facilities (name, icon, description)
        - Perintah untuk menyisipkan baris data baru ke dalam tabel `facilities`,
          dengan menargetkan kolom `name`, `icon`, dan `description`.

        VALUES (:name, :icon, :description)
        - Menentukan nilai yang akan disisipkan menggunakan placeholder.
        - Nilai sebenarnya akan diikat (bind) secara aman melalui metode `$this->db->bind()`,
          melindungi dari serangan SQL Injection.
    */


    // =================================================================
    //  UPDATE OPERATION (Operasi Memperbarui Data)
    // =================================================================

    /**
     * Memperbarui data fasilitas yang sudah ada di database berdasarkan ID.
     * @param int $id ID dari fasilitas yang akan diperbarui.
     * @param array $data Data baru yang berisi 'name', 'icon', dan 'description'.
     * @return bool `true` jika berhasil, `false` jika gagal.
     */
    public function updateFacility($id, $data) {
        $this->db->query('UPDATE facilities SET name = :name, icon = :icon, description = :description WHERE facility_id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':icon', $data['icon']);
        $this->db->bind(':description', $data['description'] ?? null);
        return $this->db->execute();
    }
    /*
        QUERY EXPLANATION:
        ------------------
        UPDATE facilities
        - Perintah untuk memperbarui baris data yang sudah ada di tabel `facilities`.

        SET name = :name, icon = :icon, description = :description
        - Menentukan kolom mana yang akan diperbarui dan nilai barunya (menggunakan placeholder).

        WHERE facility_id = :id
        - Klausa ini sangat penting. Ini memastikan bahwa pembaruan HANYA diterapkan pada baris
          yang memiliki `facility_id` yang cocok. Tanpa klausa WHERE, semua baris di tabel akan diperbarui.
    */


    // =================================================================
    //  DELETE OPERATION (Operasi Menghapus Data)
    // =================================================================

    /**
     * Menghapus data fasilitas dari database berdasarkan ID-nya.
     * @param int $id ID dari fasilitas yang akan dihapus.
     * @return bool `true` jika berhasil, `false` jika gagal.
     */
    public function deleteFacility($id) {
        $this->db->query('DELETE FROM facilities WHERE facility_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    /*
        QUERY EXPLANATION:
        ------------------
        DELETE FROM facilities
        - Perintah untuk menghapus baris data dari tabel `facilities`.

        WHERE facility_id = :id
        - Sama seperti pada UPDATE, klausa WHERE ini krusial untuk memastikan HANYA baris
          dengan `facility_id` yang spesifik yang akan dihapus. Ini mencegah penghapusan data
          yang tidak disengaja.
    */
}