<?php
$servername = "localhost";
$username = "harmony1_mahmoud";
$password = "7-GACv~bkbq9";
$dbname = "harmony1_shipping";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
