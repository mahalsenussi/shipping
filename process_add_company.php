<?php
include 'db_config.php';

// Company details
$companyName = $_POST['company_name'];
$companyAddress = $_POST['company_address'];
$companyPhone = $_POST['company_phone'];
$companyEmail = $_POST['company_email'];

// Prepare and execute the SQL statement
$sql = "INSERT INTO company (company_name, company_address, company_phone, company_email)
        VALUES ('$companyName', '$companyAddress', '$companyPhone', '$companyEmail')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Company added successfully.</h2>";
} else {
    echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
}

// Close the database connection
$conn->close();
?>