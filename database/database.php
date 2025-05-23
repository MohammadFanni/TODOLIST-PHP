<?php
// database/database.php

// Database configuration
$host = "localhost";
$username = "root";
$password = ""; // Update for production
$dbname = "db_todo";

// Application environment: 'dev' for development, 'prod' for production
define('APP_ENV', 'dev'); // Change to 'prod' when deploying

try {
    // PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Show detailed error only in development mode
    if (APP_ENV === 'dev') {
        header('Content-Type: text/html');
        echo "<pre>";
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed",
            "error" => $e->getMessage()
        ], JSON_PRETTY_PRINT);
        echo "</pre>";
    } else {
        // Hide details in production
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false,
            "message" => "Internal server error"
        ]);
    }
    exit;
}
?>