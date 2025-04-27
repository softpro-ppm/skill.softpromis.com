<?php
require_once 'config.php';

try {
    // First connect without database selected
    $pdo = new PDO(
        "mysql:host=" . DB_HOST,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

    // Create database if not exists
    $pdo->exec("DROP DATABASE IF EXISTS `" . DB_NAME . "`");
    $pdo->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Select the database
    $pdo->exec("USE `" . DB_NAME . "`");

    // Create users table with correct structure
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `user_id` int(11) NOT NULL AUTO_INCREMENT,
        `role_id` int(11) DEFAULT NULL,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `email` varchar(100) NOT NULL,
        `full_name` varchar(100) DEFAULT NULL,
        `mobile` varchar(15) DEFAULT NULL,
        `status` enum('active','inactive') DEFAULT 'active',
        `created_at` timestamp NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        `token` text DEFAULT NULL,
        `last_login` text DEFAULT NULL,
        PRIMARY KEY (`user_id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Insert default admin user
    $adminPassword = password_hash('Admin@123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO `users` (
        `username`, 
        `email`, 
        `password`, 
        `full_name`,
        `role_id`,
        `status`
    ) VALUES (
        'admin',
        'admin@softpro.com',
        ?,
        'Administrator',
        1,
        'active'
    )");
    $stmt->execute([$adminPassword]);

    echo "Database setup completed successfully!<br><br>";
    echo "Default admin credentials:<br>";
    echo "Email: admin@softpro.com<br>";
    echo "Password: Admin@123<br><br>";
    echo "Please login using these credentials.";
    
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage() . "<br>Trace: " . $e->getTraceAsString());
} 