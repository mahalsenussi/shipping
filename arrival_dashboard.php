<?php
include 'db_config.php';

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;
$request = $conn->query("SELECT * FROM requests WHERE id=$request_id")->fetch_assoc();

if (!$request) {
    echo "<div class='alert alert-danger'>Request not found.</div>";
    exit;
}

// You may want to fetch related shipment, manifest, and BOL info here
?>
<!DOCTYPE html>
<html>
<head>
    <title>Arrival Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Arrival Dashboard for Request #<?php echo $request['id']; ?></h2>
    <p>
        <strong>Customer:</strong> <?php echo htmlspecialchars($request['customer_name']); ?><br>
        <strong>Cargo Type:</strong> <?php echo htmlspecialchars($request['cargo_type']); ?><br>
        <strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?>
    </p>
    <div class="mb-3">
        <a href="generate_bill_of_lading.php?bill_number=<?php echo urlencode($request['id']); ?>" class="btn btn-primary" target="_blank">Print Bill of Lading</a>
        <a href="print_manifest.php?request_id=<?php echo $request['id']; ?>" class="btn btn-info" target="_blank">Print Manifest</a>
        <a href="print_delivery_order.php?request_id=<?php echo $request['id']; ?>" class="btn btn-success" target="_blank">Print Receipt</a>
    </div>
    <a href="index.php" class="btn btn-secondary">Back to Home</a>
</div>
</body>
</html>
