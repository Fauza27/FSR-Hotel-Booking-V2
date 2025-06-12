<?php

class PaymentController {
    private $paymentModel;
    private $bookingModel;
    private $roomModel;
    
    public function __construct() {
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
        $this->roomModel = new Room();
    }
    
    // Process payment page
    public function process($bookingId = null) {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['intended_url'] = APP_URL . '/payment/process/' . $bookingId;
            $_SESSION['flash_message'] = 'Please login to complete payment';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Check if booking ID is provided
        if($bookingId === null) {
            $_SESSION['flash_message'] = 'Invalid booking';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get booking details
        $booking = $this->bookingModel->getBookingById($bookingId);
        
        // Check if booking exists and belongs to the logged in user
        if(!$booking || $booking->user_id != $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'Booking not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Check if booking is already paid
        if($booking->status != 'pending') {
            $_SESSION['flash_message'] = 'This booking has already been ' . $booking->status;
            $_SESSION['flash_type'] = 'info';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get room details
        $room = $this->roomModel->getRoomById($booking->room_id);
        
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $data = [
                'booking_id' => $bookingId,
                'payment_method' => $_POST['payment_method'],
                'card_number' => isset($_POST['card_number']) ? $_POST['card_number'] : '',
                'card_holder' => isset($_POST['card_holder']) ? $_POST['card_holder'] : '',
                'expiry_month' => isset($_POST['expiry_month']) ? $_POST['expiry_month'] : '',
                'expiry_year' => isset($_POST['expiry_year']) ? $_POST['expiry_year'] : '',
                'cvv' => isset($_POST['cvv']) ? $_POST['cvv'] : '',
                'bank_reference' => isset($_POST['bank_reference']) ? $_POST['bank_reference'] : '',
                'amount' => $booking->total_price,
                'payment_status' => 'completed', // For demo, always set to completed
                'errors' => []
            ];
            
            // Validate payment method
            if(empty($data['payment_method'])) {
                $data['errors']['payment_method'] = 'Please select a payment method';
            }
            
            // Specific validation based on payment method
            // if($data['payment_method'] == 'credit_card') {
            //     // Validate credit card details
            //     if(empty($data['card_number'])) {
            //         $data['errors']['card_number'] = 'Please enter card number';
            //     } elseif(!preg_match('/^[0-9]{16}$/', $data['card_number'])) {
            //         $data['errors']['card_number'] = 'Card number must be 16 digits';
            //     }
                
            //     if(empty($data['card_holder'])) {
            //         $data['errors']['card_holder'] = 'Please enter card holder name';
            //     }
                
            //     if(empty($data['expiry_month']) || empty($data['expiry_year'])) {
            //         $data['errors']['expiry'] = 'Please enter expiry date';
            //     }
                
            //     if(empty($data['cvv'])) {
            //         $data['errors']['cvv'] = 'Please enter CVV';
            //     } elseif(!preg_match('/^[0-9]{3,4}$/', $data['cvv'])) {
            //         $data['errors']['cvv'] = 'CVV must be 3 or 4 digits';
            //     }
            // } elseif($data['payment_method'] == 'bank_transfer') {
            //     // Validate bank transfer details
            //     if(empty($data['bank_reference'])) {
            //         $data['errors']['bank_reference'] = 'Please enter bank reference number';
            //     }
            // }
            
            // If no errors, process payment
            if(empty($data['errors'])) {
                // In a real application, you would integrate with a payment gateway here
                // For demo purposes, we'll just create a payment record and update booking status
                
                // Generate a mock transaction ID
                $transactionId = 'TXN' . time() . rand(1000, 9999);
                
                // Create payment record
                $paymentId = $this->paymentModel->createPayment([
                    'booking_id' => $data['booking_id'],
                    'amount' => $data['amount'],
                    'payment_method' => $data['payment_method'],
                    'payment_status' => $data['payment_status'],
                    'transaction_id' => $transactionId
                ]);
                
                if($paymentId) {
                    // Update booking status to confirmed
                    $this->bookingModel->updateBookingStatus($data['booking_id'], 'confirmed');
                    
                    $_SESSION['flash_message'] = 'Payment successful! Your booking has been confirmed.';
                    $_SESSION['flash_type'] = 'success';
                    
                    header('Location: ' . APP_URL . '/payment/success/' . $paymentId);
                    exit;
                } else {
                    $_SESSION['flash_message'] = 'Payment failed. Please try again.';
                    $_SESSION['flash_type'] = 'danger';
                }
            }
            
            // If we get here, there were errors or payment failed
            $this->loadView($data, $booking, $room);
        } else {
            // Initialize data array
            $data = [
                'booking_id' => $bookingId,
                'payment_method' => '',
                'card_number' => '',
                'card_holder' => '',
                'expiry_month' => '',
                'expiry_year' => '',
                'cvv' => '',
                'bank_reference' => '',
                'amount' => $booking->total_price,
                'payment_status' => '',
                'errors' => []
            ];
            
            // Load view
            $this->loadView($data, $booking, $room);
        }
    }
    
    // Payment success page
    public function success($paymentId = null) {
        // Check if user is logged in
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['flash_message'] = 'Please login to view payment details';
            $_SESSION['flash_type'] = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        // Check if payment ID is provided
        if($paymentId === null) {
            $_SESSION['flash_message'] = 'Invalid payment';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get payment details
        $payment = $this->paymentModel->getPaymentById($paymentId);
        
        // Check if payment exists
        if(!$payment) {
            $_SESSION['flash_message'] = 'Payment not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get booking details
        $booking = $this->bookingModel->getBookingById($payment->booking_id);
        
        // Check if booking belongs to the logged in user
        if(!$booking || $booking->user_id != $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'Payment not found';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . APP_URL . '/profile/bookings');
            exit;
        }
        
        // Get room details
        $room = $this->roomModel->getRoomById($booking->room_id);
        
        // Calculate nights
        $nights = $this->bookingModel->calculateNights($booking->check_in_date, $booking->check_out_date);
        
        // Set page title
        $pageTitle = 'Payment Success';
        
        // Load view
        require_once(VIEW_PATH . 'payment/success.php');
    }
    
    // Payment failed page
    public function failed() {
        // Set page title
        $pageTitle = 'Payment Failed';
        
        // Load view
        require_once(VIEW_PATH . 'payment/failed.php');
    }
    
    // Load payment process view
    private function loadView($data, $booking, $room) {
        // Calculate nights
        $nights = $this->bookingModel->calculateNights($booking->check_in_date, $booking->check_out_date);
        
        // Set page title
        $pageTitle = 'Payment';
        
        // Load view
        require_once(VIEW_PATH . 'payment/process.php');
    }
}