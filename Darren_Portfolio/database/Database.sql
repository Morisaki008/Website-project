-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS portfolio;
USE portfolio;

-- Drop existing tables to ensure clean setup
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS admin;

-- Create messages table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date_sent DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    is_deleted BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password will be hashed in PHP)
INSERT INTO admin (username, password) 
VALUES ('admin', '$2y$10$YourHashedPasswordHere');

-- Add indexes for better performance
ALTER TABLE messages ADD INDEX idx_is_read (is_read);
ALTER TABLE messages ADD INDEX idx_is_deleted (is_deleted);
ALTER TABLE messages ADD INDEX idx_date_sent (date_sent);