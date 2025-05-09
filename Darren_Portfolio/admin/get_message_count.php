<?php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Get count of unread messages
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN is_read = FALSE AND is_deleted = FALSE THEN 1 ELSE 0 END) as unread,
        SUM(CASE WHEN is_deleted = TRUE THEN 1 ELSE 0 END) as deleted
    FROM messages");
    $stmt->execute();
    $counts = $stmt->fetch();

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'counts' => [
            'total' => (int)$counts['total'],
            'unread' => (int)$counts['unread'],
            'deleted' => (int)$counts['deleted']
        ]
    ]);
} catch (PDOException $e) {
    error_log("Error in get_message_count.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error occurred',
        'counts' => ['total' => 0, 'unread' => 0, 'deleted' => 0]
    ]);
}
?>