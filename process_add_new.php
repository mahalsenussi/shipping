<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process Shipment ID
    $shipment_id = $_POST['shipment_id'];

    // Process Add Company
    $company_name = $_POST['company_name'];
    $company_address = $_POST['company_address'];
    $company_phone = $_POST['company_phone'];
    $company_email = $_POST['company_email'];
    // Process other company fields as needed

    // Process Add Broker
    $broker_name = $_POST['broker_name'];
    $broker_address = $_POST['broker_address'];
    $broker_phone = $_POST['broker_phone'];
    $broker_email = $_POST['broker_email'];
    // Process other broker fields as needed

    // Process Shipment Form
    $container_number = $_POST['container_number'];
    $company = $_POST['company'];
    $contact_person = $_POST['contact_person'];
    $container_size = $_POST['container_size'];
    $customs_broker = $_POST['customs_broker'];
    $date = $_POST['date'];
    $shipping_company = $_POST['shipping_company'];
    $shipping_port = $_POST['shipping_port'];
    $arrival_port = $_POST['arrival_port'];
    // Process other shipment form fields as needed

    // Process Add Bill of Lading
    $bill_of_lading_shipment_id = $_POST['bill_of_lading_shipment_id'];
    $bill_of_lading_number = $_POST['bill_of_lading_number'];
    $issue_date = $_POST['issue_date'];
    // Process other bill of lading fields as needed

    // Process Add Freight & Charges
    $bill_number = $_POST['bill_number'];
    $rate = $_POST['rate'];
    $currency = $_POST['currency'];
    $amount = $_POST['amount'];
    $prepaid = $_POST['prepaid'];
    $carriers_receipt = $_POST['carriers_receipt'];
    $place_of_issue = $_POST['place_of_issue'];
    $number_sequence = $_POST['number_sequence'];
    $date_of_issue = $_POST['date_of_issue'];
    $declared_value = $_POST['declared_value'];
    $shipped_on_board_date = $_POST['shipped_on_board_date'];
    // Process other freight & charges fields as needed

    // Process Add Shipment Details
    $quantity = $_POST['quantity'];
    $weight = $_POST['weight'];
    $description = $_POST['description'];
    $marks_numbers = $_POST['marks_numbers'];
    $container_seal_number = $_POST['container_seal_number'];
    // Process other shipment details fields as needed

    // Process Add Terms and Conditions
    $terms = $_POST['terms'];
    // Process other terms fields as needed

    // Redirect to a success page or display a success message
    header("Location: success.php");
    exit();
}
?>
