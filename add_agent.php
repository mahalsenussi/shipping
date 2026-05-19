<?php
include 'db_config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Shipping Agent</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Add Shipping Agent</h2>
    <form method="post" action="process_add_agent.php">
        <div class="form-group">
            <label>Agent Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Contact Info</label>
            <input type="text" name="contact_info" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Agent</button>
    </form>
</div>
</body>
</html>
