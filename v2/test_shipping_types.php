<?php
// Test script for creating different shipping types
require_once __DIR__ . '/config/database.php';

$db = getDbConnection();

// Test data
$testData = [
    'naval' => [
        'reference_number' => 'TEST-NAVAL-' . time(),
        'customer_id' => 1, // Assuming customer with ID 1 exists
        'shipping_type' => 'naval',
        'origin_port_id' => 1, // Assuming port with ID 1 exists
        'destination_port_id' => 2, // Assuming port with ID 2 exists
        'vessel_id' => 1, // Assuming vessel with ID 1 exists
        'voyage_number' => 'TEST-VOYAGE-001',
        'estimated_departure_date' => date('Y-m-d'),
        'estimated_arrival_date' => date('Y-m-d', strtotime('+7 days'))
    ],
    'air' => [
        'reference_number' => 'TEST-AIR-' . time(),
        'customer_id' => 1,
        'shipping_type' => 'air',
        'origin_port_id' => 1,
        'destination_port_id' => 2
    ],
    'land' => [
        'reference_number' => 'TEST-LAND-' . time(),
        'customer_id' => 1,
        'shipping_type' => 'land',
        'origin_port_id' => 1,
        'destination_port_id' => 2
    ]
];

echo "Testing shipment creation for different shipping types...\n\n";

foreach ($testData as $type => $data) {
    echo "Creating {$type} shipment...\n";

    $stmt = $db->prepare("INSERT INTO shipments (reference_number, customer_id, shipping_type, origin_port_id, destination_port_id, vessel_id, voyage_number, estimated_departure_date, estimated_arrival_date, status, created_by)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', 1)");

    // Set default values for optional fields
    $vesselId = $data['vessel_id'] ?? null;
    $voyageNumber = $data['voyage_number'] ?? null;
    $estDep = $data['estimated_departure_date'] ?? null;
    $estArr = $data['estimated_arrival_date'] ?? null;

    $stmt->bind_param("sssiissss",
        $data['reference_number'],
        $data['customer_id'],
        $data['shipping_type'],
        $data['origin_port_id'],
        $data['destination_port_id'],
        $vesselId,
        $voyageNumber,
        $estDep,
        $estArr
    );

    if ($stmt->execute()) {
        $shipmentId = $db->insert_id;
        echo "✅ {$type} shipment created successfully with ID: {$shipmentId}\n";
    } else {
        echo "❌ Failed to create {$type} shipment: " . $stmt->error . "\n";
    }
}

echo "\nVerifying created shipments...\n";
$result = $db->query("SELECT id, reference_number, shipping_type FROM shipments WHERE reference_number LIKE 'TEST-%' ORDER BY id DESC LIMIT 3");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "✅ Shipment {$row['id']}: {$row['reference_number']} ({$row['shipping_type']})\n";
    }
} else {
    echo "❌ No test shipments found\n";
}

echo "\nTest completed!\n";
?>
