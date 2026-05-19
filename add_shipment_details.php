<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add Shipment Details</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add Shipment Details</h1>
        <form action="process_add_shipment_details.php" method="POST">
            <label for="shipment_id">Shipment ID:</label>
            <input type="text" id="shipment_id" name="shipment_id" required>

            <label for="quantity">Quantity:</label>
            <input type="text" id="quantity" name="quantity" required>

            <label for="weight">Weight:</label>
            <input type="text" id="weight" name="weight" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" cols="50" required></textarea>

            <label for="marks_numbers">Marks and Numbers:</label>
            <textarea id="marks_numbers" name="marks_numbers" rows="4" cols="50" required></textarea>

            <label for="container_seal_number">Container No./Seal No.:</label>
            <input type="text" id="container_seal_number" name="container_seal_number" required>

            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>
