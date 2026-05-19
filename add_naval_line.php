<?php
include 'db_config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Naval Line</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Add Naval Line</h2>
    <form method="post" action="process_add_naval_line.php">
        <div class="form-group">
            <label>Line Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Naval Line</button>
    </form>
</div>
</body>
</html>
