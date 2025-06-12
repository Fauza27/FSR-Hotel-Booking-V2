<?php
// routes.php - Router untuk aplikasi
class Router {
    protected $controller = 'HomeController';//Menyimpan nama controller yang digunakan secara default (controller utama).
    protected $method = 'index'; //Menyimpan nama metode yang akan dipanggil pada controller yang sesuai. Secara default, ini adalah metode index.
    protected $params = []; //Menyimpan parameter yang diteruskan ke metode controller. Ini digunakan untuk menyimpan nilai tambahan yang ada di URL, seperti ID atau data lainnya.
    

    //metode utama untuk memproses URL yang diminta oleh pengguna dan mengarahkan ke controller dan metode yang tepat.
    public function route($url) {
        global $routes;// Mendapatkan variabel global $routes yang menyimpan daftar route yang tersedia.
        
        error_log("Processing URL: " . print_r($url, true)); // Menulis log untuk debugging dengan menampilkan URL yang diproses.
        
        // Special case for make-admin route
        if (isset($url[0], $url[1], $url[2]) && 
            $url[0] === 'admin' && 
            $url[1] === 'users' && 
            $url[2] === 'make-admin') {
            
            error_log("Handling make-admin route");  // Menulis log jika masuk ke route 'make-admin'.
            $controller = new AdminUserController(); // Menggunakan controller AdminUserController.
            $id = isset($url[3]) ? $url[3] : (isset($_GET['id']) ? $_GET['id'] : null); // Mendapatkan ID dari URL atau query string.
            // bagian diatas merupakan sintaks operasi ternary (kondisi ? nilai_jika_true : nilai_jika_false;)
            error_log("User ID for make-admin: " . $id); // Menulis log ID user.
            $controller->makeAdmin($id); // Memanggil metode makeAdmin() di controller AdminUserController.
            return;
        }
        
        $routeKey = isset($url[0]) ? $url[0] : ''; // Mendapatkan bagian pertama dari URL untuk menjadi key route.
        $routeKey = strtolower($routeKey); // Menjadikan route key menjadi lowercase.
        $fullPath = implode('/', array_map('strtolower', $url)); // Membuat path lengkap dari URL dengan menggabungkan elemen-elemen array menjadi satu string.
        
        // Try to match route patterns
        foreach ($routes as $pattern => $handler) {
            // Convert route pattern to regex
            $pattern = str_replace('/', '\/', $pattern); // Mengubah '/' menjadi '\/' untuk keperluan regex.
            $pattern = '/^' . $pattern . '$/'; // Membungkus pola dalam tanda pembatas regex untuk pencocokan yang tepat.
            
            if (preg_match($pattern, $fullPath, $matches)) { // Mencocokkan URL dengan pola route.
                $controller = $handler[0]; // Mendapatkan nama controller dari handler.
                $method = $handler[1]; // Mendapatkan nama metode dari handler.
                $this->controller = new $controller(); // Membuat instance dari controller.
                $this->method = $method; // Menetapkan metode yang akan dipanggil.
                
                // Remove full match and keep only capturing groups as parameters
                array_shift($matches);// Menghapus nilai pertama yang merupakan kecocokan penuh, yang tidak dibutuhkan.
                $this->params = $matches; // Menyimpan parameter yang tertangkap dalam URL.
                
                call_user_func_array([$this->controller, $this->method], $this->params); // Memanggil controller dan metode dengan parameter.
                return;
            }
        }
        
        // Cek rute segmen tunggal, Ini adalah mekanisme fallback jika URL hanya terdiri dari satu segmen, dan mencoba mencari kecocokan dengan rute yang lebih sederhana.
        $params = array_slice($url, 1); // Menyimpan sisa URL setelah bagian pertama (controller).
        if (isset($routes[$routeKey])) { // Memeriksa apakah route sesuai dengan yang ada dalam $routes
            $controller = $routes[$routeKey][0]; // Mendapatkan controller dari route.
            $method = $routes[$routeKey][1];// Mendapatkan metode dari route.
            $this->controller = new $controller();// Membuat instance dari controller.
            $this->method = $method;// Menetapkan metode.
            $this->params = $params;// Menetapkan parameter.
        } else {
            // Jika tidak ada di $routes, fallback ke mekanisme lama
            if(isset($url[0]) && !empty($url[0])) {
                $controller = ucfirst($url[0]) . 'Controller';
                if(file_exists(CONTROLLER_PATH . $controller . '.php')) {
                    $this->controller = $controller;
                    unset($url[0]);
                } else {
                    $this->controller = 'HomeController';
                    $this->method = 'notFound';
                }
            }
            $this->controller = new $this->controller();
            if(isset($url[1]) && !empty($url[1])) {
                if(method_exists($this->controller, $url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                } else {
                    $this->method = 'notFound';
                }
            }
            $this->params = $url ? array_values($url) : [];
        }
        
        // Cek dynamic route: admin/users/make-admin/{id}
        if (
            isset($url[0], $url[1], $url[2], $url[3]) &&
            $url[0] === 'admin' && $url[1] === 'users' && $url[2] === 'make-admin' && is_numeric($url[3])
        ) {
            $controller = 'AdminUserController';
            $method = 'makeAdmin';
            $this->controller = new $controller();
            $this->method = $method;
            $this->params = [ $url[3] ];
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        // Cek dynamic route: admin/users/block/{id}
        if (
            isset($url[0], $url[1], $url[2], $url[3]) &&
            $url[0] === 'admin' && $url[1] === 'users' && $url[2] === 'block' && is_numeric($url[3])
        ) {
            $controller = 'AdminUserController';
            $method = 'block';
            $this->controller = new $controller();
            $this->method = $method;
            $this->params = [ $url[3] ];
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        // Cek dynamic route: admin/users/unblock/{id}
        if (
            isset($url[0], $url[1], $url[2], $url[3]) &&
            $url[0] === 'admin' && $url[1] === 'users' && $url[2] === 'unblock' && is_numeric($url[3])
        ) {
            $controller = 'AdminUserController';
            $method = 'unblock';
            $this->controller = new $controller();
            $this->method = $method;
            $this->params = [ $url[3] ];
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
}

// Definisi routes yang tersedia
$routes = [
    // Auth routes
    'login' => ['AuthController', 'login'],
    'register' => ['AuthController', 'register'],
    'logout' => ['AuthController', 'logout'],
    'forgot-password' => ['AuthController', 'forgotPassword'],
    'reset-password' => ['AuthController', 'resetPassword'],
    
    // Home routes
    '' => ['HomeController', 'index'],
    'home' => ['HomeController', 'index'],
    'about' => ['HomeController', 'about'],
    'contact' => ['HomeController', 'contact'],
    
    // Room routes
    'rooms' => ['RoomController', 'index'],
    'room/view' => ['RoomController', 'view'],
    
    // Booking routes
    'booking/create' => ['BookingController', 'create'],
    'booking/confirm/(\d+)' => ['BookingController', 'confirm'],
    'booking/success/(\d+)' => ['BookingController', 'success'],
    'booking/cancel/(\d+)' => ['BookingController', 'cancel'],
    'booking/details/(\d+)' => ['BookingController', 'details'],
    
    // Payment routes
    'payment/process' => ['PaymentController', 'process'],
    'payment/success' => ['PaymentController', 'success'],
    'payment/failed' => ['PaymentController', 'failed'],
    
    // User profile routes
    'profile' => ['ProfileController', 'index'],
    'profile/edit' => ['ProfileController', 'edit'],
    'profile/bookings' => ['ProfileController', 'bookings'],
    
    // Reviews
    'review/create' => ['ReviewController', 'create'],
    
    // Error routes
    '404' => ['HomeController', 'notFound'],

    // Admin routes
    'admin' => ['AdminController', 'index'],
    'admin/login' => ['AdminController', 'login'],
    'admin/logout' => ['AdminController', 'logout'],
    'admin/dashboard' => ['AdminController', 'dashboard'],    // Admin room management
    'admin/rooms' => ['AdminRoomController', 'index'],
    'admin/rooms/create' => ['AdminRoomController', 'create'],
    'admin/rooms/store' => ['AdminRoomController', 'store'],
    'admin/rooms/edit/(\d+)' => ['AdminRoomController', 'edit'],
    'admin/rooms/update/(\d+)' => ['AdminRoomController', 'update'],
    //'admin/rooms/update/([0-9]+)' => ['AdminRoomController', 'view'],
    'admin/rooms/delete/(\d+)' => ['AdminRoomController', 'delete'],
    'admin/rooms/view/(\d+)' => ['AdminRoomController', 'view'],
    'admin/rooms/delete-image/(\d+)' => ['AdminRoomController', 'deleteImage'],
    'admin/rooms/set-primary-image/(\d+)/(\d+)' => ['AdminRoomController', 'setPrimaryImage'],
    
    // Admin booking management
    'admin/bookings' => ['AdminBookingController', 'index'],
    'admin/bookings/view/(\d+)' => ['AdminBookingController', 'view'],
    'admin/bookings/update-status/(\d+)' => ['AdminBookingController', 'updateStatus'],
    'admin/bookings/cancel/(\d+)' => ['AdminBookingController', 'cancel'],
    
    // Admin user management
    'admin/users' => ['AdminUserController', 'index'],
    'admin/users/view/([0-9]+)' => ['AdminUserController', 'view'],
    'admin/users/block/([0-9]+)' => ['AdminUserController', 'block'],
    'admin/users/unblock/([0-9]+)' => ['AdminUserController', 'unblock'],
    'admin/users/make-admin' => ['AdminUserController', 'makeAdmin'],  // Support for ?id=X
    'admin/users/make-admin/([0-9]+)' => ['AdminUserController', 'makeAdmin'],  // Support for /X
    
    // Admin category management
    'admin/categories' => ['AdminCategoryController', 'index'],
    'admin/categories/create' => ['AdminCategoryController', 'create'],
    'admin/categories/store' => ['AdminCategoryController', 'store'],
    'admin/categories/edit/(\d+)' => ['AdminCategoryController', 'edit'],
    'admin/categories/update/(\d+)' => ['AdminCategoryController', 'update'],
    'admin/categories/delete/(\d+)' => ['AdminCategoryController', 'delete'],

    // Admin facility management
    'admin/facilities' => ['AdminFacilityController', 'index'],
    'admin/facilities/create' => ['AdminFacilityController', 'create'],
    'admin/facilities/store' => ['AdminFacilityController', 'store'],
    'admin/facilities/edit/(\d+)' => ['AdminFacilityController', 'edit'],
    'admin/facilities/update/(\d+)' => ['AdminFacilityController', 'update'],
    'admin/facilities/delete/(\d+)' => ['AdminFacilityController', 'delete'],

    // Admin payment management
    'admin/payments' => ['AdminPaymentController', 'index'],
    'admin/payments/view/(\d+)' => ['AdminPaymentController', 'view'],
    'admin/payments/updatestatus/(\d+)' => ['AdminPaymentController', 'updatestatus'],

    // Admin reports
    'admin/reports' => ['AdminReportController', 'index'],
    'admin/reports/bookings' => ['AdminReportController', 'bookings'],
    'admin/reports/revenue' => ['AdminReportController', 'revenue'],
    'admin/reports/rooms' => ['AdminReportController', 'rooms'],
    'admin/reports/export/(\w+)/(\w+)' => ['AdminReportController', 'export'],
];