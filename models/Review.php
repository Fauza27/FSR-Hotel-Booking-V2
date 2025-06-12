<?php

class Review {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Membuat review baru.
     * @param array $data Data review (booking_id, user_id, room_id, rating, comment)
     * @return bool True jika berhasil, false jika gagal.
     */
    
    // Create review
    public function createReview($data) {
        $this->db->query("
            INSERT INTO reviews (booking_id, user_id, rating, comment)
            VALUES (:booking_id, :user_id, :rating, :comment)
        ");
        
        // Bind values
        $this->db->bind(':booking_id', $data['booking_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Check if review exists
    public function checkReviewExists($bookingId, $userId) {
        $this->db->query("
            SELECT COUNT(*) as count FROM reviews
            WHERE booking_id = :booking_id AND user_id = :user_id
        ");
        
        // Bind values
        $this->db->bind(':booking_id', $bookingId);
        $this->db->bind(':user_id', $userId);
        
        // Execute and get result
        $row = $this->db->single();
        
        return $row->count > 0;
    }
    
    // Get review by ID
    public function getReviewById($reviewId) {
        $this->db->query("
            SELECT r.*, u.full_name as user_name, b.room_id
            FROM reviews r
            JOIN users u ON r.user_id = u.user_id
            JOIN bookings b ON r.booking_id = b.booking_id
            WHERE r.review_id = :review_id
        ");
        
        $this->db->bind(':review_id', $reviewId);
        
        return $this->db->single();
    }
    
    // Get reviews by user ID
    public function getUserReviews($userId) {
        $this->db->query("
            SELECT r.*, b.room_id, rm.room_number, rm.image_url
            FROM reviews r
            JOIN bookings b ON r.booking_id = b.booking_id
            JOIN rooms rm ON b.room_id = rm.room_id
            WHERE r.user_id = :user_id
            ORDER BY r.created_at DESC
        ");
        
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Mendapatkan semua review untuk kamar tertentu.
     * @param int $roomId ID kamar.
     * @return array Daftar review.
     */
    // Get reviews by room ID
    public function getRoomReviews($roomId) {
        $this->db->query("
            SELECT r.*, u.full_name as user_name, u.email as user_email
            FROM reviews r
            JOIN bookings b ON r.booking_id = b.booking_id
            JOIN users u ON r.user_id = u.user_id
            WHERE b.room_id = :room_id
            ORDER BY r.created_at DESC
        ");
        
        $this->db->bind(':room_id', $roomId);
        
        return $this->db->resultSet();
    }
    
    // Get count of reviews by user ID
    public function getUserReviewsCount($userId) {
        $this->db->query("
            SELECT COUNT(*) as count
            FROM reviews
            WHERE user_id = :user_id
        ");
        
        $this->db->bind(':user_id', $userId);
        
        $row = $this->db->single();
        
        return $row->count;
    }
    
    // Get average rating for a room
    public function getAverageRatingForRoom($roomId) {
        $this->db->query("
            SELECT AVG(r.rating) as avg_rating, COUNT(*) as review_count
            FROM reviews r
            JOIN bookings b ON r.booking_id = b.booking_id
            WHERE b.room_id = :room_id
        ");
        
        $this->db->bind(':room_id', $roomId);
        
        $result = $this->db->single();
        
        return [
            'avg_rating' => $result->avg_rating ? round($result->avg_rating, 1) : 0,
            'review_count' => $result->review_count
        ];
    }
    
    // Update review
    public function updateReview($reviewId, $data) {
        $this->db->query("
            UPDATE reviews
            SET rating = :rating,
                comment = :comment,
                updated_at = CURRENT_TIMESTAMP
            WHERE review_id = :review_id AND user_id = :user_id
        ");
        
        // Bind values
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':review_id', $reviewId);
        $this->db->bind(':user_id', $data['user_id']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Delete review
    public function deleteReview($reviewId, $userId) {
        $this->db->query("
            DELETE FROM reviews
            WHERE review_id = :review_id AND user_id = :user_id
        ");
        
        // Bind values
        $this->db->bind(':review_id', $reviewId);
        $this->db->bind(':user_id', $userId);
        
        // Execute
        return $this->db->execute();
    }

    /**
     * Memeriksa apakah user sudah pernah mereview booking tertentu.
     * @param int $bookingId ID booking.
     * @param int $userId ID user.
     * @return bool True jika sudah, false jika belum.
     */
    public function hasUserReviewedBooking($bookingId, $userId) {
        $this->db->query('SELECT COUNT(*) as count FROM reviews WHERE booking_id = :booking_id AND user_id = :user_id');
        $this->db->bind(':booking_id', $bookingId);
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result->count > 0;
    }

    /**
     * Mendapatkan booking yang sudah selesai oleh user untuk kamar tertentu dan belum direview.
     * @param int $userId ID user.
     * @param int $roomId ID kamar.
     * @return array Daftar booking yang bisa direview.
     */
    public function getCompletedBookingsForReview($userId, $roomId) {
        $this->db->query("
            SELECT b.booking_id, b.check_in_date, b.check_out_date
            FROM bookings b
            WHERE b.user_id = :user_id 
              AND b.room_id = :room_id 
              AND b.status = 'completed'
              AND NOT EXISTS (SELECT 1 FROM reviews r WHERE r.booking_id = b.booking_id AND r.user_id = :user_id)
            ORDER BY b.check_in_date DESC
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':room_id', $roomId);
        return $this->db->resultSet();
    }

     // Mendapatkan booking yang selesai dan belum direview oleh user untuk kamar tertentu
    public function getEligibleBookingForReview($userId, $roomId) {
        $this->db->query("
            SELECT b.booking_id 
            FROM bookings b
            LEFT JOIN reviews r ON b.booking_id = r.booking_id
            WHERE b.user_id = :user_id 
              AND b.room_id = :room_id 
              AND b.status = 'completed' 
              AND r.review_id IS NULL
            ORDER BY b.check_out_date DESC
            LIMIT 1
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':room_id', $roomId);
        return $this->db->single(); // Mengembalikan satu booking yang paling baru selesai dan belum direview
    }
}