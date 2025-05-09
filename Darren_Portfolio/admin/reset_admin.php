<?php
require_once '../includes/db_connect.php';

try {
    // Drop the admin table to start fresh
    $pdo->exec("DROP TABLE IF EXISTS admin");
    
    // Create the admin table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create the admin user
    $username = 'admin';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $result = $stmt->execute([$username, $hashed_password]);
    
    if ($result) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; background: #f0f0f0; border-radius: 5px;'>";
        echo "<h2 style='color: #4CAF50;'>Admin Account Reset Successfully</h2>";
        echo "<p>The admin account has been reset with the following credentials:</p>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> admin</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "</ul>";
        echo "<p><a href='login.php' style='color: #2196F3;'>Click here to login</a></p>";
        echo "</div>";
    }
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>