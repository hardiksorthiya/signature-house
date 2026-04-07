<?php
// Script to create the database for Laravel textile project

$host = '127.0.0.1';
$port = '3306';
$username = 'root';
$password = '';
$database = 'textile';

try {
    // Connect to MySQL server without selecting a database
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    
    echo "Database '$database' created successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nPlease make sure:\n";
    echo "1. XAMPP MySQL service is running\n";
    echo "2. MySQL credentials are correct (username: root, password: empty)\n";
    exit(1);
}

