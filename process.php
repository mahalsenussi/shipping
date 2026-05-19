<!DOCTYPE html>
<html>
<head>
    <title>Shipment Form - Process</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <?php
        include 'db_config.php';

        // Shipment details
        $containerNumber = $_POST['container_number'];
        $companyId = $_POST['company'];
        $contactPerson = $_POST['contact_person'];
        $containerSize = $_POST['container_size'];
        $brokerId = $_POST['customs_broker'];
        $date = $_POST['date'];
        $shippingCompany = $_POST['shipping_company'];
        $shippingPort = $_POST['shipping_port'];
        $arrivalPort = $_POST['arrival_port'];

        // Prepare and execute the SQL statement
        $sql = "INSERT INTO shipments (container_number, company_id, contact_person, container_size, broker_id, date, shipping_company, shipping_port, arrival_port)
                VALUES ('$containerNumber', '$companyId', '$contactPerson', '$containerSize', '$brokerId', '$date', '$shippingCompany', '$shippingPort', '$arrivalPort')";

        if ($conn->query($sql) === TRUE) {
            echo "<h2>Shipment details stored successfully.</h2>";
        } else {
            echo "<h2>Error: " . $sql . "<br>" . $conn->error . "</h2>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
</body>
</html>