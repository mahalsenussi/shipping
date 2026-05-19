<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'mahmoud');
define('DB_PASS', 'mahmoud');
define('DB_NAME', 'shipping_v2');

// Create connection
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for proper Unicode support
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
