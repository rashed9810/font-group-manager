<?php
// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `font_group_system`");
    echo "Database created successfully\n";
    
    // Select database
    $pdo->exec("USE `font_group_system`");
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `fonts` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `file_path` varchar(255) NOT NULL,
          `original_name` varchar(255) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Fonts table created successfully\n";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `font_groups` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Font groups table created successfully\n";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `font_group_items` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `group_id` int(11) NOT NULL,
          `font_id` int(11) NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `group_id` (`group_id`),
          KEY `font_id` (`font_id`),
          CONSTRAINT `font_group_items_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `font_groups` (`id`) ON DELETE CASCADE,
          CONSTRAINT `font_group_items_ibfk_2` FOREIGN KEY (`font_id`) REFERENCES `fonts` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Font group items table created successfully\n";
    
    echo "Database setup completed successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
