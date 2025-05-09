<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

require_once dirname(__DIR__) . '/includes/db_connect.php';

// Get message counts
try {
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_messages,
        SUM(CASE WHEN is_read = FALSE AND is_deleted = FALSE THEN 1 ELSE 0 END) as unread_messages
    FROM messages");
    $stmt->execute();
    $counts = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $counts = ['total_messages' => 0, 'unread_messages' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6C63FF;
            --secondary-color: #4FACFE;
            --background-dark: #0F123F;
            --text-color: #ffffff;
            --card-bg: rgba(13, 20, 69, 0.7);
            --accent-color: #FF6B6B;
            --success-color: #2ECC71;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --gradient-1: linear-gradient(45deg, #6C63FF, #4FACFE);
            --gradient-2: linear-gradient(135deg, #FF6B6B, #FF8E53);
            --box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
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
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-color);
            position: relative;
            overflow-x: hidden;
            padding: 2rem;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .dashboard-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 3rem;
            width: 95%;
            max-width: 1200px;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }

        .glow-effect {
            position: absolute;
            width: 150px;
            height: 150px;
            background: var(--primary-color);
            filter: blur(90px);
            border-radius: 50%;
            opacity: 0.15;
            pointer-events: none;
        }

        .glow-1 { top: -75px; left: -75px; background: var(--primary-color); }
        .glow-2 { bottom: -75px; right: -75px; background: var(--secondary-color); }

        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .dashboard-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
        }

        .dashboard-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background: var(--gradient-1);
            border-radius: 2px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--glass-bg);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transform: translateX(-100%);
            transition: 0.5s;
        }

        .stat-card:hover::before {
            transform: translateX(100%);
        }

        .stat-card h4 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .stat-card p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .stat-card i {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 2.5rem;
            opacity: 0.2;
        }

        .dashboard-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .menu-item {
            background: var(--glass-bg);
            padding: 2rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .menu-item:hover {
            transform: translateY(-5px) scale(1.02);
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.2);
        }

        .menu-item i {
            font-size: 2.5rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            transition: all 0.3s ease;
        }

        .menu-item .content {
            flex: 1;
        }

        .menu-item h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .menu-item p {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.4;
        }

        .menu-item::after {
            content: '\f054';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .menu-item:hover::after {
            opacity: 1;
            right: 15px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 0.8rem 1.5rem;
            background: var(--gradient-2);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 2rem;
            }

            .dashboard-title {
                font-size: 2rem;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .stat-card h4 {
                font-size: 2rem;
            }

            .menu-item {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="particles" id="particles-js"></div>
    
    <div class="dashboard-container">
        <div class="glow-effect glow-1"></div>
        <div class="glow-effect glow-2"></div>
        
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>

        <div class="dashboard-header">
            <h1 class="dashboard-title">Welcome to the Admin Dashboard</h1>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <i class="fas fa-envelope"></i>
                <h4><?php echo $counts['unread_messages']; ?></h4>
                <p>New Messages</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-inbox"></i>
                <h4><?php echo $counts['total_messages']; ?></h4>
                <p>Total Messages</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-shield"></i>
                <h4>Admin</h4>
                <p>Current Role</p>
            </div>
        </div>

        <div class="dashboard-menu">
            <a href="messages.php" class="menu-item">
                <i class="fas fa-envelope"></i>
                <div class="content">
                    <h3>Messages</h3>
                    <p>View and manage incoming messages from visitors</p>
                </div>
            </a>
            <a href="profile.php" class="menu-item">
                <i class="fas fa-user-circle"></i>
                <div class="content">
                    <h3>Profile</h3>
                    <p>Update your admin profile and security settings</p>
                </div>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#6C63FF' },
                shape: { type: 'circle' },
                opacity: {
                    value: 0.5,
                    random: true,
                    animation: { enable: true, speed: 1, opacity_min: 0.1, sync: false }
                },
                size: {
                    value: 3,
                    random: true,
                    animation: { enable: true, speed: 2, size_min: 0.1, sync: false }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#6C63FF',
                    opacity: 0.2,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 1,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false,
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                },
                modes: {
                    repulse: { distance: 100, duration: 0.4 },
                    push: { particles_nb: 4 }
                }
            },
            retina_detect: true
        });

        // Add animation to stat cards
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Fetch message count with animation
        fetch('get_message_count.php')
            .then(response => response.json())
            .then(data => {
                animateValue(document.getElementById('messageCount'), 0, data.count, 1000);
            })
            .catch(error => console.error('Error:', error));

        // Simulate visit count with animation
        const visitCount = Math.floor(Math.random() * 100);
        animateValue(document.getElementById('visitCount'), 0, visitCount, 1000);

        // Auto-refresh message count every 30 seconds
        setInterval(() => {
            fetch('get_message_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.count !== undefined) {
                        document.getElementById('messageCount').textContent = data.count;
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 30000);
    </script>
</body>
</html>