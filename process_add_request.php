<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $cargo_type = $conn->real_escape_string($_POST['cargo_type']);
    $width = $conn->real_escape_string($_POST['width']);
    $arrival_port = $conn->real_escape_string($_POST['arrival_port']);

    $sql = "INSERT INTO requests (customer_name, address, cargo_type, width, arrival_port)
            VALUES ('$customer_name', '$address', '$cargo_type', '$width', '$arrival_port')";
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
