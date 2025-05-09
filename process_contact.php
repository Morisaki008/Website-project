<?php
// Set CORS headers to allow requests from localhost:5500
header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once 'includes/db_connect.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', __DIR__ . '/contact_form_errors.log');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Log incoming request data
error_log("Received request from: " . $_SERVER['HTTP_ORIGIN'] ?? 'Unknown origin');
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);

try {
    // Get POST data
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);
    
    // If json_decode failed, try $_POST
    if (json_last_error() !== JSON_ERROR_NONE) {
        $postData = $_POST;
    }
    
    // Log received data
    error_log("Received data: " . print_r($postData, true));
    
    // Get and sanitize form data
    $name = strip_tags(trim($postData['name'] ?? ''));
    $email = filter_var(trim($postData['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($postData['subject'] ?? ''));
    $message = strip_tags(trim($postData['message'] ?? ''));

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        throw new Exception('All fields are required');
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message, date_sent, is_read, is_deleted) 
                          VALUES (?, ?, ?, ?, NOW(), FALSE, FALSE)");
    
    $result = $stmt->execute([$name, $email, $subject, $message]);

    if ($result) {
        error_log("Message successfully saved from: $email");
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you! Your message has been sent successfully.'
        ]);
    } else {
        throw new Exception('Failed to save message');
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error occurred. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("Form error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>