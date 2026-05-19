<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
$db = getDbConnection();

$type = $_GET['type'] ?? '';
$validTypes = ['customer', 'shipping_line', 'local_agent', 'port', 'vessel'];

if (!in_array($type, $validTypes)) {
    header('Location: create.php');
    exit();
}

$title = '';
$formContent = '';

switch ($type) {
    case 'customer':
        $title = 'إضافة عميل جديد';
        $formContent = '        
            <div class="mb-3">
                <label for="name" class="form-label">اسم العميل *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">العنوان</label>
                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tax_number" class="form-label">الرقم الضريبي</label>
                        <input type="text" class="form-control" id="tax_number" name="tax_number">
                    </div>
                </div>
            </div>
            <input type="hidden" name="type" value="customer">';
        break;
        
    case 'shipping_line':
        $title = 'إضافة خط شحن جديد';
        $formContent = '        
            <div class="mb-3">
                <label for="name" class="form-label">اسم خط الشحن *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <input type="hidden" name="type" value="shipping_line">';
        break;
        
    case 'local_agent':
        $title = 'إضافة وكيل جديد';
        $formContent = '
            <div class="mb-3">
                <label for="name" class="form-label">اسم الوكيل *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">العنوان</label>
                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
            </div>
            <input type="hidden" name="type" value="local_agent">';
        break;
        
    case 'port':
        $title = 'إضافة ميناء جديد';
        $formContent = '
            <div class="mb-3">
                <label for="name" class="form-label">اسم الميناء *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">الكود</label>
                        <input type="text" class="form-control" id="code" name="code">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="country" class="form-label">الدولة</label>
                        <input type="text" class="form-control" id="country" name="country" value="Unknown">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">نوع الميناء</label>
                        <select class="form-select" id="type" name="type">
                            <option value="seaport">ميناء بحري</option>
                            <option value="dry_port">ميناء جاف</option>
                            <option value="airport">مطار</option>
                        </select>
                    </div>
                </div>
            </div>';
        break;
        
    case 'vessel':
        $title = 'إضافة سفينة جديدة';
        
        // Get shipping lines for dropdown
        $shippingLines = [];
        $result = $db->query("SELECT id, name FROM shipping_lines ORDER BY name");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $shippingLines[] = $row;
            }
        }
        
        $shippingLinesOptions = '<option value="">-- اختر --</option>';
        foreach ($shippingLines as $line) {
            $shippingLinesOptions .= sprintf('<option value="%d">%s</option>', $line['id'], htmlspecialchars($line['name']));
        }
        
        $formContent = '
            <div class="mb-3">
                <label for="name" class="form-label">اسم السفينة *</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="imo_number" class="form-label">رقم IMO</label>
                        <input type="text" class="form-control" id="imo_number" name="imo_number">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="shipping_line_id" class="form-label">خط الشحن</label>
                        <select class="form-select" id="shipping_line_id" name="shipping_line_id">
                            ' . $shippingLinesOptions . '
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" name="type" value="vessel">';
}

