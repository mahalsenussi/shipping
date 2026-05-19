<?php
/**
 * Debug script to test shipment saving functionality
 * Run this script to identify database issues
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug: Shipment Saving Test</h1>";
echo "<pre>";

// Test 1: Database connection
echo "\n=== Test 1: Database Connection ===\n";
try {
    require_once __DIR__ . '/config/database.php';
    $db = getDbConnection();
    echo "✓ Database connection successful\n";

    // Test basic query
    $result = $db->query("SELECT 1 as test");
    if ($result && $result->fetch_assoc()) {
        echo "✓ Basic query works\n";
    } else {
        echo "✗ Basic query failed\n";
    }

} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Check table existence and structure
echo "\n=== Test 2: Table Structure ===\n";
$tables = ['shipments', 'companies', 'ports', 'vessels', 'containers', 'cargo_items'];

foreach ($tables as $table) {
    $result = $db->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✓ Table '$table' exists\n";

        // Check for required columns
        $columns_result = $db->query("DESCRIBE $table");
        $columns = [];
        while ($row = $columns_result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }

        $required_columns = [
            'shipments' => ['reference_number', 'customer_id', 'shipping_type'],
            'companies' => ['name', 'type'],
            'ports' => ['name'],
            'vessels' => ['name'],
        ];

        if (isset($required_columns[$table])) {
            foreach ($required_columns[$table] as $required_col) {
                if (in_array($required_col, $columns)) {
                    echo "  ✓ Column '$required_col' exists\n";
                } else {
                    echo "  ✗ Column '$required_col' missing\n";
                }
            }
        }
    } else {
        echo "✗ Table '$table' does not exist\n";
    }
}

// Test 3: Check foreign key constraints
echo "\n=== Test 3: Foreign Key Constraints ===\n";
$fk_checks = [
    "SELECT COUNT(*) as count FROM companies WHERE type = 'customer'" => 'customers',
    "SELECT COUNT(*) as count FROM companies WHERE type = 'shipping_line'" => 'shipping_lines',
    "SELECT COUNT(*) as count FROM companies WHERE type = 'local_agent'" => 'agents',
    "SELECT COUNT(*) as count FROM ports" => 'ports',
    "SELECT COUNT(*) as count FROM vessels" => 'vessels',
];

foreach ($fk_checks as $query => $label) {
    try {
        $result = $db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            echo "✓ $label: {$row['count']} records\n";
        }
    } catch (Exception $e) {
        echo "✗ Error checking $label: " . $e->getMessage() . "\n";
    }
}

// Test 4: Test actual shipment insertion (dry run)
echo "\n=== Test 4: Shipment Insertion Test ===\n";
try {
    // Start transaction
    $db->begin_transaction();

    // Try to insert a test shipment (will be rolled back)
    $test_ref = 'TEST-' . date('YmdHis');
    $stmt = $db->prepare("INSERT INTO shipments (reference_number, customer_id, shipping_type, status, created_by) VALUES (?, 1, 'naval', 'draft', 1)");
    $stmt->bind_param('s', $test_ref);

    if ($stmt->execute()) {
        $shipment_id = $db->insert_id;
        echo "✓ Test shipment inserted successfully (ID: $shipment_id)\n";

        // Rollback the test
        $db->rollback();
        echo "✓ Test transaction rolled back\n";

        // Clean up the test record that might have been committed
        $db->query("DELETE FROM shipments WHERE reference_number = '$test_ref'");
        echo "✓ Test record cleaned up\n";

    } else {
        echo "✗ Test shipment insertion failed: " . $db->error . "\n";
        $db->rollback();
    }

} catch (Exception $e) {
    echo "✗ Test shipment insertion error: " . $e->getMessage() . "\n";
    $db->rollback();
}

// Test 5: Check current session and POST data structure
echo "\n=== Test 5: Current Environment ===\n";
echo "Session ID: " . session_id() . "\n";
echo "Current POST data: " . (isset($_POST) ? count($_POST) . " fields" : "No POST data") . "\n";

// Test 6: Check PHP configuration that might affect database operations
echo "\n=== Test 6: PHP Configuration ===\n";
$configs = [
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'mysqli.allow_persistent' => ini_get('mysqli.allow_persistent'),
    'mysqli.max_persistent' => ini_get('mysqli.max_persistent'),
];

foreach ($configs as $key => $value) {
    echo "$key: $value\n";
}

echo "\n=== Debug Complete ===\n";
echo "Check the error log for detailed database errors if the shipment saving still fails.\n";
echo "</pre>";
?>
