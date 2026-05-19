<?php
include 'db_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <!-- Action Buttons -->
    <div class="mb-4">
        <a href="add_request.php" class="btn btn-primary">Create New Request</a>
        <a href="add_agent.php" class="btn btn-secondary">Add Shipping Agent</a>
        <a href="add_naval_line.php" class="btn btn-info">Add Naval Line</a>
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Example: Fetch requests from a 'requests' table
        $req = $conn->query("SELECT * FROM requests");
        while ($row = $req->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['customer_name']}</td>
                <td>{$row['address']}</td>
                <td>{$row['cargo_type']}</td>
                <td>{$row['width']}</td>
                <td>{$row['arrival_port']}</td>
                <td>
                    <a href='?select_agent={$row['id']}' class='btn btn-primary btn-sm'>Process</a>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- 2. Shipping Agent & Naval Line Section -->
    <h2>2. Shipping Agent & Naval Line</h2>
    <?php if (isset($_GET['select_agent'])): 
        $request_id = intval($_GET['select_agent']);
        // Fetch request info for this id
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
                        <small>Or <a href="create_naval_line.php?request_id=<?php echo $req['id']; ?>">create new naval line</a></small>
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
    <div class="alert alert-info">Details and actions after arrival will be shown here.</div>
</div>
</body>
</html>
