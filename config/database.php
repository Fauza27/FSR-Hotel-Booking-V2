<?php
// Mendefinisikan kelas Database untuk menangani koneksi ke database menggunakan PDO
class Database {
    // Mendeklarasikan properti privat untuk menyimpan informasi koneksi
    private $host = DB_HOST;   // Host untuk database
    private $user = DB_USER;   // Username untuk koneksi ke database
    private $pass = DB_PASS;   // Password untuk koneksi ke database
    private $dbname = DB_NAME; // Nama database yang akan digunakan
    private $port = DB_PORT;   // Port untuk koneksi database (misalnya 3306 untuk MySQL)

    // Properti privat untuk menangani koneksi dan error
    private $conn; //Properti ini digunakan untuk menyimpan koneksi yang telah dibuat oleh objek PDO (PHP Data Objects) ke database.
    private $error; //Properti ini digunakan untuk menyimpan pesan kesalahan jika terjadi error saat membuat koneksi atau menjalankan query.
    private $stmt; //Properti ini digunakan untuk menyimpan objek statement yang dipersiapkan (prepared statement) oleh PDO. Dengan menggunakan prepared statements, Anda dapat menghindari serangan SQL Injection dan meningkatkan efisiensi query yang sering dijalankan dengan parameter yang berbeda-beda.

    // Konstruktor untuk menginisialisasi koneksi saat objek dibuat
    public function __construct() {
        // Membuat string DSN (Data Source Name) yang berisi informasi untuk menghubungkan ke database
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';port=' . $this->port;

        // Mengatur opsi PDO (PHP Data Objects) untuk koneksi ke database
        $options = array(
            PDO::ATTR_PERSISTENT => true,   // Menggunakan koneksi persisten (koneksi yang dipertahankan), Koneksi Persisten adalah mekanisme yang digunakan untuk menjaga koneksi database tetap terbuka setelah skrip PHP selesai dieksekusi,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Mengatur mode error menjadi pengecualian
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ // Mode Pengambilan Data (Fetch Mode) mengatur bagaimana data yang diambil dari database akan disajikan ke dalam bentuk objek PHP.
        );

        // Mencoba untuk membuat objek PDO yang akan menghubungkan ke database
        try {
            // Membuat instance PDO untuk koneksi ke database
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            // Menangkap exception jika ada kesalahan koneksi dan menyimpan pesan error
            $this->error = $e->getMessage();
            // Menampilkan pesan error jika terjadi kegagalan koneksi
            echo 'Connection Error: ' . $this->error;
        }
    }

    // Fungsi untuk menyiapkan query SQL
    public function query($sql) {
        // Menyiapkan statement untuk eksekusi
        $this->stmt = $this->conn->prepare($sql); //prepare() adalah metode dari objek PDO yang digunakan untuk menyiapkan query SQL untuk dieksekusi. Metode ini akan mengirimkan query ke server database, yang kemudian dapat dieksekusi dengan cara yang lebih aman (dengan parameterisasi atau binding).
            //$stmt adalah singkatan dari statement. Variabel ini digunakan untuk menyimpan objek prepared statement yang dihasilkan oleh metode prepare() dari objek PDO (dalam hal ini $this->conn, yang merupakan koneksi PDO ke database).
    }
    //Tujuan: Fungsi ini bertugas untuk mempersiapkan query SQL yang diberikan sebagai parameter $sql. Dengan kata lain, fungsi ini akan mempersiapkan sebuah prepared statement, yang nantinya akan dieksekusi.
    // Parameter:
    // $sql: Parameter yang diterima oleh fungsi ini adalah sebuah string yang berisi query SQL yang ingin dijalankan (misalnya, SELECT, INSERT, UPDATE, atau DELETE).
    // Fungsi untuk mengikat nilai pada parameter di query SQL

    public function bind($param, $value, $type = null) {
        // Jika tipe parameter tidak diberikan, kita tentukan tipe berdasarkan jenis data value
        if(is_null($type)) {
            // Mengidentifikasi tipe data dari value untuk mengikat parameter dengan benar
            switch(true) {
                case is_int($value):
                    $type = PDO::PARAM_INT; // Tipe data integer
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL; // Tipe data boolean
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL; // Tipe data null
                    break;
                default:
                    $type = PDO::PARAM_STR; // Tipe data string jika tidak ada yang lain
            }
        }
        // Mengikat nilai parameter dengan tipe yang sesuai
        $this->stmt->bindValue($param, $value, $type);
    }
    //Fungsi ini digunakan untuk mengikat nilai (value) ke parameter dalam query SQL. Tipe parameter juga ditentukan secara otomatis berdasarkan jenis data dari value.
    //Penggunaan bindValue() di sini sangat berguna dalam menghindari SQL injection.


    // Fungsi untuk mengeksekusi query yang sudah disiapkan
    public function execute() {
        // Menjalankan query dan mengembalikan hasilnya
        return $this->stmt->execute();
    }

    // Fungsi untuk mendapatkan semua hasil query sebagai array objek
    public function resultSet() {
        // Menjalankan query dan mengembalikan hasil dalam bentuk array objek
        $this->execute();
        return $this->stmt->fetchAll();
        //fetchAll() mengambil seluruh baris hasil query dan mengembalikannya dalam bentuk array. Setiap elemen dari array adalah sebuah objek, di mana setiap kolom hasil query akan menjadi properti objek tersebut.
    }

    // Fungsi untuk mendapatkan satu hasil query sebagai objek
    public function single() {
        // Menjalankan query dan mengembalikan satu hasil saja
        $this->execute();
        return $this->stmt->fetch();
    }

    // Fungsi untuk mendapatkan jumlah baris yang terpengaruh oleh query
    public function rowCount() {
        // Mengembalikan jumlah baris yang terpengaruh oleh query
        return $this->stmt->rowCount();
    }

    // Fungsi untuk mendapatkan ID terakhir yang dimasukkan ke dalam tabel (misalnya untuk auto_increment)
    public function lastInsertId() {
        // Mengembalikan ID terakhir yang dimasukkan
        return $this->conn->lastInsertId();
    }

    // Fungsi untuk memulai transaksi database
    public function beginTransaction() {
        // Memulai transaksi untuk memastikan konsistensi data
        return $this->conn->beginTransaction();
    }
    // Metode beginTransaction() digunakan untuk memulai sebuah transaksi. Ketika transaksi dimulai, semua perubahan yang dilakukan dalam transaksi tidak akan langsung disimpan ke dalam database. Sebaliknya, perubahan-perubahan ini akan "tertunda" dan hanya akan disimpan jika transaksi selesai dengan sukses.

    // Fungsi untuk mengakhiri transaksi dan menyimpan perubahan
    public function endTransaction() {
        // Menyimpan semua perubahan yang terjadi selama transaksi
        return $this->conn->commit();
    }

    // Fungsi untuk membatalkan transaksi dan membatalkan semua perubahan
    public function cancelTransaction() {
        // Membatalkan transaksi yang sedang berjalan
        return $this->conn->rollBack();
    }
}
?>