// Check database connection
if ($db->connect_error) {
    die("<div class='alert alert-danger'>فشل الاتصال بقاعدة البيانات: " . $db->connect_error . "</div>");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Log the POST data for debugging
        error_log('Form submission data: ' . print_r($_POST, true));
        
        switch ($type) {
            case 'customer':
                // Prepare data for the API call
                $postData = [
                    'name' => $_POST['name'],
                    'type' => 'customer',
                    'address' => $_POST['address'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'tax_number' => $_POST['tax_number'] ?? ''
                ];

                // Initialize cURL session
                $ch = curl_init();
                
                // Set cURL options (call controller directly; router does not handle page=ajax)
                curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/shipping/v2/controllers/AjaxController.php?action=add_company');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                
                // Execute cURL request
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);
                
                if ($result === false) {
                    throw new Exception('فشل في الاتصال بالخادم: ' . $error);
                }
                
                $response = json_decode($result, true);
                
                // If this is an AJAX request, always return JSON
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    if ($response && !empty($response['success'])) {
                        echo json_encode([
                            'success' => true,
                            'message' => $response['message'] ?? 'تمت الإضافة بنجاح',
                            'id' => $response['id'] ?? null,
                            'name' => $response['name'] ?? ($postData['name'] ?? ''),
                            'itemType' => 'customer'
                        ]);
                    } else {
                        $errorMsg = $response['message'] ?? 'حدث خطأ غير معروف';
                        echo json_encode(['success' => false, 'message' => 'فشل في إضافة العميل: ' . $errorMsg]);
                    }
                    exit();
                }

                // Non-AJAX (popup submitted without fetch) -> notify parent and close
                if ($response && !empty($response['success'])) {
                    echo "<script>
                        window.opener && window.opener.postMessage({
                            type: 'newItemAdded',
                            id: '" . ($response['id'] ?? '') . "',
                            name: " . json_encode($response['name'] ?? $postData['name']) . ",
                            itemType: 'customer'
                        }, '*');
                        window.close();
                    </script>";
                    exit();
                } else {
                    $errorMsg = $response['message'] ?? 'حدث خطأ غير معروف';
                    throw new Exception('فشل في إضافة العميل: ' . $errorMsg);
                }
                
            case 'shipping_line':
                $stmt = $db->prepare("INSERT INTO shipping_lines (name) VALUES (?)");
                $stmt->bind_param("s", $_POST['name']);
                break;
                
            case 'local_agent':
                // Prepare data for the API call via AjaxController (same as customer path)
                $postData = [
                    'name' => $_POST['name'],
                    'type' => 'local_agent',
                    'address' => $_POST['address'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'tax_number' => $_POST['tax_number'] ?? ''
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/shipping/v2/controllers/AjaxController.php?action=add_company');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($result === false) {
                    throw new Exception('فشل في الاتصال بالخادم: ' . $error);
                }

                $response = json_decode($result, true);
                $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    if ($response && !empty($response['success'])) {
                        echo json_encode([
                            'success' => true,
                            'message' => $response['message'] ?? 'تمت الإضافة بنجاح',
                            'id' => $response['id'] ?? null,
                            'name' => $response['name'] ?? ($postData['name'] ?? ''),
                            'itemType' => 'local_agent'
                        ]);
                    } else {
                        $errorMsg = $response['message'] ?? 'حدث خطأ غير معروف';
                        echo json_encode(['success' => false, 'message' => 'فشل في إضافة الوكيل: ' . $errorMsg]);
                    }
                    exit();
                }

                if ($response && !empty($response['success'])) {
                    echo "<script>
                        window.opener && window.opener.postMessage({
                            type: 'newItemAdded',
                            id: '" . ($response['id'] ?? '') . "',
                            name: " . json_encode($response['name'] ?? $postData['name']) . ",
                            itemType: 'local_agent'
                        }, '*');
                        window.close();
                    </script>";
                    exit();
                } else {
                    $errorMsg = $response['message'] ?? 'حدث خطأ غير معروف';
                    throw new Exception('فشل في إضافة الوكيل: ' . $errorMsg);
                }
                // ensure we don't fall through to common $stmt->execute()
                break;
                
            case 'port':
                $code = !empty($_POST['code']) ? $_POST['code'] : strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $_POST['name']), 0, 3));
                $country = $_POST['country'] ?? 'Unknown';
                $portType = $_POST['type'] ?? 'seaport';
                
                $stmt = $db->prepare("INSERT INTO ports (name, code, country, type) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $_POST['name'], $code, $country, $portType);
                break;
                
            case 'vessel':
                try {
                    // Get and validate input
                    $vesselName = trim($_POST['name'] ?? '');
                    if (empty($vesselName)) {
                        throw new Exception('اسم السفينة مطلوب');
                    }
                    
                    $shippingLineId = !empty($_POST['shipping_line_id']) ? (int)$_POST['shipping_line_id'] : null;
                    $imoNumber = !empty($_POST['imo_number']) ? trim($_POST['imo_number']) : null;
                    
                    // Debug log
                    error_log("Vessel Data - Name: $vesselName, IMO: " . ($imoNumber ?? 'NULL') . ", Shipping Line ID: " . ($shippingLineId ?? 'NULL'));
                    
                    // Check if shipping line exists if provided
                    if ($shippingLineId) {
                        $check = $db->query("SELECT id FROM shipping_lines WHERE id = $shippingLineId");
                        if ($check === false) {
                            throw new Exception('خطأ في التحقق من خط الشحن: ' . $db->error);
                        }
                        if ($check->num_rows === 0) {
                            throw new Exception('خطأ: خط الشحن المحدد غير موجود');
                        }
                    }
                    
                    // Prepare and execute the insert
                    $stmt = $db->prepare("INSERT INTO vessels (name, imo_number, shipping_line_id) VALUES (?, ?, ?)");
                    if ($stmt === false) {
                        throw new Exception('خطأ في إعداد الاستعلام: ' . $db->error);
                    }
                    
                    // Ensure all values are passed by value, not by reference
                    $vesselNameValue = $vesselName;
                    $imoNumberValue = $imoNumber;
                    $shippingLineIdValue = $shippingLineId;
                    
                    $bindResult = $stmt->bind_param("ssi", $vesselNameValue, $imoNumberValue, $shippingLineIdValue);
                    if ($bindResult === false) {
                        throw new Exception('خطأ في ربط المعلمات: ' . $stmt->error);
                    }
                    
                    $executeResult = $stmt->execute();
                    if ($executeResult === false) {
                        throw new Exception('خطأ في تنفيذ الاستعلام: ' . $stmt->error . ' (Error Code: ' . $stmt->errno . ')');
                    }
                    
                    // Set success response
                    $response = [
                        'success' => true,
                        'message' => 'تمت إضافة السفينة بنجاح',
                        'id' => $db->insert_id,
                        'name' => $vesselName,
                        'type' => 'vessel'
                    ];
                    
                    // If this is an AJAX request, return JSON response
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode($response);
                        exit();
                    }
                    
                    // If not AJAX, set success message and show form again
                    $successMessage = $response['message'];
                    
                } catch (Exception $e) {
                    // Log the detailed error
                    error_log("Vessel Creation Error: " . $e->getMessage());
                    throw $e; // Re-throw to be caught by the outer try-catch
                }
                
                // Skip the outer execute() for vessel type
                break;
        }
        
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'تمت الإضافة بنجاح',
                'id' => $db->insert_id,
                'name' => $_POST['name'],
                'itemType' => $type  // Add itemType to identify the type of item added
            ];
            
            // If this is an AJAX request, return JSON response
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            
            // If not AJAX, set success message and show form again
            $successMessage = $response['message'];
        } else {
            $errorMsg = 'فشل في إضافة البيانات: ' . $db->error . '\n';
            $errorMsg .= 'Query: ' . $stmt->error . '\n';
            $errorMsg .= 'POST data: ' . print_r($_POST, true) . '\n';
            error_log($errorMsg);
            throw new Exception('حدث خطأ غير متوقع. يرجى مراجعة سجل الأخطاء لمزيد من التفاصيل.');
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        error_log('Exception: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
        
        // If this is an AJAX request, return JSON error
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit();
        }
    }
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><?php echo $title; ?></h4>
            <a href="create.php" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success">
                    <?php echo $successMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form id="addForm" method="post" class="needs-validation" novalidate>
                <?php echo $formContent; ?>
                
                <div class="text-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                        <i class="fas fa-times"></i> إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('addForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    const formData = new FormData(this);
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الحفظ...';
    
    // Submit form via AJAX
    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // If this is opened in a popup, update the parent window
            if (window.opener) {
                // Send message to parent window
                window.opener.postMessage({
                    type: 'newItemAdded',
                    id: data.id,
                    name: data.name,
                    itemType: '<?php echo $type; ?>'
                }, '*');
                window.close();
            } else {
                // If not in a popup, show success message and reset form
                showAlert('success', data.message);
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    this.reset();
                    this.classList.remove('was-validated');
                }
            }
        } else {
            showAlert('danger', data.message || 'حدث خطأ أثناء الحفظ');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'حدث خطأ غير متوقع');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 5000);
}
</script>
