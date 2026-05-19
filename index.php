<?php
include 'db_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container mt-4">
    <!-- Quick Actions -->
    <div class="mb-4">
        <a href="add_request.php" class="btn btn-primary">Create New Request</a>
        <a href="add_company.php" class="btn btn-secondary">Add Company</a>
        <a href="add_agent.php" class="btn btn-info">Add Shipping Agent</a>
        <a href="add_naval_line.php" class="btn btn-dark">Add Naval Line</a>
        <a href="add_broker.php" class="btn btn-outline-secondary">Add Broker</a>
        <a href="add_bill_of_lading.php" class="btn btn-outline-primary">Add Bill of Lading</a>
        <a href="search_bill_of_lading.php" class="btn btn-outline-success">Search Bill of Lading</a>
    </div>

    <!-- 1. Requests Section -->
    <h2>1. Requests</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Request #</th>
                <th>Customer Name</th>
                <th>Address</th>
                <th>Cargo Type</th>
                <th>Width</th>
                <th>Arrival Port</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $req = $conn->query("SELECT * FROM requests");
        while ($row = $req->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['address']}</td>
                <td>{$row['cargo_type']}</td>
                <td>{$row['width']}</td>
                <td>{$row['arrival_port']}</td>
                <td>{$row['status']}</td>
                <td>";
            if ($row['status'] === 'arrived') {
                echo "<a href='arrival_dashboard.php?request_id={$row['id']}' class='btn btn-info btn-sm'>Arrival Dashboard</a>";
            } else {
                echo "<a href='?select_agent={$row['id']}' class='btn btn-primary btn-sm'>Process</a>";
                if ($row['status'] === 'approved') {
                    echo " <a href='arrival_approval.php?request_id={$row['id']}' class='btn btn-success btn-sm'>Approve/Declare Arrival</a>";
                }
            }
            echo "</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- 2. Shipping Agent & Naval Line Section -->
    <h2>2. Shipping Agent & Naval Line</h2>
    <?php if (isset($_GET['select_agent'])): 
        $request_id = intval($_GET['select_agent']);
        $req = $conn->query("SELECT * FROM requests WHERE id=$request_id")->fetch_assoc();
    ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5>Request #<?php echo $req['id']; ?> - <?php echo $req['customer_name']; ?></h5>
                <form method="post" action="process_agent_selection.php">
                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                    <div class="form-group">
                        <label>Shipping Agent</label>
                        <select name="agent_id" class="form-control" required>
                            <?php
                            $agents = $conn->query("SELECT * FROM agents");
                            while ($a = $agents->fetch_assoc()) {
                                echo "<option value='{$a['id']}'>{$a['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Naval Line</label>
                        <select name="naval_line_id" class="form-control">
                            <option value="">Select existing</option>
                            <?php
                            $lines = $conn->query("SELECT * FROM naval_lines");
                            while ($l = $lines->fetch_assoc()) {
                                echo "<option value='{$l['id']}'>{$l['name']}</option>";
                            }
                            ?>
                        </select>
                        <small>Or <a href="add_naval_line.php">create new naval line</a></small>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Print Receipt & Wait for Approval</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. Post-Arrival Process Section -->
    <h2>3. Post-Arrival Process</h2>
    <?php
    $arrived = $conn->query("SELECT * FROM requests WHERE status='arrived'");
    if ($arrived->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Request #</th>
                    <th>Customer Name</th>
                    <th>Cargo Type</th>
                    <th>Arrival Port</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $arrived->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['cargo_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_port']); ?></td>
                    <td>
                        <a href="arrival_dashboard.php?request_id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Arrival Dashboard</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No shipments have arrived yet.</div>
    <?php endif; ?>
</div>
</body>
</html>
