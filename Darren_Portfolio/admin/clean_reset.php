<?php
require_once '../includes/db_connect.php';

try {
    // Drop the admin table
    $pdo->exec("DROP TABLE IF EXISTS admin");

    // Create fresh admin table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Create admin user with simple password storage
    $sql = "INSERT INTO admin (username, password) VALUES ('admin', ?)";
    $stmt = $pdo->prepare($sql);
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt->execute([$hashed_password]);

    echo '<div style="font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; border-radius: 8px; text-align: center;">';
    echo '<h2 style="color: #4CAF50;">Admin Account Reset Successfully</h2>';
    echo '<p>Your admin account has been reset. You can now log in with:</p>';
    echo '<div style="background: #fff; padding: 15px; border-radius: 4px; margin: 15px 0; text-align: left;">';
    echo '<strong>Username:</strong> admin<br>';
    echo '<strong>Password:</strong> admin123';
    echo '</div>';
    echo '<p><a href="login.php" style="background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;">Go to Login Page</a></p>';
    echo '</div>';

} catch(PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>