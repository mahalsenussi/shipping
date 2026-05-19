<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $contact_info = $conn->real_escape_string($_POST['contact_info']);

    $sql = "INSERT INTO agents (name, contact_info) VALUES ('$name', '$contact_info')";
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
