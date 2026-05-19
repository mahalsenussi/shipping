<?php
include 'db_config.php';

// Broker details
$brokerName = $_POST['broker_name'];
$brokerAddress = $_POST['broker_address'];
$brokerPhone = $_POST['broker_phone'];
$brokerEmail = $_POST['broker_email'];

// Prepare and execute the SQL statement
$sql = "INSERT INTO broker (broker_name, broker_address, broker_phone, broker_email)
        VALUES ('$brokerName', '$brokerAddress', '$brokerPhone', '$brokerEmail')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Broker added successfully.</h2>";
} else {
    echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
}

// Close the database connection
$conn->close();
?>
