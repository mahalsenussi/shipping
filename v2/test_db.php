<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

require_once __DIR__ . '/config/database.php';

// Test database connection
try {
    $db = getDbConnection();
    echo "Database connection successful!<br>";
    
    // Test query
    $result = $db->query("SELECT COUNT(*) as count FROM companies");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Number of companies: " . $row['count'] . "<br>";
    } else {
        echo "Query failed: " . $db->error . "<br>";
    }
    
    // Test insert
    $testName = "test_" . time();
    $stmt = $db->prepare("INSERT INTO companies (name, type) VALUES (?, 'customer')");
    if ($stmt) {
        $stmt->bind_param("s", $testName);
        if ($stmt->execute()) {
            echo "Test insert successful! ID: " . $db->insert_id . "<br>";
        } else {
            echo "Insert failed: " . $stmt->error . "<br>";
        }
    } else {
        echo "Prepare failed: " . $db->error . "<br>";
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
