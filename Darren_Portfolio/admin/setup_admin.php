<?php
require_once '../includes/db_connect.php';

try {
    // Drop existing admin table to ensure clean setup
    $pdo->exec("DROP TABLE IF EXISTS admin");

    // Create admin table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create default admin user
    $username = 'admin';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);

    echo "Admin setup completed successfully. You can now login with:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>