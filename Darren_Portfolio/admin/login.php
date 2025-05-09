<?php
session_start();
require_once dirname(__FILE__) . '/../includes/db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add debug log file
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/login_debug.log');
error_log("Login page accessed");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    error_log("Login attempt - Username: " . $username);

    try {
        // Test database connection
        error_log("Testing database connection...");
        $test = $pdo->query("SELECT 1");
        error_log("Database connection successful");

        // First, check if we need to create default admin
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin");
        $adminCount = $stmt->fetchColumn();
        error_log("Admin count in database: " . $adminCount);

        if ($adminCount === 0) {
            // Create default admin user
            $defaultUsername = 'admin';
            $defaultPassword = 'admin123';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            if ($stmt->execute([$defaultUsername, $hashedPassword])) {
                error_log("Default admin user created successfully");
            }
        }

        // Now attempt login
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        error_log("User found in database: " . ($user ? 'Yes' : 'No'));

        if ($user) {
            error_log("Attempting password verification");
            $passwordMatch = password_verify($password, $user['password']);
            error_log("Password verification result: " . ($passwordMatch ? 'Success' : 'Failed'));

            if ($passwordMatch) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                error_log("Login successful - Redirecting to dashboard");
                header('Location: dashboard.php');
                exit();
            }
        }
        
        error_log("Login failed - Invalid credentials");
        $error = 'Invalid username or password';
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = 'Database error occurred. Please try again.';
    }
}

// Check if we need to show default credentials message
$showDefaultCredentials = false;
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin");
    $adminCount = $stmt->fetchColumn();
    $showDefaultCredentials = ($adminCount === 0);
} catch (PDOException $e) {
    error_log("Error checking admin count: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a90e2;
            --primary-hover: #357abd;
            --text-color: #ffffff;
            --background-dark: #0a0c1b;
            --card-bg: rgba(255, 255, 255, 0.05);
            --input-bg: rgba(255, 255, 255, 0.07);
            --error: #ff4d4d;
            --success: #4bb543;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: radial-gradient(circle at center, #1a1f4d, var(--background-dark));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .login-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-out;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .login-title {
            font-size: 2rem;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: var(--input-bg);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(74, 144, 226, 0.1);
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
        }

        .form-control:focus + i {
            color: var(--primary-color);
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error {
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div id="particles-js" class="particles-js"></div>
    
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-user-shield login-icon"></i>
            <h1 class="login-title">Admin Login</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
                <i class="fas fa-user"></i>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <i class="fas fa-lock"></i>
            </div>
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#4a90e2' },
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
                    color: '#4a90e2',
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
    </script>
</body>
</html>
