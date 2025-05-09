<?php
session_start();
require_once dirname(__DIR__) . '/includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Get message ID from URL
$message_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$message_id) {
    header('Location: messages.php');
    exit();
}

// Get message details
try {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch();

    if (!$message) {
        header('Location: messages.php');
        exit();
    }

    // Mark message as read
    if (!$message['is_read']) {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = TRUE WHERE id = ?");
        $stmt->execute([$message_id]);
    }
} catch (PDOException $e) {
    header('Location: messages.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --gradient-1: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
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

        .message-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        .glow-effect {
            position: absolute;
            width: 150px;
            height: 150px;
            filter: blur(90px);
            border-radius: 50%;
            opacity: 0.15;
            pointer-events: none;
        }

        .glow-1 { top: -75px; left: -75px; background: var(--primary-color); }
        .glow-2 { bottom: -75px; right: -75px; background: var(--secondary-color); }

        .message-header {
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .message-title {
            font-size: 1.8rem;
            margin: 0 0 1.5rem 0;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 600;
        }

        .sender-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            min-width: 80px;
        }

        .info-value {
            color: var(--text-color);
            font-weight: 500;
        }

        .message-content {
            background: var(--glass-bg);
            padding: 1.5rem;
            border-radius: 12px;
            margin: 1.5rem 0;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.8;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 100%;
            overflow-x: hidden;
        }

        .message-content p {
            margin-bottom: 1rem;
        }

        .actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background: var(--glass-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-color);
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.2);
        }

        .btn.back {
            background: var(--gradient-1);
            border: none;
        }

        .btn.back:hover {
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }

        .btn.danger {
            color: var(--danger);
        }

        .btn.danger:hover {
            background: rgba(255, 77, 77, 0.1);
            box-shadow: 0 5px 15px rgba(255, 77, 77, 0.2);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .message-container {
                padding: 1.5rem;
            }

            .message-title {
                font-size: 1.5rem;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Delete Confirmation Modal */
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
        }

        .modal-text {
            color: var(--text-muted);
            margin-bottom: 2rem;
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
    </style>
</head>
<body>
    <div class="message-container">
        <div class="glow-effect glow-1"></div>
        <div class="glow-effect glow-2"></div>

        <div class="message-header">
            <h1 class="message-title"><?php echo htmlspecialchars($message['subject']); ?></h1>
            <div class="sender-info">
                <div class="info-row">
                    <span class="info-label">From:</span>
                    <span class="info-value"><?php echo htmlspecialchars($message['name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($message['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sent:</span>
                    <span class="info-value"><?php echo date('F j, Y g:i A', strtotime($message['date_sent'])); ?></span>
                </div>
            </div>
        </div>
        
        <div class="message-content">
            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
        </div>

        <div class="actions">
            <a href="messages.php" class="btn back">
                <i class="fas fa-arrow-left"></i>
                Back to Messages
            </a>
            <?php if (!$message['is_deleted']): ?>
            <button class="btn danger" onclick="showDeleteModal()">
                <i class="fas fa-trash"></i>
                Delete Message
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <i class="fas fa-exclamation-triangle modal-icon"></i>
            <h2 class="modal-title">Delete Message?</h2>
            <p class="modal-text">Are you sure you want to delete this message? This action cannot be undone.</p>
            <div class="modal-actions">
                <button class="modal-btn cancel" onclick="closeModal()">Cancel</button>
                <button class="modal-btn confirm" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <script>
    const modal = document.getElementById('deleteModal');

    function showDeleteModal() {
        modal.style.display = 'block';
        setTimeout(() => modal.classList.add('show'), 10);
    }

    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }

    function confirmDelete() {
        fetch('messages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `message_id=<?php echo $message['id']; ?>&action=delete`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = 'messages.php';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
    </script>
</body>
</html>