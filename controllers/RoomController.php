<?php
class RoomController {
    private $roomModel;
    private $reviewModel;
    
    public function __construct() {
        $this->roomModel = new Room();
        $this->reviewModel = new Review();
    }
    
    // List all rooms
    public function index() {
        // Get search parameters
        $checkIn = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
        $checkOut = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
        $adults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
        $children = isset($_GET['children']) ? intval($_GET['children']) : 0;
        $categoryId = isset($_GET['category']) ? intval($_GET['category']) : 0;
        
        // Total guests
        $guests = $adults + $children;
        
        // Get all room categories
        $categories = $this->roomModel->getAllCategories();
        
        // Filter rooms based on search criteria
        if($checkIn && $checkOut && $guests > 0) {
            // Search available rooms
            $rooms = $this->roomModel->searchAvailableRooms($checkIn, $checkOut, $guests);
            
            // If category is selected, filter by category
            if($categoryId > 0) {
                $rooms = array_filter($rooms, function($room) use ($categoryId) {
                    return $room->category_id == $categoryId;
                });
            }
        } else {
            // No search criteria or invalid, get all rooms
            if($categoryId > 0) {
                $rooms = $this->roomModel->getRoomsByCategory($categoryId);
            } else {
                $rooms = $this->roomModel->getAllRooms();
            }
        }
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'Our Rooms';
        $currentPage = 'rooms';
        
        // Load view
        require_once(VIEW_PATH . 'rooms/list.php');
    }
    
    // View room details
    public function view($id = null) {
        if($id === null) {
            $_SESSION['flash_message'] = 'Invalid room ID.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/rooms');
            exit;
        }
        
        $room = $this->roomModel->getRoomById($id);
        
        if(!$room) {
            $_SESSION['flash_message'] = 'Room not found.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/rooms'); // Atau ke halaman 404
            exit;
        }
        
        $roomFacilities = $this->roomModel->getRoomFacilities($id);
        $roomImages = $this->roomModel->getRoomImages($id);
        $similarRooms = $this->roomModel->getRoomsByCategory($room->category_id);
        
        // Get reviews for the room
        $reviews = $this->reviewModel->getRoomReviews($id); // Panggil dari ReviewModel
        
        // Calculate average rating
        $averageRating = 0;
        $totalReviews = count($reviews);
        
        if($totalReviews > 0) {
            $ratingSum = 0;
            foreach($reviews as $review) {
                $ratingSum += $review->rating;
            }
            $averageRating = round($ratingSum / $totalReviews, 1);
        }

        // Check if the current logged-in user can review this room
        // User bisa review jika:
        // 1. Login
        // 2. Pernah booking kamar ini (room_id)
        // 3. Status bookingnya 'completed'
        // 4. Belum pernah mereview booking tersebut
        $bookingsEligibleForReview = [];
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $bookingsEligibleForReview = $this->reviewModel->getCompletedBookingsForReview($userId, $id);
        }
        
        $pageTitle = $room->room_number . ' - ' . $room->category_name;
        $currentPage = 'rooms';
        
        require_once(VIEW_PATH . 'rooms/details.php');
    }
    
    // Check room availability
    public function checkAvailability() {
        // This method is used for AJAX requests to check room availability
        
        // Check if it's an AJAX request
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }
        
        // Get request data
        $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
        $checkIn = isset($_POST['check_in']) ? $_POST['check_in'] : '';
        $checkOut = isset($_POST['check_out']) ? $_POST['check_out'] : '';
        
        // Validate data
        if($roomId <= 0 || empty($checkIn) || empty($checkOut)) {
            echo json_encode(['error' => 'Invalid data']);
            exit;
        }
        
        // Check availability
        $isAvailable = $this->roomModel->checkAvailability($roomId, $checkIn, $checkOut);
        
        // Return result
        echo json_encode(['available' => $isAvailable]);
        exit;
    }
}