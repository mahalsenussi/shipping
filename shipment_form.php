<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Shipment Form</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Shipment Form</h1>
        <form action="process.php" method="POST">
            <label for="container_number">رقم الحاوية (Container Number):</label>
            <input type="text" id="container_number" name="container_number" required>

            <label for="company">الشركة (Company):</label>
            <select id="company" name="company" required>
                <?php
                include 'db_config.php';

                // Fetch companies from the database
                $companyQuery = "SELECT * FROM company";
                $companyResult = $conn->query($companyQuery);

                // Display dropdown options for companies
                if ($companyResult->num_rows > 0) {
                    while ($row = $companyResult->fetch_assoc()) {
                        echo '<option value="' . $row["id"] . '">' . $row["company_name"] . '</option>';
                    }
                } else {
                    echo '<option value="">No companies available</option>';
                }

                // Close the company query
                $companyResult->close();
                ?>
            </select>

            <label for="contact_person">الشخص الذي يمكن الاتصال به (Contact Person):</label>
            <input type="text" id="contact_person" name="contact_person" required>

            <label for="container_size">حجم الحاوية (Container Size):</label>
            <input type="text" id="container_size" name="container_size" required>

            <label for="customs_broker">وكيل الجمارك (Customs Broker):</label>
            <select id="customs_broker" name="customs_broker" required>
                <?php
                // Fetch brokers from the database
                $brokerQuery = "SELECT * FROM broker";
                $brokerResult = $conn->query($brokerQuery);

                // Display dropdown options for brokers
                if ($brokerResult->num_rows > 0) {
                    while ($row = $brokerResult->fetch_assoc()) {
                        echo '<option value="' . $row["id"] . '">' . $row["broker_name"] . '</option>';
                    }
                } else {
                    echo '<option value="">No brokers available</option>';
                }

                // Close the broker query and the database connection
                $brokerResult->close();
                $conn->close();
                ?>
            </select>

            <label for="date">التاريخ (Date):</label>
            <input type="date" id="date" name="date" required>

            <label for="shipping_company">شركة الشحن (Shipping Company):</label>
            <input type="text" id="shipping_company" name="shipping_company" required>

            <label for="shipping_port">ميناء الشحن (Shipping Port):</label>
            <input type="text" id="shipping_port" name="shipping_port" required>

            <label for="arrival_port">ميناء الوصول (Arrival Port):</label>
            <input type="text" id="arrival_port" name="arrival_port" required>

            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>
