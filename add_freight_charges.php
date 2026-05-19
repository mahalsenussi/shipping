<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add Freight & Charges</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add Freight & Charges</h1>
        <form action="process_add_freight_charges.php" method="POST">
            <label for="bill_number">Bill Number:</label>
            <input type="text" id="bill_number" name="bill_number" required>

            <label for="rate">Rate:</label>
            <input type="text" id="rate" name="rate" required>

            <label for="currency">Currency:</label>
            <input type="text" id="currency" name="currency" required>

            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>

            <label for="prepaid">Prepaid:</label>
            <input type="text" id="prepaid" name="prepaid" required>

            <label for="carriers_receipt">Carriers Receipt:</label>
            <input type="text" id="carriers_receipt" name="carriers_receipt" required>

            <label for="place_of_issue">Place of Issue:</label>
            <input type="text" id="place_of_issue" name="place_of_issue" required>

            <label for="number_sequence">Number & Sequence of Original B/L:</label>
            <input type="text" id="number_sequence" name="number_sequence" required>

            <label for="date_of_issue">Date of Issue:</label>
            <input type="date" id="date_of_issue" name="date_of_issue" required>

            <label for="declared_value">Declared Value:</label>
            <input type="text" id="declared_value" name="declared_value" required>

            <label for="shipped_on_board_date">Shipped on Board Date:</label>
            <input type="date" id="shipped_on_board_date" name="shipped_on_board_date" required>

            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>
