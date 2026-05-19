<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Search Bill of Lading</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Search Bill of Lading</h1>
        <form action="view_bill_of_lading.php" method="GET" class="mb-4">
            <div class="form-group">
                <label for="bill_number">Bill Number:</label>
                <input type="text" id="bill_number" name="bill_number" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <h2 class="text-center mb-4">Saved Bills of Lading</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Issue Date</th>
                    <th>Shipment ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_config.php';

                // Fetch all bills of lading from the database
                $billsQuery = "SELECT * FROM bill_of_lading";
                $billsResult = $conn->query($billsQuery);

                if ($billsResult->num_rows > 0) {
                    while ($row = $billsResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row["bill_number"] . '</td>';
                        echo '<td>' . $row["issue_date"] . '</td>';
                        echo '<td>' . $row["shipment_id"] . '</td>';
                        echo '<td><a href="view_bill_of_lading.php?bill_number=' . $row["bill_number"] . '" class="btn btn-info btn-sm">View</a></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">No bills of lading found</td></tr>';
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
