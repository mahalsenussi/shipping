<?php
include 'db_config.php';

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

// Fetch request info
$request = $conn->query("SELECT * FROM requests WHERE id=$request_id")->fetch_assoc();

if (!$request) {
    echo "<div class='alert alert-danger'>Request not found.</div>";
    exit;
}

// Handle arrival approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['declare_arrival'])) {
    $conn->query("UPDATE requests SET status='arrived' WHERE id=$request_id");
    header("Location: arrival_dashboard.php?request_id=$request_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Arrival Approval</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Arrival Approval for Request #<?php echo $request['id']; ?></h2>
    <p>
        <strong>Customer:</strong> <?php echo htmlspecialchars($request['customer_name']); ?><br>
        <strong>Cargo Type:</strong> <?php echo htmlspecialchars($request['cargo_type']); ?><br>
        <strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?>
    </p>
    <form method="post">
        <button type="submit" name="declare_arrival" class="btn btn-success">Declare Arrival</button>
    </form>
</div>
</body>
</html>
