<?php
try {
    // Create database connection without selecting a database
    $pdo = new PDO("mysql:host=localhost", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS portfolio");
    $pdo->exec("USE portfolio");

    // Create messages table
    $pdo->exec("DROP TABLE IF EXISTS messages");
    $pdo->exec("CREATE TABLE messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        date_sent DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        is_read BOOLEAN DEFAULT FALSE,
        is_deleted BOOLEAN DEFAULT FALSE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Create admin table
    $pdo->exec("DROP TABLE IF EXISTS admin");
    $pdo->exec("CREATE TABLE admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Add indexes for better performance
    $pdo->exec("ALTER TABLE messages ADD INDEX idx_is_read (is_read)");
    $pdo->exec("ALTER TABLE messages ADD INDEX idx_is_deleted (is_deleted)");
    $pdo->exec("ALTER TABLE messages ADD INDEX idx_date_sent (date_sent)");

    // Create default admin user
    $defaultUsername = 'admin';
    $defaultPassword = 'admin123';
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->execute([$defaultUsername, $hashedPassword]);

    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; border-radius: 8px;'>";
    echo "<h2 style='color: #4CAF50;'>Database Setup Complete!</h2>";
    echo "<p>The database has been successfully created and initialized.</p>";
    echo "<p>You can now log in with these credentials:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> admin123</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='color: #2196F3; text-decoration: none;'>Click here to login</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>