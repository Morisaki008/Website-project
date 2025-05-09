<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e00ff;
            --secondary-color: #00d4ff;
            --background-dark: #0a0a2e;
            --text-color: #ffffff;
            --card-bg: rgba(13, 20, 69, 0.7);
            --glass-bg: rgba(255, 255, 255, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--background-dark) 0%, #1a1a4f 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-color);
            padding: 2rem;
        }

        .profile-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .profile-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 2rem;
            border: 3px solid var(--primary-color);
            padding: 5px;
            background: var(--glass-bg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-image i {
            font-size: 4rem;
            color: var(--primary-color);
        }

        .profile-info {
            background: var(--glass-bg);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn {
            padding: 1rem;
            border-radius: 10px;
            border: none;
            background: var(--glass-bg);
            color: var(--text-color);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1.2rem;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .btn.primary {
            background: var(--primary-color);
        }

        .btn.primary:hover {
            background: var(--secondary-color);
        }

        @media (max-width: 480px) {
            .profile-container {
                padding: 2rem;
            }

            .profile-header h1 {
                font-size: 2rem;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-image">
                <i class="fas fa-user"></i>
            </div>
            <h1>Admin Profile</h1>
        </div>

        <div class="profile-info">
            <div class="info-item">
                <span class="info-label">Username</span>
                <span>admin</span>
            </div>
            <div class="info-item">
                <span class="info-label">Role</span>
                <span>Administrator</span>
            </div>
            <div class="info-item">
                <span class="info-label">Last Login</span>
                <span><?php echo date('F j, Y g:i A'); ?></span>
            </div>
        </div>

        <div class="actions">
            <a href="dashboard.php" class="btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>