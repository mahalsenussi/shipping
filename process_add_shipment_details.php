<?php
include 'db_config.php';

// Shipment details
$shipmentId = $_POST['shipment_id'];
$quantity = $_POST['quantity'];
$weight = $_POST['weight'];
$description = $_POST['description'];
$marksNumbers = $_POST['marks_numbers'];
$containerSealNumber = $_POST['container_seal_number'];

// Prepare and execute the SQL statement
$sql = "INSERT INTO shipment_details (shipment_id, quantity, weight, description, marks_numbers, container_seal_number)
        VALUES ('$shipmentId', '$quantity', '$weight', '$description', '$marksNumbers', '$containerSealNumber')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Shipment details added successfully.</h2>";
} else {
    echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
}

// Close the database connection
$conn->close();
?>
