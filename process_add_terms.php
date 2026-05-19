<?php
include 'db_config.php';

// Terms and Conditions details
$terms = $_POST['terms'];

// Prepare and execute the SQL statement
$sql = "INSERT INTO terms_conditions (terms) VALUES ('$terms')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Terms and Conditions added successfully.</h2>";
} else {
    echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
}

// Close the database connection
$conn->close();
?>
