<?php
// AdminRoomController

class AdminRoomController {
    // Mendeklarasikan properti untuk model Room
    private $roomModel;
    
    // Konstruktor yang dijalankan saat objek controller ini dibuat
    public function __construct() {
        // Memuat model Room
        require_once __DIR__ . '/../models/Room.php';
        // Membuat instance dari model Room
        $this->roomModel = new Room();
    }
    
    // Menampilkan daftar semua kamar dengan pagination
    public function index() {
        $limit = 10; // Menentukan jumlah kamar yang ditampilkan per halaman
        // Mengatur halaman default ke 1 jika parameter 'page' tidak ada atau tidak valid
        $page = 1;
        // (mengecek apakah parameter page ada dalam URL  && apakah nilai parameter tersebut merupakan angka yang valid && apakah nilainya lebih besar dari 0)
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0){
            $page = (int)$_GET['page']; // Mengambil nomor halaman dari URL jika valid dan diubah menjadi integer
        }
        //Kegunaan: Menyaring dan memastikan bahwa halaman yang diminta valid dan tidak ada input yang salah. Jika parameter page tidak ada atau tidak valid, halaman pertama (1) akan digunakan sebagai default.

        $offset = ($page - 1) * $limit; // Menghitung offset berdasarkan halaman saat ini
        
        // Menyimpan pencarian dan kategori dari parameter GET (jika ada)
        $search = $_GET['search'] ?? ''; //berarti jika parameter search ada di URL, nilainya akan disalin ke variabel $search; jika tidak ada, maka $search di-set ke string kosong ('').
        $category = $_GET['category'] ?? null; //berarti jika parameter category ada di URL, nilainya akan disalin ke variabel $category; jika tidak ada, maka $category di-set ke null.
        
        // Mengambil semua kategori untuk filter dropdown
        $categories = $this->roomModel->getAllCategories();
        
        // Mendapatkan kamar berdasarkan filter dan paginasi
        $rooms = $this->roomModel->getRoomsPaginated($limit, $offset, $search, $category);
        // $limit untuk membatasi jumlah kamar yang diambil,
        // $offset untuk menentukan dari kamar ke berapa data akan diambil,
        // $search untuk memfilter kamar berdasarkan kata kunci pencarian (nomor kamar, deskripsi, atau kategori),
        // $category untuk memfilter kamar berdasarkan kategori yang dipilih.
        
        // Mendapatkan total jumlah kamar dengan filter yang sama untuk pagination
        $totalRooms = $this->roomModel->getTotalRooms($search, $category);
        // Menghitung jumlah halaman berdasarkan total kamar dan batas per halaman
        $totalPages = ceil($totalRooms / $limit);
        // Fungsi ceil() digunakan untuk membulatkan hasil pembagian totalRooms / limit ke atas, karena jika ada sisa (misalnya, 25 kamar dengan limit 10 per halaman), maka jumlah halaman akan dibulatkan menjadi 3 halaman (bukannya 2 halaman).
        //Variabel $totalPages akan digunakan untuk menampilkan tombol navigasi pagination di halaman admin. Ini memberi tahu berapa banyak halaman yang perlu ditampilkan untuk menavigasi melalui daftar kamar.


