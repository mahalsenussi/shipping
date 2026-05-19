<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add Company</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add Company</h1>
        <form action="process_add_company.php" method="POST">
            <label for="company_name">Company Name:</label>
            <input type="text" id="company_name" name="company_name" required>

            <label for="company_address">Company Address:</label>
            <input type="text" id="company_address" name="company_address" required>

            <label for="company_phone">Company Phone:</label>
            <input type="text" id="company_phone" name="company_phone" required>

            <label for="company_email">Company Email:</label>
            <input type="text" id="company_email" name="company_email" required>

            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>