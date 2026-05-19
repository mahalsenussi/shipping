<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all manifest/delivery order info
    $source_port = $_POST['source_port'];
    $manifest_number = $_POST['manifest_number'];
    $shipping_number = $_POST['shipping_number'];
    $ship_name = $_POST['ship_name'];
    $date_of_arrival = $_POST['date_of_arrival'];
    $number_of_boxes = $_POST['number_of_boxes'];
    $company_id = intval($_POST['company_id']);

    // Fetch company name
    $company = $conn->query("SELECT company_name FROM company WHERE id=$company_id")->fetch_assoc();
    $company_name = $company ? $company['company_name'] : '';

    // Display info (replace with PDF/delivery order print logic later)
    echo "<h2>Delivery Order</h2>
    <table border='1' cellpadding='8'>
        <tr><th>Source Port</th><td>$source_port</td></tr>
        <tr><th>Manifest Number</th><td>$manifest_number</td></tr>
        <tr><th>Shipping Number</th><td>$shipping_number</td></tr>
        <tr><th>Ship Name</th><td>$ship_name</td></tr>
        <tr><th>Date of Arrival</th><td>$date_of_arrival</td></tr>
        <tr><th>Number of Boxes</th><td>$number_of_boxes</td></tr>
        <tr><th>Source (Company)</th><td>$company_name</td></tr>
    </table>
    <p><a href='index.php'>Back to Home</a></p>";
}
?>
