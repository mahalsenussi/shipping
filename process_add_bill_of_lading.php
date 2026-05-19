<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_config.php';

// Bill of lading details
$shipmentId = $_POST['shipment_id'];
$billNumber = $_POST['bill_number'];
$issueDate = $_POST['issue_date'];

// Check if the shipment ID exists
$shipmentCheckSql = "SELECT id FROM shipments WHERE id = '$shipmentId'";
$shipmentCheckResult = $conn->query($shipmentCheckSql);

if ($shipmentCheckResult->num_rows > 0) {
    // Prepare and execute the SQL statement
    $sql = "INSERT INTO bill_of_lading (shipment_id, bill_number, issue_date)
            VALUES ('$shipmentId', '$billNumber', '$issueDate')";

    if ($conn->query($sql) === TRUE) {
        echo "<h2>Bill of Lading added successfully.</h2>";
    } else {
        echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
    }
} else {
    echo "<h2>Error: Shipment ID does not exist.</h2>";
}

// Close the database connection
$conn->close();
?>
