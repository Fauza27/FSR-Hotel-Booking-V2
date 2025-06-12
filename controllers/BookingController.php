<?php
// controllers/BookingController.php

class BookingController {
    private $roomModel;
    private $bookingModel;
    private $userModel;
    
    public function __construct() {
        $this->roomModel = new Room();
        $this->bookingModel = new Booking();
        $this->userModel = new User();
    }
    
    // Create booking
    public function create() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Please login to book a room';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'room_id' => $_POST['room_id'],
                'user_id' => $_SESSION['user_id'],
                'check_in_date' => $_POST['check_in'],
                'check_out_date' => $_POST['check_out'],
                'total_price' => $_POST['total_price'],
                'adults' => $_POST['adults'],
                'children' => isset($_POST['children']) ? $_POST['children'] : 0,
                'status' => 'pending',
                'identity_file' => ''
            ];
            
            // Validate room availability again
            if(!$this->roomModel->checkAvailability($data['room_id'], $data['check_in_date'], $data['check_out_date'])) {
                $_SESSION['flash_message'] = 'Sorry, this room is no longer available for the selected dates';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . APP_URL . '/room/view/' . $data['room_id']);
                exit;
            }
            
            // Handle file upload
            if(isset($_FILES['identity_file']) && $_FILES['identity_file']['error'] == 0) {
                $upload_dir = ROOT_PATH . '/uploads/identity/';
                
                // Create directory if not exists
                if(!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Get file info
                $file_name = $_FILES['identity_file']['name'];
                $file_size = $_FILES['identity_file']['size'];
                $file_tmp = $_FILES['identity_file']['tmp_name'];
                $file_type = $_FILES['identity_file']['type'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Allowed file extensions
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
                
                // Validate file extension
                if(!in_array($file_ext, $allowed_extensions)) {
                    $_SESSION['flash_message'] = 'File type not allowed. Please upload JPG, JPEG, PNG, or PDF file';
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: ' . APP_URL . '/room/view/' . $data['room_id']);
                    exit;
                }
                
                // Validate file size (max 2MB)
                if($file_size > 2097152) {
                    $_SESSION['flash_message'] = 'File size cannot be larger than 2MB';
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: ' . APP_URL . '/room/view/' . $data['room_id']);
                    exit;
                }
                
                // Generate unique filename
                $new_file_name = 'ID_' . $_SESSION['user_id'] . '_' . date('YmdHis') . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                
                // Upload file
                if(move_uploaded_file($file_tmp, $upload_path)) {
                    $data['identity_file'] = 'uploads/identity/' . $new_file_name;
                } else {
                    $_SESSION['flash_message'] = 'Failed to upload file';
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: ' . APP_URL . '/room/view/' . $data['room_id']);
                    exit;
                }
            } else {
                $_SESSION['flash_message'] = 'Identity document is required';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . APP_URL . '/room/view/' . $data['room_id']);
                exit;
            }
            
            // Create booking
            $booking_id = $this->bookingModel->createBooking($data);
            
            if($booking_id) {
                $_SESSION['flash_message'] = 'Booking created successfully';
                $_SESSION['flash_type'] = 'success';
                header('Location: ' . APP_URL . '/booking/confirm/' . $booking_id);
                exit;
            } else {
                $_SESSION['flash_message'] = 'Failed to create booking';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . APP_URL . '/room/view/' . $data['room_id']);
                exit;
            }
        } else {
            header('Location: ' . APP_URL);
            exit;
        }
    }
    
    // Confirm booking
    public function confirm($id = null) {
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Please login to view your bookings';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        if($id === null) {
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get booking details
        $booking = $this->bookingModel->getBookingById($id);
        
        // Check if booking exists and belongs to the logged in user
        if(!$booking || $booking->user_id != $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'Booking not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get room details
        $room = $this->roomModel->getRoomById($booking->room_id);
        
        // Get user details
        $user = $this->userModel->getUserById($booking->user_id);
        
        // Calculate nights
        $nights = $this->bookingModel->calculateNights($booking->check_in_date, $booking->check_out_date);
        
        // Get room images
        $roomImages = $this->roomModel->getRoomImages($booking->room_id);
        
        // Load view
        $data = [
            'booking' => $booking,
            'room' => $room,
            'user' => $user,
            'nights' => $nights,
            'roomImages' => $roomImages
        ];
        
        // Set page title
        $pageTitle = 'Booking Confirmation';
        
        require_once(VIEW_PATH . 'booking/confirmation.php');
    }
    
    // Success page after payment
    public function success($id = null) {
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Please login to view your bookings';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        if($id === null) {
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get booking details
        $booking = $this->bookingModel->getBookingById($id);
        
        // Check if booking exists and belongs to the logged in user
        if(!$booking || $booking->user_id != $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'Booking not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Update booking status to confirmed
        $this->bookingModel->updateBookingStatus($id, 'confirmed');
        
        // Get room details
        $room = $this->roomModel->getRoomById($booking->room_id);
        
        // Load view
        $data = [
            'booking' => $booking,
            'room' => $room
        ];
        
        // Set page title
        $pageTitle = 'Booking Success';
        
        require_once(VIEW_PATH . 'booking/success.php');
    }
    
     // Cancel booking
    public function cancel($id = null) {
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/profile/bookings'; // Arahkan ke histori jika coba cancel tanpa login
            $_SESSION['flash_message'] = 'Please login to manage your bookings.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        if($id === null) {
            $_SESSION['flash_message'] = 'Booking ID not provided.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        $booking = $this->bookingModel->getBookingById($id);
        
        if(!$booking || $booking->user_id != $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'Booking not found or you do not have permission to cancel it.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // PERUBAHAN UTAMA: Hanya izinkan cancel jika status 'pending'
        if($booking->status !== 'pending') {
            $_SESSION['flash_message'] = 'This booking cannot be cancelled as it is not in a pending state.';
            $_SESSION['flash_type'] = 'warning'; // Gunakan warning karena ini batasan bisnis, bukan error sistem
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Jika status adalah 'pending', lanjutkan dengan pembatalan
        if($this->bookingModel->updateBookingStatus($id, 'cancelled')) {
            // updateBookingStatus di model akan menghandle perubahan status kamar menjadi 'available'
            $_SESSION['flash_message'] = 'Booking cancelled successfully.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Failed to cancel booking. Please try again or contact support.';
            $_SESSION['flash_type'] = 'danger';
        }
        
        header('Location: ' . APP_URL . '/profile/bookings');
        exit;
    }

    public function details($id = null) {
        // Pastikan user sudah login
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Please login to view booking details.';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        if($id === null) {
            $_SESSION['flash_message'] = 'Booking ID not provided.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Ambil detail booking dari model
        $booking = $this->bookingModel->getBookingById($id);
        
        // Cek apakah booking ada dan milik user yang login
        if(!$booking || $booking->user_id != $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'Booking not found or you do not have permission to view it.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Ambil detail kamar
        $room = $this->roomModel->getRoomById($booking->room_id);
        
        // Ambil gambar kamar
        $roomImages = $this->roomModel->getRoomImages($booking->room_id); // Ambil semua gambar, atau hanya yang utama jika ada logika tersebut

        // Ambil detail user (mungkin tidak terlalu perlu jika sudah ada di $booking, tapi bisa untuk kelengkapan)
        $user = $this->userModel->getUserById($booking->user_id);

        // Hitung jumlah malam
        $nights = $this->bookingModel->calculateNights($booking->check_in_date, $booking->check_out_date);
        
        // Siapkan data untuk view
        $data = [
            'booking' => $booking,
            'room' => $room,
            'roomImages' => $roomImages, // Kirim gambar kamar ke view
            'user' => $user,
            'nights' => $nights,
            'pageTitle' => 'Booking Details #' . $booking->booking_id,
            'currentPage' => 'profile' // Untuk menandai menu aktif jika diperlukan
        ];
        
        // Muat view
        require_once(VIEW_PATH . 'booking/details.php');
    }
}