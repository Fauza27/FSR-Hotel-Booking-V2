<?php

class HomeController {
    private $roomModel;
    
    public function __construct() {
        $this->roomModel = new Room();
    }
    
    // Home page
    public function index() {
        // Get featured rooms
        $featuredRooms = $this->roomModel->getAllRooms();
        
        // Limit to 3 rooms for the homepage
        $featuredRooms = array_slice($featuredRooms, 0, 6);
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'Welcome';
        $currentPage = 'home';
        
        // Load view
        require_once(VIEW_PATH . 'home/index.php');
    }
    
    // About page
    public function about() {
        // Set page title and current page for menu highlighting
        $pageTitle = 'About Us';
        $currentPage = 'about';
        
        // Load view
        require_once(VIEW_PATH . 'home/about.php');
    }
    
    // Contact page
    public function contact() {
        // Check if form is submitted
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get form data
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $subject = trim($_POST['subject']);
            $message = trim($_POST['message']);
            
            // Simple validation
            if(empty($name) || empty($email) || empty($subject) || empty($message)) {
                $_SESSION['flash_message'] = 'Please fill in all fields';
                $_SESSION['flash_type'] = 'danger';
            } else {
                // In a real application, you would send an email here
                // For now, we'll just show a success message
                
                $_SESSION['flash_message'] = 'Thank you for your message. We will get back to you soon!';
                $_SESSION['flash_type'] = 'success';
                
                // Redirect to avoid form resubmission on page refresh
                header('Location: ' . APP_URL . '/contact');
                exit;
            }
        }
        
        // Set page title and current page for menu highlighting
        $pageTitle = 'Contact Us';
        $currentPage = 'contact';
        
        // Load view
        require_once(VIEW_PATH . 'home/contact.php');
    }
    
    // 404 Not Found page
    public function notFound() {
        // Set page title
        $pageTitle = 'Page Not Found';
        
        // Set HTTP response code to 404
        http_response_code(404);
        
        // Load view
        require_once(VIEW_PATH . 'shared/error.php');
    }
    
    // Error page
    public function error($message = 'An error occurred') {
        // Set page title
        $pageTitle = 'Error';
        
        // Set error message
        $errorMessage = $message;
        
        // Load view
        require_once(VIEW_PATH . 'shared/error.php');
    }
}