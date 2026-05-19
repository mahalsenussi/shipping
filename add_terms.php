<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add Terms and Conditions</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add Terms and Conditions</h1>
        <form action="process_add_terms.php" method="POST">
            <label for="terms">Terms and Conditions:</label>
            <textarea id="terms" name="terms" rows="10" cols="50" required></textarea>
            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>
