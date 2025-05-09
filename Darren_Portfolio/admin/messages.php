<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

require_once dirname(__DIR__) . '/includes/db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    
    $message_id = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';
    
    if (!$message_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
        exit();
    }

    try {
        switch ($action) {
            case 'mark_read':
                $sql = "UPDATE messages SET is_read = TRUE WHERE id = ?";
                break;
            case 'mark_unread':
                $sql = "UPDATE messages SET is_read = FALSE WHERE id = ?";
                break;
            case 'delete':
                $sql = "UPDATE messages SET is_deleted = TRUE WHERE id = ?";
                break;
            case 'restore':
                $sql = "UPDATE messages SET is_deleted = FALSE WHERE id = ?";
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
                exit();
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$message_id]);

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
        exit();
    } catch (PDOException $e) {
        error_log("Database error in messages.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        exit();
    }
}

// Get view parameter (inbox or deleted)
$show_deleted = isset($_GET['view']) && $_GET['view'] === 'deleted';

// Fetch messages
try {
    // Get unread count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE is_read = FALSE AND is_deleted = FALSE");
    $stmt->execute();
    $unread_count = $stmt->fetchColumn();

    // Fetch messages based on view
    $sql = "SELECT * FROM messages WHERE is_deleted = ? ORDER BY date_sent DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$show_deleted]);
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Database error in messages.php: " . $e->getMessage());
    die("Error: Unable to fetch messages");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6C63FF;
            --secondary-color: #4FACFE;
            --background-dark: #0F123F;
            --text-color: #ffffff;
            --text-muted: #9ca3af;
            --danger: #ff4d4d;
            --success: #2ECC71;
            --card-bg: rgba(13, 20, 69, 0.7);
            --glass-bg: rgba(255, 255, 255, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--background-dark) 0%, #1a1a4f 100%);
            min-height: 100vh;
            color: var(--text-color);
            line-height: 1.6;
            padding: 2rem;
        }

        .messages-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: var(--glass-bg);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 2rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .unread-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--glass-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-btn.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .messages-grid {
            display: grid;
            gap: 1rem;
        }

        .message-card {
            background: linear-gradient(145deg, var(--glass-bg) 0%, rgba(13, 20, 69, 0.9) 100%);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .message-card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 12px 40px rgba(108, 99, 255, 0.2);
        }

        .message-card.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-color);
            border-radius: 4px;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .sender-info h3 {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
        }

        .message-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .message-subject {
            font-size: 1.2rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 600;
            margin: 0.5rem 0;
        }

        .message-preview {
            color: var(--text-muted);
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .message-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-color);
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.15);
        }

        .action-btn.view {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
        }

        .action-btn.delete {
            color: var(--danger);
        }

        .action-btn.restore {
            color: var(--success);
        }

        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: linear-gradient(145deg, var(--glass-bg) 0%, rgba(13, 20, 69, 0.9) 100%);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .empty-state::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .empty-state p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 90%;
            width: 400px;
            text-align: center;
            transition: transform 0.3s ease;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .modal.show .modal-content {
            transform: translate(-50%, -50%) scale(1);
        }

        .modal-icon {
            font-size: 3rem;
            color: var(--danger);
            margin-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .modal-text {
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .modal-btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            min-width: 120px;
        }

        .modal-btn.confirm {
            background: var(--danger);
            color: white;
        }

        .modal-btn.cancel {
            background: var(--glass-bg);
            color: var(--text-color);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-btn:hover {
            transform: translateY(-2px);
        }

        .modal-btn.confirm:hover {
            background: #ff3333;
            box-shadow: 0 5px 15px rgba(255, 77, 77, 0.3);
        }

        .modal-btn.cancel:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
                padding: 1rem;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .nav-btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .message-actions {
                flex-wrap: wrap;
            }

            .action-btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="messages-wrapper">
        <div class="dashboard-header">
            <div class="header-title">
                <h1><?php echo $show_deleted ? 'Deleted Messages' : 'Messages Inbox'; ?></h1>
                <?php if ($unread_count > 0 && !$show_deleted): ?>
                    <div class="unread-badge">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo $unread_count; ?> unread</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="header-actions">
                <a href="?view=inbox" class="nav-btn <?php echo !$show_deleted ? 'active' : ''; ?>">
                    <i class="fas fa-inbox"></i>
                    <span>Inbox</span>
                </a>
                <a href="?view=deleted" class="nav-btn <?php echo $show_deleted ? 'active' : ''; ?>">
                    <i class="fas fa-trash"></i>
                    <span>Deleted</span>
                </a>
                <a href="dashboard.php" class="nav-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>

        <div class="messages-grid">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <i class="fas <?php echo $show_deleted ? 'fa-trash-alt' : 'fa-inbox'; ?>"></i>
                    <p><?php echo $show_deleted ? 'No deleted messages' : 'No messages yet'; ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message-card <?php echo !$message['is_read'] && !$show_deleted ? 'unread' : ''; ?>">
                        <div class="message-header">
                            <div class="sender-info">
                                <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                                <div class="message-date">
                                    <?php echo date('F j, Y g:i A', strtotime($message['date_sent'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="message-subject"><?php echo htmlspecialchars($message['subject']); ?></div>
                        <div class="message-preview">
                            <?php 
                            $preview = substr(strip_tags($message['message']), 0, 150);
                            echo htmlspecialchars($preview . (strlen($message['message']) > 150 ? '...' : ''));
                            ?>
                        </div>
                        <div class="message-actions">
                            <?php if (!$show_deleted): ?>
                                <a href="view_message.php?id=<?php echo $message['id']; ?>" class="action-btn view">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </a>
                                <button class="action-btn" onclick="toggleRead(<?php echo $message['id']; ?>, <?php echo $message['is_read'] ? 'false' : 'true'; ?>)">
                                    <i class="fas <?php echo $message['is_read'] ? 'fa-envelope' : 'fa-envelope-open'; ?>"></i>
                                    <span><?php echo $message['is_read'] ? 'Mark as Unread' : 'Mark as Read'; ?></span>
                                </button>
                                <button class="action-btn delete" onclick="deleteMessage(<?php echo $message['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            <?php else: ?>
                                <button class="action-btn restore" onclick="restoreMessage(<?php echo $message['id']; ?>)">
                                    <i class="fas fa-undo"></i>
                                    <span>Restore</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h2 class="modal-title">Delete Message?</h2>
            <p class="modal-text">Are you sure you want to delete this message? This action cannot be undone.</p>
            <div class="modal-actions">
                <button class="modal-btn cancel" onclick="closeModal()">Cancel</button>
                <button class="modal-btn confirm" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>

    <script>
    function toggleRead(messageId, isRead) {
        updateMessage(messageId, isRead ? 'mark_read' : 'mark_unread');
    }

    const modal = document.getElementById('confirmModal');
    const confirmBtn = document.getElementById('confirmDelete');
    let messageToDelete = null;

    function deleteMessage(messageId) {
        messageToDelete = messageId;
        modal.classList.add('show');
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            messageToDelete = null;
        }, 300);
    }

    confirmBtn.addEventListener('click', () => {
        if (messageToDelete) {
            updateMessage(messageToDelete, 'delete');
            closeModal();
        }
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });

    function restoreMessage(messageId) {
        updateMessage(messageId, 'restore');
    }

    function updateMessage(messageId, action) {
        fetch('messages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `message_id=${messageId}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update message'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
    </script>
</body>
</html>