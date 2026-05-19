<?php
include 'db_config.php';

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

// Fetch companies for source selection
$companies = $conn->query("SELECT id, company_name FROM company");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shipment Manifest Input</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Enter Manifest & Delivery Order Info</h2>
    <form method="post" action="print_delivery_order.php">
        <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
        <div class="form-group">
            <label>Source Port</label>
            <input type="text" name="source_port" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Manifest Number</label>
            <input type="text" name="manifest_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Shipping Number</label>
            <input type="text" name="shipping_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Ship Name</label>
            <input type="text" name="ship_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Date of Arrival</label>
            <input type="date" name="date_of_arrival" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Number of Boxes</label>
            <input type="number" name="number_of_boxes" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Source (Company)</label>
            <select name="company_id" class="form-control" required>
                <?php while ($c = $companies->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo $c['company_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Print Delivery Order</button>
    </form>
</div>
</body>
</html>
