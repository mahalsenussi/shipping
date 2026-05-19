<?php
// Test database connection
require_once __DIR__ . '/config/database.php';

// Test connection
function testConnection() {
    try {
        $conn = getDbConnection();
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        echo "<h2>Database Connection Test</h2>";
        echo "<p>Connected successfully to MySQL server version: " . $conn->server_info . "</p>";
        
        // Check if database exists
        $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'shipping_v2'");
        if ($result->num_rows > 0) {
            echo "<p>Database 'shipping_v2' exists.</p>";
            
            // Select the database
            $conn->select_db('shipping_v2');
            
            // List tables
            $tables = $conn->query("SHOW TABLES");
            echo "<h3>Tables in shipping_v2:</h3>";
            echo "<ul>";
            while ($row = $tables->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
            
            // Test insert
            $testName = "TestPort_" . time();
            $stmt = $conn->prepare("INSERT INTO ports (name, code, country, type) VALUES (?, 'TP1', 'Test', 'seaport')");
            $stmt->bind_param("s", $testName);
            
            if ($stmt->execute()) {
                echo "<p>Successfully inserted test port: $testName</p>";
            } else {
                echo "<p>Insert failed: " . $stmt->error . "</p>";
            }
            
        } else {
            echo "<p>Database 'shipping_v2' does not exist.</p>";
        }
        
        $conn->close();
        
    } catch (Exception $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

// Run the test
echo "<html><body>";
testConnection();
echo "</body></html>";
?>
