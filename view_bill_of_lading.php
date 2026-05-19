<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>View Bill of Lading</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Bill of Lading Details</h1>
        <?php
        include 'db_config.php';

        // Get the bill number from the request
        $billNumber = $_GET['bill_number'];

        // Fetch bill of lading details
        $sql = "SELECT * FROM bill_of_lading WHERE bill_number = '$billNumber'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $bill = $result->fetch_assoc();
            echo '<table class="table table-bordered">';
            echo '<tr><th>Bill Number</th><td>' . $bill["bill_number"] . '</td></tr>';
            echo '<tr><th>Issue Date</th><td>' . $bill["issue_date"] . '</td></tr>';
            echo '<tr><th>Shipment ID</th><td>' . $bill["shipment_id"] . '</td></tr>';
            echo '</table>';
            echo '<a href="generate_bill_of_lading.php?bill_number=' . $bill["bill_number"] . '" class="btn btn-primary">Generate PDF</a>';
        } else {
            echo '<h2 class="text-center">No bill of lading found with the given number.</h2>';
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
