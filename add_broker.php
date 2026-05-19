<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add Broker</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add Broker</h1>
        <form action="process_add_broker.php" method="POST">
            <label for="broker_name">Broker Name:</label>
            <input type="text" id="broker_name" name="broker_name" required>

            <label for="broker_address">Broker Address:</label>
            <input type="text" id="broker_address" name="broker_address" required>

            <label for="broker_phone">Broker Phone:</label>
            <input type="text" id="broker_phone" name="broker_phone" required>

            <label for="broker_email">Broker Email:</label>
            <input type="text" id="broker_email" name="broker_email" required>

            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>