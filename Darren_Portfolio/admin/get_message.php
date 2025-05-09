<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $message_id = intval($_GET['id']);
    $sql = "SELECT * FROM messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if ($message) {
        echo json_encode($message);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Message not found']);
    }
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Message ID not provided']);
}
$conn->close();
?>