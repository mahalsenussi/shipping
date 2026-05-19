<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Welcome to the Shipping Platform</h1>
        <div class="list-group">
            <h2>Company and Broker Management</h2>
            <a href="add_company.php" class="list-group-item list-group-item-action">Add Company</a>
            <a href="add_broker.php" class="list-group-item list-group-item-action">Add Broker</a>

            <h2>Shipment Management</h2>
            <a href="shipment_form.php" class="list-group-item list-group-item-action">Shipment Form</a>
            <a href="search_shipment.php" class="list-group-item list-group-item-action">Search Shipment</a>

            <h2>Bill of Lading Management</h2>
            <a href="add_bill_of_lading.php" class="list-group-item list-group-item-action">Add Bill of Lading</a>
            <a href="search_bill_of_lading.php" class="list-group-item list-group-item-action">Search Bill of Lading</a>

            <h2>Additional Information</h2>
            <a href="add_terms.php" class="list-group-item list-group-item-action">Add Terms and Conditions</a>
            <a href="add_freight_charges.php" class="list-group-item list-group-item-action">Add Freight & Charges</a>
            <a href="add_shipment_details.php" class="list-group-item list-group-item-action">Add Shipment Details</a>
        </div>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
