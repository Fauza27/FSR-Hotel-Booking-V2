<?php

class ReviewController {
    private $reviewModel;
    private $bookingModel;
    private $roomModel;
    
    public function __construct() {
        $this->reviewModel = new Review();
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
    }
    
    // Create review
    public function create() {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Please login to submit a review';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'booking_id' => $_POST['booking_id'],
                'user_id' => $_SESSION['user_id'],
                'rating' => $_POST['rating'],
                'comment' => trim($_POST['comment']),
                'errors' => []
            ];
            
            // Validate booking ID
            if(empty($data['booking_id'])) {
                $data['errors']['booking_id'] = 'Invalid booking';
                $_SESSION['flash_message'] = 'Invalid booking';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . APP_URL . '/profile/bookings');
                exit;
            }
            
            // Get booking details
            $booking = $this->bookingModel->getBookingById($data['booking_id']);
            
            // Check if booking exists and belongs to the logged in user
            if(!$booking || $booking->user_id != $_SESSION['user_id']) {
                $data['errors']['booking_id'] = 'Booking not found';
                $_SESSION['flash_message'] = 'Booking not found';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . APP_URL . '/profile/bookings');
                exit;
            }
            
            // Check if booking is completed (only allow reviews for completed bookings)
            if($booking->status != 'completed') {
                $data['errors']['booking_id'] = 'You can only review completed bookings';
                $_SESSION['flash_message'] = 'You can only review completed bookings';
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . APP_URL . '/profile/bookings');
                exit;
            }
            
            // Check if review already exists
            if($this->reviewModel->checkReviewExists($data['booking_id'], $data['user_id'])) {
                $data['errors']['booking_id'] = 'You have already reviewed this booking';
                $_SESSION['flash_message'] = 'You have already reviewed this booking';
                $_SESSION['flash_type'] = 'warning';
                header('Location: ' . APP_URL . '/profile/bookings');
                exit;
            }
            
            // Validate rating
            if(empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                $data['errors']['rating'] = 'Please select a rating between 1 and 5';
            }
            
            // Validate comment
            if(empty($data['comment'])) {
                $data['errors']['comment'] = 'Please enter a comment';
            } elseif(strlen($data['comment']) < 10) {
                $data['errors']['comment'] = 'Comment must be at least 10 characters';
            }
            
            // If no errors, create review
            if(empty($data['errors'])) {
                if($this->reviewModel->createReview($data)) {
                    $_SESSION['flash_message'] = 'Thank you for your review!';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ' . APP_URL . '/profile/bookings');
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Failed to submit review. Please try again.';
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: ' . APP_URL . '/profile/bookings');
                    exit;
                }
            } else {
                // If we have errors, redirect back with error message
                $_SESSION['flash_message'] = 'Please fix the errors in your review';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . APP_URL . '/profile/bookings');
                exit;
            }
        } else {
            // If not POST request, redirect to bookings
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
    }
    
    // View reviews for a room
    public function roomReviews($roomId = null) {
        // Check if room ID is provided
        if($roomId === null) {
            $_SESSION['flash_message'] = 'Invalid room';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/rooms');
            exit;
        }
        
        // Get room details
        $room = $this->roomModel->getRoomById($roomId);
        
        // Check if room exists
        if(!$room) {
            $_SESSION['flash_message'] = 'Room not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/rooms');
            exit;
        }
        
        // Get reviews for the room
        $reviews = $this->reviewModel->getRoomReviews($roomId);
        
        // Calculate average rating
        $averageRating = 0;
        $totalReviews = count($reviews);
        
        if($totalReviews > 0) {
            $ratingSum = 0;
            foreach($reviews as $review) {
                $ratingSum += $review->rating;
            }
            $averageRating = $ratingSum / $totalReviews;
        }
        
        // Set page title
        $pageTitle = 'Reviews for ' . $room->room_number;
        
        // Load view
        require_once(VIEW_PATH . 'reviews/room_reviews.php');
    }
}