        // Memuat tampilan untuk menampilkan daftar kamar
        require __DIR__ . '/../views/admin/rooms/index.php';
    }

    // Menampilkan form untuk menambah kamar baru
    public function create() {
        // Mengambil semua kategori dan fasilitas untuk ditampilkan di form tambah kamar
        $categories = $this->roomModel->getAllCategories();
        $facilities = $this->roomModel->getAllFacilities();
        // Memuat tampilan form tambah kamar
        require __DIR__ . '/../views/admin/rooms/create.php';
    }

    // Proses penyimpanan kamar baru ke database
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            $room_number = trim($_POST['room_number'] ?? '');
            $category_id = $_POST['category_id'] ?? '';
            $price_per_night = $_POST['price_per_night'] ?? '';
            $capacity = $_POST['capacity'] ?? '';
            $size_sqm = $_POST['size_sqm'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'available';
            $selectedFacilities = $_POST['facilities'] ?? [];

            // Validasi input form
            if (empty($room_number)) $errors[] = 'Nomor kamar wajib diisi';
            if (empty($category_id)) $errors[] = 'Kategori wajib dipilih';
            if (!is_numeric($price_per_night) || $price_per_night <= 0) $errors[] = 'Harga tidak valid atau harus lebih besar dari 0';
            if (!is_numeric($capacity) || $capacity <= 0) $errors[] = 'Kapasitas tidak valid atau harus lebih besar dari 0';
            if (empty($size_sqm) || !is_numeric($size_sqm) || $size_sqm <= 0) $errors[] = 'Ukuran tidak valid atau harus lebih besar dari 0'; // Ukuran bisa desimal
            if (empty($description)) $errors[] = 'Deskripsi wajib diisi.';
            if (empty($selectedFacilities)) $errors[] = 'Pilih minimal satu fasilitas.';

            // Validasi gambar
            $uploadedImagesData = []; // Ganti nama variabel agar lebih jelas
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $imageCount = count($_FILES['images']['name']);
                for ($i = 0; $i < $imageCount; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileName = $_FILES['images']['name'][$i];
                        $fileTmpName = $_FILES['images']['tmp_name'][$i];
                        $fileSize = $_FILES['images']['size'][$i];
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
                            $errors[] = "Ekstensi file '" . htmlspecialchars($fileName) . "' tidak diizinkan. Hanya: " . implode(', ', ALLOWED_EXTENSIONS);
                        }
                        if ($fileSize > MAX_FILE_SIZE) {
                            $errors[] = "Ukuran file '" . htmlspecialchars($fileName) . "' terlalu besar. Maksimum: " . (MAX_FILE_SIZE / 1024 / 1024) . " MB";
                        }
                        $uploadedImagesData[] = [ // Ganti nama variabel
                            'name' => $fileName,
                            'tmp_name' => $fileTmpName,
                            'ext' => $fileExt
                        ];
                    } elseif ($_FILES['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $errors[] = 'Gagal upload gambar: ' . $_FILES['images']['name'][$i];
                    }
                }
                if (empty($uploadedImagesData) && $imageCount > 0 && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                     $errors[] = 'Minimal satu gambar harus berhasil diupload jika Anda memilih file.';
                }
            } else {
                $errors[] = 'Minimal satu gambar wajib diupload.';
            }


            if (empty($errors)) {
                $primaryImagePathForRoomTable = null; 
                $savedImagePaths = [];

                foreach ($uploadedImagesData as $index => $img) { // Ganti nama variabel
                    $uploadDir = UPLOAD_PATH; 
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0777, true)) {
                            $errors[] = "Gagal membuat direktori upload: " . $uploadDir;
                            break; 
                        }
                    }
                    $newFileName = 'room_' . time() . '_' . uniqid() . '.' . $img['ext'];
                    $targetPath = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($img['tmp_name'], $targetPath)) {
                        // Path yang disimpan di DB relatif terhadap root aplikasi, tanpa ROOT_PATH di depannya
                        // Misal: 'assets/images/rooms/namafile.jpg'
                        $dbImagePath = str_replace(ROOT_PATH . '/', '', $targetPath);
                        // Atau jika UPLOAD_PATH adalah 'assets/images/rooms/', maka:
                        // $dbImagePath = AVATAR_UPLOAD_DIR . $newFileName; // AVATAR_UPLOAD_DIR salah, harusnya bagian dari UPLOAD_PATH
                        // $dbImagePath = str_replace(ROOT_PATH . '/', '', UPLOAD_PATH) . $newFileName;
                        // Paling aman, jika UPLOAD_PATH = '.../assets/images/rooms/'
                        // maka $dbImagePath = 'assets/images/rooms/' . $newFileName;
                        // Mari kita asumsikan UPLOAD_PATH adalah path absolut, dan kita mau simpan path relatif dari web root
                        // Jika APP_URL adalah 'http://localhost/ProjectAkhirWeb' dan ROOT_PATH adalah '/var/www/html/ProjectAkhirWeb'
                        // dan UPLOAD_PATH adalah '/var/www/html/ProjectAkhirWeb/assets/images/rooms/'
                        // Maka $dbImagePath harus menjadi 'assets/images/rooms/' . $newFileName
                        
                        // Ambil path relatif dari UPLOAD_PATH setelah ROOT_PATH
                        $relativeUploadDir = str_replace(ROOT_PATH . '/', '', rtrim(UPLOAD_PATH, '/'));
                        $dbImagePath = $relativeUploadDir . '/' . $newFileName;


                        $savedImagePaths[] = [
                            'path' => $dbImagePath,
                            'is_primary' => ($index === 0) 
                        ];
                        if ($index === 0) {
                            $primaryImagePathForRoomTable = $dbImagePath;
                        }
                    } else {
                        $errors[] = 'Gagal memindahkan file gambar: ' . htmlspecialchars($img['name']);
                    }
                }
                
                if (empty($errors)) {
                    $roomData = [
                        'room_number' => $room_number,
                        'category_id' => $category_id,
                        'price_per_night' => $price_per_night,
                        'capacity' => $capacity,
                        'size_sqm' => $size_sqm,
                        'description' => $description,
                        'status' => $status,
                        'image_url' => $primaryImagePathForRoomTable // Kolom image_url di tabel rooms
                    ];

                    $newRoomId = $this->roomModel->addRoom($roomData);

                    if ($newRoomId) {
                        if (!empty($selectedFacilities)) {
                            if (!$this->roomModel->addRoomFacilities($newRoomId, $selectedFacilities)) {
                                $errors[] = 'Gagal menyimpan fasilitas kamar.';
                            }
                        }

                        foreach ($savedImagePaths as $imgPathData) {
                            if (!$this->roomModel->addRoomImage($newRoomId, $imgPathData['path'], $imgPathData['is_primary'])) {
                                $errors[] = 'Gagal menyimpan detail gambar: ' . htmlspecialchars($imgPathData['path']);
                            }
                        }
                        
                        if (!empty($errors)) {
                            $categories = $this->roomModel->getAllCategories();
                            $facilities = $this->roomModel->getAllFacilities();
                            if ($newRoomId) $this->roomModel->deleteRoom($newRoomId); 
                            require VIEW_PATH . 'admin/rooms/create.php';
                        } else {
                            $_SESSION['success'] = 'Kamar berhasil ditambahkan';
                            header('Location: ' . base_url('admin/rooms')); 
                            exit;
                        }

                    } else {
                        $errors[] = 'Gagal menambahkan kamar ke database.';
                    }
                }
            }

            if (!empty($errors)) {
                $categories = $this->roomModel->getAllCategories();
                $facilities = $this->roomModel->getAllFacilities();            
                require VIEW_PATH . 'admin/rooms/create.php'; 
            }
        } else {
            header('Location: ' . base_url('admin/rooms/create'));
            exit;
        }
    }
    
    // Menampilkan form edit kamar berdasarkan ID
    public function edit($id) {
        $room = $this->roomModel->getRoomById($id); 
        
        if (!$room) {
            $_SESSION['error'] = 'Kamar tidak ditemukan'; 
            header('Location: ' . base_url('admin/rooms')); // Gunakan base_url
            exit;
        }
        
        $categories = $this->roomModel->getAllCategories();
        $facilities = $this->roomModel->getAllFacilities();
        $roomFacilitiesObjects = $this->roomModel->getRoomFacilities($id); 
        $roomImages = $this->roomModel->getRoomImages($id); 
        
        // Ubah $roomFacilities menjadi array ID saja untuk kemudahan di view
        $roomFacilityIds = [];
        foreach ($roomFacilitiesObjects as $facility) {
            $roomFacilityIds[] = $facility->facility_id;
        }

        require __DIR__ . '/../views/admin/rooms/edit.php'; 
    }


    // Proses update data kamar
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
            $errors = []; 
            
            $room = $this->roomModel->getRoomById($id);
            if (!$room) {
                $_SESSION['error'] = 'Kamar tidak ditemukan untuk diperbarui.';
                header('Location: ' . base_url('admin/rooms'));
                exit;
            }

            $room_number = trim($_POST['room_number'] ?? '');
            $category_id = $_POST['category_id'] ?? '';
            $price_per_night = $_POST['price_per_night'] ?? '';
            $capacity = $_POST['capacity'] ?? '';
            $size_sqm = $_POST['size_sqm'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'available'; 
            $selectedFacilities = $_POST['facilities'] ?? [];

            if (empty($room_number)) $errors[] = 'Nomor kamar wajib diisi';
            if (empty($category_id)) $errors[] = 'Kategori wajib dipilih';
            if (!is_numeric($price_per_night) || $price_per_night <= 0) $errors[] = 'Harga tidak valid atau harus lebih besar dari 0';
            if (!is_numeric($capacity) || $capacity <= 0) $errors[] = 'Kapasitas tidak valid atau harus lebih besar dari 0';
            if (empty($size_sqm) || !is_numeric($size_sqm) || $size_sqm <= 0) $errors[] = 'Ukuran tidak valid atau harus lebih besar dari 0';
            if (empty($description)) $errors[] = 'Deskripsi wajib diisi.';
            if (empty($selectedFacilities)) $errors[] = 'Pilih minimal satu fasilitas.';


            // Data kamar dasar untuk diupdate
            $roomData = [
                'room_number' => $room_number,
                'category_id' => $category_id,
                'price_per_night' => $price_per_night,
                'capacity' => $capacity,
                'size_sqm' => $size_sqm,
                'description' => $description,
                'status' => $status,
                // image_url akan diupdate jika ada gambar utama baru
            ];

            // Proses upload gambar baru jika ada
            // Di sini kita tidak akan otomatis mengganti image_url di tabel rooms kecuali gambar utama diupload/diganti
            // Jika Anda ingin menghapus gambar lama yang tidak ada di form lagi, itu butuh logika tambahan
            $newlyUploadedPrimaryImage = null;

            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $imageCount = count($_FILES['images']['name']);
                $existingImages = $this->roomModel->getRoomImages($id);
                $isPrimaryAlreadySet = false;
                foreach($existingImages as $exImg) {
                    if ($exImg->is_primary) {
                        $isPrimaryAlreadySet = true;
                        break;
                    }
                }

                for ($i = 0; $i < $imageCount; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileName = $_FILES['images']['name'][$i];
                        $fileTmpName = $_FILES['images']['tmp_name'][$i];
                        $fileSize = $_FILES['images']['size'][$i];
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                        if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
                            $errors[] = "Ekstensi file '" . htmlspecialchars($fileName) . "' tidak diizinkan. Hanya: " . implode(', ', ALLOWED_EXTENSIONS);
                            continue;
                        }
                        if ($fileSize > MAX_FILE_SIZE) {
                            $errors[] = "Ukuran file '" . htmlspecialchars($fileName) . "' terlalu besar. Maksimum: " . (MAX_FILE_SIZE / 1024 / 1024) . " MB";
                            continue;
                        }

                        $uploadDir = UPLOAD_PATH; 
                        if (!is_dir($uploadDir)) {
                            if (!mkdir($uploadDir, 0777, true)) {
                                $errors[] = "Gagal membuat direktori upload: " . $uploadDir;
                                break; 
                            }
                        }
                        $newFileName = 'room_' . time() . '_' . uniqid() . '_' . $i . '.' . $fileExt;
                        $targetPath = $uploadDir . $newFileName;
                        
                        // Path relatif untuk DB
                        $relativeUploadDir = str_replace(ROOT_PATH . '/', '', rtrim(UPLOAD_PATH, '/'));
                        $dbImagePath = $relativeUploadDir . '/' . $newFileName;

                        if (move_uploaded_file($fileTmpName, $targetPath)) {
                            // Tentukan apakah ini gambar utama
                            // Jika ini gambar pertama yang diupload DAN belum ada gambar utama sebelumnya, jadikan primary
                            $isPrimary = ($i === 0 && !$isPrimaryAlreadySet); 
                                                        
                            if (!$this->roomModel->addRoomImage($id, $dbImagePath, $isPrimary)) {
                                $errors[] = 'Gagal menyimpan gambar ' . htmlspecialchars($fileName) . ' ke database.';
                            } else {
                                if ($isPrimary) {
                                    $newlyUploadedPrimaryImage = $dbImagePath;
                                    $isPrimaryAlreadySet = true; // Pastikan hanya satu primary baru
                                }
                            }
                        } else {
                            $errors[] = 'Gagal memindahkan file gambar: ' . htmlspecialchars($fileName);
                        }
                    } elseif ($_FILES['images']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $errors[] = 'Gagal upload gambar: ' . $_FILES['images']['name'][$i];
                    }
                }
            }
            
            // Jika ada gambar utama baru yang diupload, update kolom image_url di tabel rooms
            if ($newlyUploadedPrimaryImage) {
                $roomData['image_url'] = $newlyUploadedPrimaryImage;
            } elseif (empty($this->roomModel->getRoomImages($id)) && $room->image_url) {
                // Jika semua gambar dihapus (logika hapus gambar perlu ada), dan room->image_url masih ada, mungkin perlu di-null-kan
                // Untuk saat ini, jika tidak ada primary image baru, image_url lama tetap.
                // Jika tidak ada gambar sama sekali, set image_url ke null
                $currentImages = $this->roomModel->getRoomImages($id);
                $hasPrimaryImage = false;
                foreach($currentImages as $img) {
                    if($img->is_primary) {
                        $roomData['image_url'] = $img->image_url;
                        $hasPrimaryImage = true;
                        break;
                    }
                }
                if (!$hasPrimaryImage && !empty($currentImages)) { // Jika tidak ada primary, ambil gambar pertama
                     $roomData['image_url'] = $currentImages[0]->image_url;
                } elseif (empty($currentImages)) {
                     $roomData['image_url'] = null;
                }

            } else {
                // Jika tidak ada gambar baru yang diupload sebagai primary, biarkan image_url lama
                // Namun, jika user menghapus gambar utama yang lama, kita perlu update.
                // Ini butuh logika "set primary image" atau "delete image" yang lebih canggih.
                // Untuk sekarang, kita asumsikan jika ada gambar baru, yang pertama jadi primary jika belum ada.
                // Jika tidak ada gambar baru, image_url di tabel 'rooms' tidak diubah kecuali logika penghapusan gambar
                // yang ada di form edit diimplementasikan untuk mengubahnya.
                 $roomData['image_url'] = $room->image_url; // Pertahankan image_url lama jika tidak ada primary baru

                 // Cek apakah gambar utama yang lama masih ada
                $currentImages = $this->roomModel->getRoomImages($id);
                $primaryImageStillExists = false;
                if ($room->image_url) { // Jika ada image_url lama
                    foreach($currentImages as $img) {
                        if ($img->image_url == $room->image_url && $img->is_primary) {
                            $primaryImageStillExists = true;
                            break;
                        }
                    }
                }
                if (!$primaryImageStillExists) { // Jika gambar utama lama dihapus atau bukan primary lagi
                    // coba cari primary image baru dari yang ada
                    $newPrimaryFromExisting = null;
                    foreach($currentImages as $img) {
                        if ($img->is_primary) {
                            $newPrimaryFromExisting = $img->image_url;
                            break;
                        }
                    }
                    if ($newPrimaryFromExisting) {
                        $roomData['image_url'] = $newPrimaryFromExisting;
                    } elseif (!empty($currentImages)) {
                        $roomData['image_url'] = $currentImages[0]->image_url; // ambil gambar pertama jika tidak ada primary
                    } else {
                        $roomData['image_url'] = null; // tidak ada gambar sama sekali
                    }
                }
            }


            if (empty($errors)) {
                // Update data dasar kamar
                if ($this->roomModel->updateRoom($id, $roomData)) {
                    // Update fasilitas
                    if (!$this->roomModel->updateRoomFacilities($id, $selectedFacilities)) {
                        $errors[] = 'Gagal memperbarui fasilitas kamar.';
                    }

                    if (empty($errors)) {
                        $_SESSION['success'] = 'Kamar berhasil diperbarui';
                        header('Location: ' . base_url('admin/rooms')); // Perbaikan redirect
                        exit; 
                    }
                } else {
                    $errors[] = 'Gagal memperbarui data dasar kamar.'; 
                }
            }

            // Jika ada error, siapkan data lagi untuk form
            // $room (data lama) sudah ada dari awal. Jika update gagal, tampilkan data lama.
            $categories = $this->roomModel->getAllCategories();
            $facilities = $this->roomModel->getAllFacilities();
            $roomFacilitiesObjects = $this->roomModel->getRoomFacilities($id);
            $roomImages = $this->roomModel->getRoomImages($id); 
            
            $roomFacilityIds = [];
            foreach ($roomFacilitiesObjects as $facility) {
                $roomFacilityIds[] = $facility->facility_id;
            }
            require __DIR__ . '/../views/admin/rooms/edit.php'; 
        } else {
            // Jika bukan POST, redirect atau tampilkan error
            header('Location: ' . base_url('admin/rooms'));
            exit;
        }
    }
    
    // Menghapus kamar dari database
    public function delete($id) {
        $room = $this->roomModel->getRoomById($id);
        if (!$room) {
             $_SESSION['error'] = 'Kamar tidak ditemukan.';
             header('Location: ' . base_url('admin/rooms'));
             exit;
        }

        // Hapus gambar fisik sebelum menghapus dari DB
        $images = $this->roomModel->getRoomImages($id);
        foreach ($images as $img) {
            $imagePath = ROOT_PATH . '/' . $img->image_url; // Asumsi image_url adalah path relatif dari ROOT_PATH
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Hapus juga gambar utama dari tabel rooms jika pathnya beda (seharusnya sama)
        if ($room->image_url) {
            $mainImagePath = ROOT_PATH . '/' . $room->image_url;
             if (file_exists($mainImagePath) && !in_array($room->image_url, array_column($images, 'image_url'))) { // Cek jika belum dihapus oleh loop di atas
                unlink($mainImagePath);
            }
        }


        if ($this->roomModel->deleteRoom($id)) {
            $_SESSION['success'] = 'Kamar berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kamar'; 
        }
        header('Location: ' . base_url('admin/rooms')); // Perbaikan redirect
        exit;
    }
    
    public function view($id = null) {
        if (!$id || !is_numeric($id)) { // Validasi ID
            $_SESSION['error'] = 'ID kamar tidak valid';
            header('Location: ' . base_url('admin/rooms')); // Gunakan base_url
            exit;
        }
        
        $room = $this->roomModel->getRoomById($id);
        if (!$room) {
            $_SESSION['error'] = 'Kamar tidak ditemukan';
            header('Location: ' . base_url('admin/rooms')); // Gunakan base_url
            exit;
        }
        
        $roomFacilities = $this->roomModel->getRoomFacilities($id);
        $roomImages = $this->roomModel->getRoomImages($id);
        $roomBookings = $this->roomModel->getRoomBookings($id);
        
        require __DIR__ . '/../views/admin/rooms/view.php';
    }

    // Tambahkan fungsi untuk menghapus gambar individual
    public function deleteImage($imageId) {
        // Validasi: Pastikan user adalah admin dan punya hak
        // ... (Tambahkan pemeriksaan sesi admin di sini) ...

        $image = $this->roomModel->getRoomImageById($imageId); // Perlu method ini di Room model
        if ($image) {
            $roomId = $image->room_id; // Simpan room_id untuk redirect

            // Hapus file fisik
            $filePath = ROOT_PATH . '/' . $image->image_url; // Asumsi image_url disimpan relatif dari root aplikasi
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Hapus dari database
            if ($this->roomModel->deleteRoomImage($imageId)) { // Perlu method ini di Room model
                $_SESSION['success'] = 'Gambar berhasil dihapus.';

                // Jika gambar yang dihapus adalah primary, update image_url di tabel rooms
                $room = $this->roomModel->getRoomById($roomId);
                if ($room && $room->image_url == $image->image_url) {
                    $remainingImages = $this->roomModel->getRoomImages($roomId);
                    $newPrimaryUrl = null;
                    if (!empty($remainingImages)) {
                        // Coba cari primary baru
                        foreach ($remainingImages as $remImg) {
                            if ($remImg->is_primary) {
                                $newPrimaryUrl = $remImg->image_url;
                                break;
                            }
                        }
                        // Jika tidak ada primary, ambil gambar pertama
                        if (!$newPrimaryUrl) {
                           $newPrimaryUrl = $remainingImages[0]->image_url;
                           // Jadikan gambar pertama sebagai primary jika tidak ada primary lain
                           $this->roomModel->setPrimaryImage($remainingImages[0]->image_id, $roomId);
                        }
                    }
                    $this->roomModel->updateRoomMainImage($roomId, $newPrimaryUrl); // Perlu method ini
                }

            } else {
                $_SESSION['error'] = 'Gagal menghapus gambar dari database.';
            }
            header('Location: ' . base_url('admin/rooms/edit/' . $roomId));
            exit;
        } else {
            $_SESSION['error'] = 'Gambar tidak ditemukan.';
            header('Location: ' . base_url('admin/rooms'));
            exit;
        }
    }

     // Tambahkan fungsi untuk set gambar sebagai primary
    public function setPrimaryImage($imageId, $roomId) {
        // Validasi user admin
        // ...

        if ($this->roomModel->setPrimaryImage($imageId, $roomId)) {
            // Update image_url di tabel rooms
            $image = $this->roomModel->getRoomImageById($imageId);
            if ($image) {
                $this->roomModel->updateRoomMainImage($roomId, $image->image_url);
            }
            $_SESSION['success'] = 'Gambar utama berhasil diubah.';
        } else {
            $_SESSION['error'] = 'Gagal mengubah gambar utama.';
        }
        header('Location: ' . base_url('admin/rooms/edit/' . $roomId));
        exit;
    }
}

