<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>View Shipment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Shipment Details</h1>
        <?php
        include 'db_config.php';

        // Get the shipment ID from the request
        $shipmentId = $_GET['shipment_id'];

        // Fetch shipment details
        $sql = "SELECT * FROM shipments WHERE id = '$shipmentId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $shipment = $result->fetch_assoc();
            echo '<table class="table table-bordered">';
            echo '<tr><th>Shipment ID</th><td>' . $shipment["id"] . '</td></tr>';
            echo '<tr><th>Container Number</th><td>' . $shipment["container_number"] . '</td></tr>';
            echo '<tr><th>Company</th><td>' . $shipment["company_id"] . '</td></tr>';
            echo '<tr><th>Contact Person</th><td>' . $shipment["contact_person"] . '</td></tr>';
            echo '<tr><th>Container Size</th><td>' . $shipment["container_size"] . '</td></tr>';
            echo '<tr><th>Customs Broker</th><td>' . $shipment["broker_id"] . '</td></tr>';
            echo '<tr><th>Date</th><td>' . $shipment["date"] . '</td></tr>';
            echo '<tr><th>Shipping Company</th><td>' . $shipment["shipping_company"] . '</td></tr>';
            echo '<tr><th>Shipping Port</th><td>' . $shipment["shipping_port"] . '</td></tr>';
            echo '<tr><th>Arrival Port</th><td>' . $shipment["arrival_port"] . '</td></tr>';
            echo '</table>';
        } else {
            echo '<h2 class="text-center">No shipment found with the given ID.</h2>';
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
