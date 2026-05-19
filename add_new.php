<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Add New</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add New</h1>
        <form action="process_add_new.php" method="post">
            <fieldset>
                <legend>Shipment ID</legend>
                <label for="shipment_id">معرف الشحنة (Shipment ID):</label>
                <input type="text" id="shipment_id" name="shipment_id" required>
            </fieldset>
            <fieldset>
                <legend>Add Company</legend>
                <label for="company_name">اسم الشركة (Company Name):</label>
                <input type="text" id="company_name" name="company_name" required>
                <label for="company_address">عنوان الشركة (Company Address):</label>
                <input type="text" id="company_address" name="company_address" required>
                <label for="company_phone">هاتف الشركة (Company Phone):</label>
                <input type="text" id="company_phone" name="company_phone" required>
                <label for="company_email">البريد الإلكتروني للشركة (Company Email):</label>
                <input type="text" id="company_email" name="company_email" required>
            </fieldset>
            <fieldset>
                <legend>Add Broker</legend>
                <label for="broker_name">اسم الوسيط (Broker Name):</label>
                <input type="text" id="broker_name" name="broker_name" required>
                <label for="broker_address">عنوان الوسيط (Broker Address):</label>
                <input type="text" id="broker_address" name="broker_address" required>
                <label for="broker_phone">هاتف الوسيط (Broker Phone):</label>
                <input type="text" id="broker_phone" name="broker_phone" required>
                <label for="broker_email">البريد الإلكتروني للوسيط (Broker Email):</label>
                <input type="text" id="broker_email" name="broker_email" required>
            </fieldset>
            <fieldset>
                <legend>Shipment Form</legend>
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
            </fieldset>
            <fieldset>
                <legend>Add Bill of Lading</legend>
                <label for="bill_of_lading_shipment_id">معرف الشحنة (Shipment ID):</label>
                <input type="text" id="bill_of_lading_shipment_id" name="bill_of_lading_shipment_id" required>
                <label for="bill_of_lading_number">رقم الفاتورة (Bill Number):</label>
                <input type="text" id="bill_of_lading_number" name="bill_of_lading_number" required>
                <label for="issue_date">تاريخ الإصدار (Issue Date):</label>
                <input type="date" id="issue_date" name="issue_date" required>
            </fieldset>
            <fieldset>
                <legend>Add Freight & Charges</legend>
                <label for="bill_number">رقم الفاتورة (Bill Number):</label>
                <input type="text" id="bill_number" name="bill_number" required>
                <label for="rate">المعدل (Rate):</label>
                <input type="text" id="rate" name="rate" required>
                <label for="currency">العملة (Currency):</label>
                <input type="text" id="currency" name="currency" required>
                <label for="amount">المبلغ (Amount):</label>
                <input type="text" id="amount" name="amount" required>
                <label for="prepaid">مدفوع مسبقاً (Prepaid):</label>
                <input type="text" id="prepaid" name="prepaid" required>
                <label for="carriers_receipt">إيصال الناقل (Carriers Receipt):</label>
                <input type="text" id="carriers_receipt" name="carriers_receipt" required>
                <label for="place_of_issue">مكان الإصدار (Place of Issue):</label>
                <input type="text" id="place_of_issue" name="place_of_issue" required>
                <label for="number_sequence">رقم وتسلسل الأصلية (Number & Sequence of Original B/L):</label>
                <input type="text" id="number_sequence" name="number_sequence" required>
                <label for="date_of_issue">تاريخ الإصدار (Date of Issue):</label>
                <input type="date" id="date_of_issue" name="date_of_issue" required>
                <label for="declared_value">القيمة المعلنة (Declared Value):</label>
                <input type="text" id="declared_value" name="declared_value" required>
                <label for="shipped_on_board_date">تاريخ الشحن على متن السفينة (Shipped on Board Date):</label>
                <input type="date" id="shipped_on_board_date" name="shipped_on_board_date" required>
            </fieldset>
            <fieldset>
                <legend>Add Shipment Details</legend>
                <label for="shipment_id">معرف الشحنة (Shipment ID):</label>
                <input type="text" id="shipment_id" name="shipment_id" required>
                <label for="quantity">الكمية (Quantity):</label>
                <input type="text" id="quantity" name="quantity" required>
                <label for="weight">الوزن (Weight):</label>
                <input type="text" id="weight" name="weight" required>
                <label for="description">الوصف (Description):</label>
                <textarea id="description" name="description" rows="4" cols="50" required></textarea>
                <label for="marks_numbers">العلامات والأرقام (Marks and Numbers):</label>
                <textarea id="marks_numbers" name="marks_numbers" rows="4" cols="50" required></textarea>
                <label for="container_seal_number">رقم الحاوية/الختم (Container No./Seal No.):</label>
                <input type="text" id="container_seal_number" name="container_seal_number" required>
            </fieldset>
            <fieldset>
                <legend>Add Terms and Conditions</legend>
                <label for="terms">الشروط والأحكام (Terms and Conditions):</label>
                <textarea id="terms" name="terms" rows="10" cols="50" required></textarea>
            </fieldset>
            <input type="submit" value="Submit">
        </form>
    </div>
    <button class="floating-home-button" onclick="window.location.href='index.php'">🏠</button>
</body>
</html>
