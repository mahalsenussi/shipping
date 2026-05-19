<?php
include 'db_config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Add New Request</h2>
    <form method="post" action="process_add_request.php">
        <div class="form-group">
            <label>Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Cargo Type</label>
            <input type="text" name="cargo_type" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Width</label>
            <input type="text" name="width" class="form-control">
        </div>
        <div class="form-group">
            <label>Arrival Port</label>
            <input type="text" name="arrival_port" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Request</button>
    </form>
</div>
</body>
</html>
