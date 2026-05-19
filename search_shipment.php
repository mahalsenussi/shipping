<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Search Shipment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Search Shipment</h1>
        <form action="view_shipment.php" method="GET" class="mb-4">
            <div class="form-group">
                <label for="shipment_id">Shipment ID:</label>
                <input type="text" id="shipment_id" name="shipment_id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <h2 class="text-center mb-4">Saved Shipments</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Shipment ID</th>
                    <th>Container Number</th>
                    <th>Company</th>
                    <th>Contact Person</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_config.php';

                // Fetch all shipments from the database
                $shipmentsQuery = "SELECT * FROM shipments";
                $shipmentsResult = $conn->query($shipmentsQuery);

                if ($shipmentsResult->num_rows > 0) {
                    while ($row = $shipmentsResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row["id"] . '</td>';
                        echo '<td>' . $row["container_number"] . '</td>';
                        echo '<td>' . $row["company_id"] . '</td>';
                        echo '<td>' . $row["contact_person"] . '</td>';
                        echo '<td><a href="view_shipment.php?shipment_id=' . $row["id"] . '" class="btn btn-info btn-sm">View</a></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">No shipments found</td></tr>';
                }

                // Close the database connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
