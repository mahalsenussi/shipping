<?php
include 'db_config.php';

// Freight & Charges details
$billNumber = $_POST['bill_number'];
$rate = $_POST['rate'];
$currency = $_POST['currency'];
$amount = $_POST['amount'];
$prepaid = $_POST['prepaid'];
$carriersReceipt = $_POST['carriers_receipt'];
$placeOfIssue = $_POST['place_of_issue'];
$numberSequence = $_POST['number_sequence'];
$dateOfIssue = $_POST['date_of_issue'];
$declaredValue = $_POST['declared_value'];
$shippedOnBoardDate = $_POST['shipped_on_board_date'];

// Prepare and execute the SQL statement
$sql = "INSERT INTO freight_charges (bill_number, rate, currency, amount, prepaid, carriers_receipt, place_of_issue, number_sequence, date_of_issue, declared_value, shipped_on_board_date)
        VALUES ('$billNumber', '$rate', '$currency', '$amount', '$prepaid', '$carriersReceipt', '$placeOfIssue', '$numberSequence', '$dateOfIssue', '$declaredValue', '$shippedOnBoardDate')";

if ($conn->query($sql) === TRUE) {
    echo "<h2>Freight & Charges details added successfully.</h2>";
} else {
    echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
}

// Close the database connection
$conn->close();
?>
