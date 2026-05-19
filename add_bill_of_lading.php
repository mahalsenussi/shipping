<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add Bill of Lading</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add Bill of Lading</h1>
        <form action="process_add_bill_of_lading.php" method="POST">
            <label for="shipment_id">Shipment ID:</label>
            <input type="text" id="shipment_id" name="shipment_id" required>

            <label for="bill_number">Bill Number:</label>
            <input type="text" id="bill_number" name="bill_number" required>

            <label for="issue_date">Issue Date:</label>
            <input type="date" id="issue_date" name="issue_date" required>

            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>
