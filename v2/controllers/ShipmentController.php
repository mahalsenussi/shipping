<?php
class ShipmentController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = getDbConnection();
    }

    // Edit form
    public function edit($id) {
        // Load shipment header
        $stmt = $this->db->prepare("SELECT * FROM shipments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $shipment = $stmt->get_result()->fetch_assoc();
        if (!$shipment) { die('Shipment not found'); }

        // Load dropdown data
        $customers = $this->getCompanies('customer');
        $agents = $this->getCompanies('local_agent');
        $shipping_lines = $this->getCompanies('shipping_line');
        $ports = $this->getPorts();
        $vessels = $this->getVessels();
        $containerTypes = $this->getContainerTypes();

        // Containers
        $containers = [];
        $stmt = $this->db->prepare("SELECT c.*, ct.code as container_type_code FROM containers c LEFT JOIN container_types ct ON c.container_type_id = ct.id WHERE shipment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) { $containers[] = $row; }

        // Cargo
        $cargoItems = [];
        $stmt = $this->db->prepare("SELECT * FROM cargo_items WHERE shipment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) { $cargoItems[] = $row; }

        include __DIR__ . '/../views/shipments/edit.php';
    }

    // Update shipment and replace its containers and cargo
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=shipments&action=edit&id=' . (int)$id);
            exit();
        }

        $this->db->begin_transaction();
        try {
            // Normalize header inputs
            $reference = $_POST['reference_number'];
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $shippingType = $_POST['shipping_type'] ?? 'naval';
            $shippingLineId = !empty($_POST['shipping_line_id']) ? (int)$_POST['shipping_line_id'] : null;
            $localAgentId = !empty($_POST['local_agent_id']) ? (int)$_POST['local_agent_id'] : null;
            $originPortId = !empty($_POST['origin_port_id']) ? (int)$_POST['origin_port_id'] : null;
            $destinationPortId = !empty($_POST['destination_port_id']) ? (int)$_POST['destination_port_id'] : null;
            $vesselId = !empty($_POST['vessel_id']) ? (int)$_POST['vessel_id'] : null;
            $voyageNumber = $_POST['voyage_number'] !== '' ? $_POST['voyage_number'] : null;
            $estDep = $_POST['estimated_departure_date'] !== '' ? $_POST['estimated_departure_date'] : null;
            $estArr = $_POST['estimated_arrival_date'] !== '' ? $_POST['estimated_arrival_date'] : null;

            // Update shipments header
            $stmt = $this->db->prepare("UPDATE shipments SET reference_number=?, customer_id=?, shipping_type=?, shipping_line_id=?, local_agent_id=?, origin_port_id=?, destination_port_id=?, vessel_id=?, voyage_number=?, estimated_departure_date=?, estimated_arrival_date=? WHERE id = ?");
            $stmt->bind_param("sissiiiisssi", $reference, $customerId, $shippingType, $shippingLineId, $localAgentId, $originPortId, $destinationPortId, $vesselId, $voyageNumber, $estDep, $estArr, $id);
            $stmt->execute();

            // Clear old containers and cargo
            $this->db->query("DELETE FROM cargo_items WHERE shipment_id = " . (int)$id);
            $this->db->query("DELETE FROM containers WHERE shipment_id = " . (int)$id);

            // Re-insert containers
            $containerIdByNumber = [];
            if (!empty($_POST['containers'])) {
                $stmtC = $this->db->prepare("INSERT INTO containers (shipment_id, container_number, container_type_id, seal_number, weight_kg, volume_cbm) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($_POST['containers'] as $container) {
                    $containerNumber = $container['container_number'] ?? null;
                    if (empty($containerNumber)) { continue; }
                    $containerTypeId = !empty($container['container_type_id']) ? (int)$container['container_type_id'] : null;
                    $sealNumber = $container['seal_number'] ?? null;
                    $weightKg = (isset($container['weight_kg']) && $container['weight_kg'] !== '') ? (float)$container['weight_kg'] : null;
                    $volumeCbm = (isset($container['volume_cbm']) && $container['volume_cbm'] !== '') ? (float)$container['volume_cbm'] : null;
                    $stmtC->bind_param("isisdd", $id, $containerNumber, $containerTypeId, $sealNumber, $weightKg, $volumeCbm);
                    $stmtC->execute();
                    $containerIdByNumber[$containerNumber] = $this->db->insert_id;
                }
            }

            // Re-insert cargo items
            if (!empty($_POST['cargo_items'])) {
                $stmtI = $this->db->prepare("INSERT INTO cargo_items (shipment_id, description, hs_code, quantity, unit_type, weight_kg, volume_cbm, container_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($_POST['cargo_items'] as $item) {
                    $containerId = null;
                    if (!empty($item['container_number_ref'])) {
                        $ref = $item['container_number_ref'];
                        if (isset($containerIdByNumber[$ref])) {
                            $containerId = (int)$containerIdByNumber[$ref];
                        } else {
                            $lookup = $this->db->prepare("SELECT id FROM containers WHERE shipment_id = ? AND container_number = ? LIMIT 1");
                            $lookup->bind_param("is", $id, $ref);
                            $lookup->execute();
                            $res = $lookup->get_result();
                            if ($row = $res->fetch_assoc()) { $containerId = (int)$row['id']; }
                        }
                    }

                    $desc = isset($item['description']) ? $item['description'] : '';
                    $hs = isset($item['hs_code']) ? $item['hs_code'] : null;
                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                    $unit = isset($item['unit_type']) ? $item['unit_type'] : 'PCS';
                    $w = (isset($item['weight_kg']) && $item['weight_kg'] !== '') ? (float)$item['weight_kg'] : null;
                    $vol = (isset($item['volume_cbm']) && $item['volume_cbm'] !== '') ? (float)$item['volume_cbm'] : null;

                    $stmtI->bind_param("issisddi", $id, $desc, $hs, $qty, $unit, $w, $vol, $containerId);
                    $stmtI->execute();
                }
            }

            $this->db->commit();
            header('Location: ?page=shipments&action=show&id=' . (int)$id);
            exit();
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Shipment update error: ' . $e->getMessage());
            $_SESSION['error'] = 'حدث خطأ أثناء تحديث الشحنة';
            header('Location: ?page=shipments&action=edit&id=' . (int)$id);
            exit();
        }
    }

    // List all shipments
    public function index() {
        $allowed = ['draft','quotation_sent','approved','in_transit','arrived','customs_cleared','delivered','cancelled'];
        $where = '';
        if (!empty($_GET['status']) && in_array($_GET['status'], $allowed)) {
            $status = $_GET['status'];
            $where = " WHERE s.status = '" . $this->db->real_escape_string($status) . "' ";
        }

        $sql = "SELECT s.*, c.name as company_name, p1.name as origin_port, p2.name as destination_port 
                FROM shipments s 
                LEFT JOIN companies c ON s.customer_id = c.id 
                LEFT JOIN ports p1 ON s.origin_port_id = p1.id 
                LEFT JOIN ports p2 ON s.destination_port_id = p2.id "
                . $where .
                " ORDER BY s.created_at DESC";

        $result = $this->db->query($sql);
        $shipments = [];
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $shipments[] = $row;
            }
        }
        
        include __DIR__ . '/../views/shipments/index.php';
    }

    // Show create shipment form
    public function create() {
        // Get companies for dropdown
        $customers = $this->getCompanies('customer');
        $shipping_lines = $this->getCompanies('shipping_line');
        $agents = $this->getCompanies('local_agent');
        $ports = $this->getPorts();
        $vessels = $this->getVessels();
        $containerTypes = $this->getContainerTypes();

        include __DIR__ . '/../views/shipments/create.php';
    }

    /**
     * Get container types for dropdown
     */
    private function getContainerTypes() {
        $result = $this->db->query("SELECT id, code, description FROM container_types ORDER BY code");
        $types = [];
        while ($row = $result->fetch_assoc()) {
            $types[] = $row;
        }
        return $types;
    }

    /**
     * Get companies by type
     */
    private function getCompanies($type) {
        $stmt = $this->db->prepare("SELECT id, name FROM companies WHERE type = ? ORDER BY name");
        $stmt->bind_param("s", $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $companies = [];
        while ($row = $result->fetch_assoc()) {
            $companies[] = $row;
        }
        return $companies;
    }

    /**
     * Get all ports
     */
    private function getPorts() {
        $result = $this->db->query("SELECT id, name, country FROM ports ORDER BY name");
        $ports = [];
        while ($row = $result->fetch_assoc()) {
            $ports[] = $row;
        }
        return $ports;
    }

    /**
     * Get all vessels
     */
    private function getVessels() {
        $result = $this->db->query("SELECT v.id, v.name, sl.name as shipping_line_name FROM vessels v LEFT JOIN shipping_lines sl ON v.shipping_line_id = sl.id ORDER BY v.name");
        $vessels = [];
        while ($row = $result->fetch_assoc()) {
            $vessels[] = $row;
        }
        return $vessels;
    }

    // Store new shipment
    public function store() {
        // Basic validation
        if (empty($_POST['reference_number'])) {
            $_SESSION['error'] = 'رقم المرجع مطلوب';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }

        // Check if reference number already exists
        $checkStmt = $this->db->prepare("SELECT id FROM shipments WHERE reference_number = ? LIMIT 1");
        $checkStmt->bind_param('s', $_POST['reference_number']);
        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();

        if ($existing) {
            $_SESSION['error'] = 'رقم المرجع موجود بالفعل. يرجى استخدام رقم مختلف.';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }

        if (empty($_POST['customer_id'])) {
            $_SESSION['error'] = 'يجب اختيار العميل';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }

        // Validate customer_id is a valid integer
        if (!is_numeric($_POST['customer_id']) || (int)$_POST['customer_id'] <= 0) {
            $_SESSION['error'] = 'معرف العميل غير صالح';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }

        if (empty($_POST['shipping_type'])) {
            $_SESSION['error'] = 'نوع الشحن مطلوب';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }

        // Validate shipping type
        $allowedShippingTypes = ['naval', 'air', 'land'];
        if (!in_array($_POST['shipping_type'], $allowedShippingTypes)) {
            $_SESSION['error'] = 'نوع الشحن غير صالح';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }

        // Validate other foreign key IDs if provided
        $foreignKeyFields = [
            'shipping_line_id' => 'خط الشحن',
            'local_agent_id' => 'الوكيل المحلي',
            'origin_port_id' => 'ميناء الشحن',
            'destination_port_id' => 'ميناء الوصول',
            'vessel_id' => 'السفينة'
        ];

        foreach ($foreignKeyFields as $field => $label) {
            if (!empty($_POST[$field]) && (!is_numeric($_POST[$field]) || (int)$_POST[$field] <= 0)) {
                $_SESSION['error'] = "معرف $label غير صالح";
                $_SESSION['form_data'] = $_POST;
                header('Location: ?page=shipments&action=create');
                exit();
            }
        }

        // Start transaction
        $this->db->begin_transaction();
        
        try {
            // Insert shipment
            $stmt = $this->db->prepare("INSERT INTO shipments (reference_number, customer_id, shipping_type, shipping_line_id, local_agent_id, 
                                  status, origin_port_id, destination_port_id, vessel_id, voyage_number, 
                                  estimated_departure_date, estimated_arrival_date, created_by) 
                           VALUES (?, ?, ?, ?, ?, 'draft', ?, ?, ?, ?, ?, ?, ?)");

            // Normalize optional values (empty to NULL)
            $customerId = (int)$_POST['customer_id']; // Already validated above
            $shippingType = $_POST['shipping_type'] ?? 'naval';
            $shippingLineId = !empty($_POST['shipping_line_id']) ? (int)$_POST['shipping_line_id'] : null;
            $localAgentId = !empty($_POST['local_agent_id']) ? (int)$_POST['local_agent_id'] : null;
            $originPortId = !empty($_POST['origin_port_id']) ? (int)$_POST['origin_port_id'] : null;
            $destinationPortId = !empty($_POST['destination_port_id']) ? (int)$_POST['destination_port_id'] : null;
            $vesselId = !empty($_POST['vessel_id']) ? (int)$_POST['vessel_id'] : null;
            $voyageNumber = $_POST['voyage_number'] !== '' ? $_POST['voyage_number'] : null;
            $estDep = $_POST['estimated_departure_date'] !== '' ? $_POST['estimated_departure_date'] : null;
            $estArr = $_POST['estimated_arrival_date'] !== '' ? $_POST['estimated_arrival_date'] : null;
            $createdBy = $_SESSION['user_id'] ?? 1;

            $stmt->bind_param("sisiiiiisssi",
                $_POST['reference_number'],
                $customerId,
                $shippingType,
                $shippingLineId,
                $localAgentId,
                $originPortId,
                $destinationPortId,
                $vesselId,
                $voyageNumber,
                $estDep,
                $estArr,
                $createdBy
            );
            
            $stmt->execute();
            $shipmentId = $this->db->insert_id;
            
            // Insert containers and keep a map of container_number => id
            $containerIdByNumber = [];
            if (!empty($_POST['containers'])) {
                $stmt = $this->db->prepare("INSERT INTO containers (shipment_id, container_number, container_type_id, 
                                        seal_number, weight_kg, volume_cbm) 
                                     VALUES (?, ?, ?, ?, ?, ?)");
                
                foreach ($_POST['containers'] as $container) {
                    $containerNumber = $container['container_number'] ?? null;
                    $containerTypeId = !empty($container['container_type_id']) ? (int)$container['container_type_id'] : null;
                    $sealNumber = $container['seal_number'] ?? null;
                    $weightKg = ($container['weight_kg'] === '' || !isset($container['weight_kg'])) ? null : (float)$container['weight_kg'];
                    $volumeCbm = ($container['volume_cbm'] === '' || !isset($container['volume_cbm'])) ? null : (float)$container['volume_cbm'];

                    // skip empty rows
                    if (empty($containerNumber)) { continue; }

                    $stmt->bind_param("isisdd", 
                        $shipmentId,
                        $containerNumber,
                        $containerTypeId,
                        $sealNumber,
                        $weightKg,
                        $volumeCbm
                    );
                    $stmt->execute();
                    $containerIdByNumber[$containerNumber] = $this->db->insert_id;
                }
            }
            
            // Insert cargo items
            if (!empty($_POST['cargo_items'])) {
                $stmt = $this->db->prepare("INSERT INTO cargo_items (shipment_id, description, hs_code, 
                                        quantity, unit_type, weight_kg, volume_cbm, container_id) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($_POST['cargo_items'] as $item) {
                    // Map optional container number reference to inserted container ID
                    $containerId = null;
                    if (!empty($item['container_number_ref'])) {
                        $ref = $item['container_number_ref'];
                        if (isset($containerIdByNumber[$ref])) {
                            $containerId = (int)$containerIdByNumber[$ref];
                        } else {
                            // Try to find an existing container for this shipment by number (in case it already existed)
                            $lookup = $this->db->prepare("SELECT id FROM containers WHERE shipment_id = ? AND container_number = ? LIMIT 1");
                            $lookup->bind_param("is", $shipmentId, $ref);
                            $lookup->execute();
                            $res = $lookup->get_result();
                            if ($row = $res->fetch_assoc()) { $containerId = (int)$row['id']; }
                        }
                    }

                    // Prepare variables for bind_param (must be variables passed by reference)
                    $desc = isset($item['description']) ? $item['description'] : '';
                    $hs = isset($item['hs_code']) ? $item['hs_code'] : null;
                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                    $unit = isset($item['unit_type']) ? $item['unit_type'] : 'PCS';
                    $w = (isset($item['weight_kg']) && $item['weight_kg'] !== '') ? (float)$item['weight_kg'] : null;
                    $vol = (isset($item['volume_cbm']) && $item['volume_cbm'] !== '') ? (float)$item['volume_cbm'] : null;

                    $stmt->bind_param("issisddi", 
                        $shipmentId,
                        $desc,
                        $hs,
                        $qty,
                        $unit,
                        $w,
                        $vol,
                        $containerId
                    );
                    $stmt->execute();
                }
            }
            
            $this->db->commit();
            
            // Redirect to view shipment
            header("Location: ?page=shipments&action=show&id=" . $shipmentId);
            exit();
            
        } catch (Exception $e) {
            $this->db->rollback();

            // Log detailed error information for debugging
            $errorDetails = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'post_data' => $_POST,
                'db_error' => $this->db->error,
                'db_errno' => $this->db->errno
            ];

            error_log('Shipment store error - Detailed info: ' . json_encode($errorDetails, JSON_UNESCAPED_UNICODE));

            // Provide more specific error message based on the error type
            $errorMessage = 'حدث خطأ أثناء حفظ الشحنة';

            if ($this->db->errno) {
                switch ($this->db->errno) {
                    case 1452: // Cannot add or update a child row: a foreign key constraint fails
                        $errorMessage = 'خطأ في البيانات المرتبطة. تأكد من اختيار قيم صحيحة للعملاء والموانئ والسفن.';
                        break;
                    case 1062: // Duplicate entry
                        if (strpos($this->db->error, 'reference_number') !== false) {
                            $errorMessage = 'رقم المرجع موجود بالفعل. يرجى استخدام رقم مختلف.';
                        } else {
                            $errorMessage = 'بيانات مكررة في قاعدة البيانات. يرجى المحاولة مرة أخرى.';
                        }
                        break;
                    case 1366: // Incorrect integer value
                        $errorMessage = 'خطأ في البيانات الرقمية. تأكد من إدخال أرقام صحيحة.';
                        break;
                    case 1265: // Data truncated for column
                        $errorMessage = 'البيانات المدخلة طويلة جداً. يرجى اختصار النصوص.';
                        break;
                    default:
                        $errorMessage = 'خطأ في قاعدة البيانات: ' . $this->db->error;
                }
            }

            $_SESSION['error'] = $errorMessage;
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=create');
            exit();
        }
    }

    // Show single shipment
    public function show($id) {
        $stmt = $this->db->prepare("SELECT s.*, c.name as company_name, c.address as company_address, 
                                  c.phone as company_phone, c.email as company_email, 
                                  sl.name as shipping_line_name, la.name as local_agent_name,
                                  p1.name as origin_port_name, p2.name as destination_port_name,
                                  v.name as vessel_name
                           FROM shipments s
                           LEFT JOIN companies c ON s.customer_id = c.id
                           LEFT JOIN companies sl ON s.shipping_line_id = sl.id
                           LEFT JOIN companies la ON s.local_agent_id = la.id
                           LEFT JOIN ports p1 ON s.origin_port_id = p1.id
                           LEFT JOIN ports p2 ON s.destination_port_id = p2.id
                           LEFT JOIN vessels v ON s.vessel_id = v.id
                           WHERE s.id = ?");
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $shipment = $stmt->get_result()->fetch_assoc();
        
        if (!$shipment) {
            // TODO: Handle not found
            die("Shipment not found");
        }
        
        // Get containers
        $containers = [];
        $stmt = $this->db->prepare("SELECT c.*, ct.code as container_type_code 
                                  FROM containers c 
                                  LEFT JOIN container_types ct ON c.container_type_id = ct.id 
                                  WHERE c.shipment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $containersResult = $stmt->get_result();
        
        while ($row = $containersResult->fetch_assoc()) {
            $containers[] = $row;
        }
        
        // Get cargo items
        $cargoItems = [];
        $stmt = $this->db->prepare("SELECT * FROM cargo_items WHERE shipment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $cargoResult = $stmt->get_result();
        
        while ($row = $cargoResult->fetch_assoc()) {
            $cargoItems[] = $row;
        }
        
        // Get payment data
        $paymentData = $this->getShipmentPayments($id);
        
        // Get documents
        $documents = [];
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE shipment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $documentsResult = $stmt->get_result();
        
        while ($row = $documentsResult->fetch_assoc()) {
            $documents[] = $row;
        }
        
        include __DIR__ . '/../views/shipments/show.php';
    }


    // Status helpers for views
    public function getStatusBadgeClass($status) {
        switch ($status) {
            case 'draft': return 'secondary';
            case 'quotation_sent': return 'warning';
            case 'approved': return 'primary';
            case 'in_transit': return 'info';
            case 'arrived': return 'success';
            case 'customs_cleared': return 'success';
            case 'delivered': return 'dark';
            case 'cancelled': return 'danger';
            default: return 'secondary';
        }
    }

    public function getStatusText($status) {
        switch ($status) {
            case 'draft': return 'مسودة';
            case 'quotation_sent': return 'تم إرسال عرض السعر';
            case 'approved': return 'تمت الموافقة';
            case 'in_transit': return 'قيد النقل';
            case 'arrived': return 'وصلت';
            case 'customs_cleared': return 'تخليص جمركي';
            case 'delivered': return 'تم التسليم';
            case 'cancelled': return 'أُلغيت';
            default: return 'غير معروف';
        }
    }

    // Update status endpoint
    public function updateStatus($id, $status) {
        $allowed = ['draft','quotation_sent','approved','in_transit','arrived','customs_cleared','delivered','cancelled'];
        if (!in_array($status, $allowed)) {
            $_SESSION['error'] = 'حالة غير صالحة';
            header('Location: ?page=shipments&action=show&id=' . (int)$id);
            exit();
        }

        // Get current status before update
        $currentStatusStmt = $this->db->prepare("SELECT status FROM shipments WHERE id = ?");
        $currentStatusStmt->bind_param("i", $id);
        $currentStatusStmt->execute();
        $currentStatus = $currentStatusStmt->get_result()->fetch_assoc()['status'];

        $stmt = $this->db->prepare("UPDATE shipments SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            // Add status history
            $hist = $this->db->prepare("INSERT INTO shipment_status_history (shipment_id, status, created_by) VALUES (?, ?, ?)");
            $userId = $_SESSION['user_id'] ?? 1;
            $hist->bind_param("isi", $id, $status, $userId);
            $hist->execute();

            // If status is approved and wasn't approved before, create payment record
            if ($status === 'approved' && $currentStatus !== 'approved') {
                // Removed automatic payment creation - will be handled manually
            }

            // Also create payment if approving from delivered status (common scenario)
            if ($status === 'approved' && $currentStatus === 'delivered') {
                // Removed automatic payment creation - will be handled manually
            }

            header('Location: ?page=shipments&action=show&id=' . (int)$id);
        } else {
            $_SESSION['error'] = 'تعذر تحديث الحالة';
            header('Location: ?page=shipments&action=show&id=' . (int)$id);
        }
        exit();
    }

    /**
     * Get payments for a specific shipment with payment progress calculation
     */
    public function getShipmentPayments($shipmentId) {
        // First get the quotation amount
        $stmt = $this->db->prepare("
            SELECT q.total_amount, q.currency, d.document_number
            FROM quotations q
            INNER JOIN documents d ON q.document_id = d.id
            WHERE d.shipment_id = ? AND d.document_type = 'quotation'
            LIMIT 1
        ");
        $stmt->bind_param("i", $shipmentId);
        $stmt->execute();
        $quotation = $stmt->get_result()->fetch_assoc();

        $quotationAmount = $quotation ? $quotation['total_amount'] : 0;
        $quotationCurrency = $quotation ? $quotation['currency'] : 'USD';

        // Get all payments for this shipment
        $stmt = $this->db->prepare("
            SELECT p.*, q.total_amount as quotation_amount, d.document_number
            FROM payments p
            LEFT JOIN quotations q ON p.quotation_id = q.id
            LEFT JOIN documents d ON q.document_id = d.id
            WHERE p.shipment_id = ?
            ORDER BY p.payment_date DESC, p.created_at DESC
        ");
        $stmt->bind_param("i", $shipmentId);
        $stmt->execute();
        $payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Calculate totals
        $totalPaid = 0;
        $totalPending = 0;

        foreach ($payments as &$payment) {
            if ($payment['status'] === 'paid') {
                $totalPaid += $payment['amount'];
            } elseif ($payment['status'] === 'pending') {
                $totalPending += $payment['amount'];
            }
        }

        $remainingAmount = $quotationAmount - $totalPaid;

        return [
            'payments' => $payments,
            'quotation_amount' => $quotationAmount,
            'quotation_currency' => $quotationCurrency,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'remaining_amount' => $remainingAmount,
            'payment_progress' => $quotationAmount > 0 ? ($totalPaid / $quotationAmount) * 100 : 0
        ];
    }

    /**
     * Create payment record when shipment is approved
     */
    private function createPaymentOnApproval($shipmentId) {
        // Get the latest quotation for this shipment
        $stmt = $this->db->prepare("
            SELECT q.*, d.document_number
            FROM quotations q
            INNER JOIN documents d ON q.document_id = d.id
            WHERE d.shipment_id = ? AND d.document_type = 'quotation'
            LIMIT 1
        ");
        $stmt->bind_param("i", $shipmentId);
        $stmt->execute();
        $quotation = $stmt->get_result()->fetch_assoc();

        if ($quotation) {
            // Create payment record
            $paymentStmt = $this->db->prepare("
                INSERT INTO payments (shipment_id, quotation_id, amount, currency, payment_date, payment_method, reference_number, status, created_by)
                VALUES (?, ?, ?, ?, CURDATE(), 'bank_transfer', ?, 'paid', ?)
            ");

            $referenceNumber = 'PAY-' . $shipmentId . '-' . date('Ymd');
            $userId = $_SESSION['user_id'] ?? 1;

            $paymentStmt->bind_param("idsssi",
                $shipmentId,
                $quotation['id'],
                $quotation['total_amount'],
                $quotation['currency'],
                $referenceNumber,
                $userId
            );

            $paymentStmt->execute();
        }
    }

    /**
     * Get payment statistics for dashboard
     */
    public function getPaymentStats() {
        $stats = [];

        // Total payments this month
        $stmt = $this->db->prepare("
            SELECT
                SUM(amount) as total_amount,
                COUNT(*) as total_payments,
                currency
            FROM payments
            WHERE payment_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
            AND payment_date <= LAST_DAY(CURDATE())
            AND status = 'paid'
        ");
        $stmt->execute();
        $monthly = $stmt->get_result()->fetch_assoc();
        $stats['monthly'] = $monthly;

        // Total payments this year
        $stmt = $this->db->prepare("
            SELECT
                SUM(amount) as total_amount,
                COUNT(*) as total_payments
            FROM payments
            WHERE payment_date >= DATE_FORMAT(CURDATE(), '%Y-01-01')
            AND payment_date <= CURDATE()
            AND status = 'paid'
        ");
        $stmt->execute();
        $yearly = $stmt->get_result()->fetch_assoc();
        $stats['yearly'] = $yearly;

        // Pending payments
        $stmt = $this->db->prepare("
            SELECT
                SUM(amount) as total_amount,
                COUNT(*) as total_payments
            FROM payments
            WHERE status = 'pending'
        ");
        $stmt->execute();
        $pending = $stmt->get_result()->fetch_assoc();
        $stats['pending'] = $pending;

        // Recent payments (last 5)
        $stmt = $this->db->prepare("
            SELECT p.*, s.reference_number, c.name as customer_name
            FROM payments p
            INNER JOIN shipments s ON p.shipment_id = s.id
            LEFT JOIN companies c ON s.customer_id = c.id
            WHERE p.status = 'paid'
            ORDER BY p.payment_date DESC, p.created_at DESC
            LIMIT 5
        ");
        $stmt->execute();
        $recent = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stats['recent'] = $recent;

        return $stats;
    }

    /**
     * Create a new payment for a shipment
     */
    public function paymentStore($shipmentId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        // Basic validation
        $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
        $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'bank_transfer';
        $referenceNumber = isset($_POST['reference_number']) ? trim($_POST['reference_number']) : '';
        $paymentDate = isset($_POST['payment_date']) && $_POST['payment_date'] !== '' ? $_POST['payment_date'] : date('Y-m-d');
        $notes = isset($_POST['notes']) ? $_POST['notes'] : null;

        if ($amount <= 0) {
            $_SESSION['error'] = 'مبلغ الدفع يجب أن يكون أكبر من صفر';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        // Get the quotation for this shipment to validate against
        $stmt = $this->db->prepare("
            SELECT q.id, q.total_amount, q.currency, d.document_number
            FROM quotations q
            INNER JOIN documents d ON q.document_id = d.id
            WHERE d.shipment_id = ? AND d.document_type = 'quotation'
            LIMIT 1
        ");
        $stmt->bind_param("i", $shipmentId);
        $stmt->execute();
        $quotation = $stmt->get_result()->fetch_assoc();

        if (!$quotation) {
            $_SESSION['error'] = 'لا توجد عرض سعر لهذه الشحنة';
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        // Check if payment amount is valid (shouldn't exceed remaining amount)
        $paymentData = $this->getShipmentPayments($shipmentId);
        $remainingAmount = $paymentData['remaining_amount'];

        if ($amount > $remainingAmount) {
            $_SESSION['error'] = 'مبلغ الدفع يتجاوز المبلغ المتبقي (' . number_format($remainingAmount, 2) . ' ' . $paymentData['quotation_currency'] . ')';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        $this->db->begin_transaction();
        try {
            // Generate reference number if not provided
            if (empty($referenceNumber)) {
                $referenceNumber = 'PAY-' . $shipmentId . '-' . date('YmdHis');
            }

            // Create payment record
            $stmt = $this->db->prepare("
                INSERT INTO payments (shipment_id, quotation_id, amount, currency, payment_date, payment_method, reference_number, status, notes, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', ?, ?)
            ");

            $userId = $_SESSION['user_id'] ?? 1;
            $status = 'paid'; // Default to paid for new payments

            $stmt->bind_param("idssssssi",
                $shipmentId,
                $quotation['id'],
                $amount,
                $quotation['currency'],
                $paymentDate,
                $paymentMethod,
                $referenceNumber,
                $notes,
                $userId
            );

            if (!$stmt->execute()) {
                throw new Exception('Failed to create payment: ' . $stmt->error);
            }

            $this->db->commit();
            $_SESSION['success'] = 'تم إضافة الدفع بنجاح';
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();

        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Payment creation failed: ' . $e->getMessage());
            $_SESSION['error'] = 'تعذر إضافة الدفع';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }
    }

    // Create and store a quotation for a shipment
    public function quotationStore($shipmentId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        // Basic validation
        $subtotal = isset($_POST['subtotal']) ? (float)$_POST['subtotal'] : 0.0;
        $taxRate = isset($_POST['tax_rate']) ? (float)$_POST['tax_rate'] : 0.0; // percent
        $currency = isset($_POST['currency']) && $_POST['currency'] !== '' ? $_POST['currency'] : 'USD';
        $notes = isset($_POST['notes']) ? $_POST['notes'] : null;
        $validUntil = isset($_POST['valid_until']) && $_POST['valid_until'] !== '' ? $_POST['valid_until'] : null;

        $taxAmount = round($subtotal * ($taxRate / 100.0), 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        $this->db->begin_transaction();
        try {
            // Create a document record for the quotation
            $docNumber = 'Q-' . $shipmentId . '-' . date('YmdHis');
            $stmt = $this->db->prepare("INSERT INTO documents (shipment_id, document_type, document_number, status, issue_date) VALUES (?, 'quotation', ?, 'issued', CURDATE())");
            $stmt->bind_param('is', $shipmentId, $docNumber);
            if (!$stmt->execute()) { throw new Exception('Failed to create document: ' . $stmt->error); }
            $documentId = $this->db->insert_id;

            // Create quotation record
            $stmtQ = $this->db->prepare("INSERT INTO quotations (document_id, valid_until, currency, subtotal, tax_amount, total_amount, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtQ->bind_param('issddds', $documentId, $validUntil, $currency, $subtotal, $taxAmount, $totalAmount, $notes);
            if (!$stmtQ->execute()) { throw new Exception('Failed to create quotation: ' . $stmtQ->error); }

            $this->db->commit();
            // Redirect to printable view
            header('Location: ?page=shipments&action=quotation_print&document_id=' . (int)$documentId);
            exit();
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Quotation creation failed: ' . $e->getMessage());
            $_SESSION['error'] = 'تعذر إنشاء عرض السعر';
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }
    }

    // Render a printable quotation
    public function quotationPrint($documentId) {
        // Load document and related data
        $stmt = $this->db->prepare("SELECT d.*, s.reference_number, s.id as shipment_id, s.shipping_type,
                                          s.origin_port_id, s.destination_port_id, s.vessel_id, s.voyage_number,
                                          s.estimated_departure_date, s.estimated_arrival_date,
                                          c.name as customer_name, c.address as customer_address, c.email as customer_email, c.phone as customer_phone,
                                          p1.name as origin_port_name, p2.name as destination_port_name,
                                          v.name as vessel_name
                                    FROM documents d
                                    LEFT JOIN shipments s ON d.shipment_id = s.id
                                    LEFT JOIN companies c ON s.customer_id = c.id
                                    LEFT JOIN ports p1 ON s.origin_port_id = p1.id
                                    LEFT JOIN ports p2 ON s.destination_port_id = p2.id
                                    LEFT JOIN vessels v ON s.vessel_id = v.id
                                    WHERE d.id = ? AND d.document_type = 'quotation'");
        $stmt->bind_param('i', $documentId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();
        if (!$doc) { die('Quotation not found'); }

        $stmtQ = $this->db->prepare("SELECT * FROM quotations WHERE document_id = ?");
        $stmtQ->bind_param('i', $documentId);
        $stmtQ->execute();
        $quote = $stmtQ->get_result()->fetch_assoc();

        // Load cargo items for this shipment
        $cargoItems = [];
        if ($doc['shipment_id']) {
            $stmt = $this->db->prepare("SELECT * FROM cargo_items WHERE shipment_id = ?");
            $stmt->bind_param("i", $doc['shipment_id']);
            $stmt->execute();
            $cargoResult = $stmt->get_result();
            while ($row = $cargoResult->fetch_assoc()) {
                $cargoItems[] = $row;
            }
        }

        // Include printable view
        include __DIR__ . '/../views/shipments/quotation.php';
    }

    // Store uploaded document for a shipment
    public function documentStore($shipmentId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        $allowedTypes = ['quotation','bill_of_lading','commercial_invoice','packing_list','customs_declaration','delivery_order','receipt'];
        $type = isset($_POST['document_type']) ? $_POST['document_type'] : '';
        if (!in_array($type, $allowedTypes)) {
            $_SESSION['error'] = 'نوع المستند غير صالح';
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        $number = isset($_POST['document_number']) ? trim($_POST['document_number']) : '';
        $issueDate = isset($_POST['issue_date']) && $_POST['issue_date'] !== '' ? $_POST['issue_date'] : null;
        $expiryDate = isset($_POST['expiry_date']) && $_POST['expiry_date'] !== '' ? $_POST['expiry_date'] : null;
        $status = isset($_POST['status']) && $_POST['status'] !== '' ? $_POST['status'] : 'issued';

        // Handle file upload (optional)
        $filePath = null;
        if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/documents/' . (int)$shipmentId;
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }
            $originalName = basename($_FILES['file']['name']);
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^A-Za-z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $destName = $safeName . '_' . date('YmdHis') . ($ext ? ('.' . $ext) : '');
            $destPath = $uploadDir . '/' . $destName;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
                $_SESSION['error'] = 'تعذر رفع الملف';
                header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
                exit();
            }
            // Public relative path for download link
            $filePath = 'uploads/documents/' . (int)$shipmentId . '/' . $destName;
        }

        // Insert document
        $stmt = $this->db->prepare("INSERT INTO documents (shipment_id, document_type, document_number, file_path, issue_date, expiry_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssss', $shipmentId, $type, $number, $filePath, $issueDate, $expiryDate, $status);
        if ($stmt->execute()) {
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
        } else {
            $_SESSION['error'] = 'تعذر حفظ المستند: ' . $stmt->error;
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
        }
        exit();
    }

    // View a document (quotation, receipt, etc.)
    public function documentView($shipmentId, $docId) {
        // Load document and related data
        $stmt = $this->db->prepare("SELECT d.*, s.reference_number, s.id as shipment_id, c.name as customer_name, c.address as customer_address, c.email as customer_email, c.phone as customer_phone
                                    FROM documents d
                                    LEFT JOIN shipments s ON d.shipment_id = s.id
                                    LEFT JOIN companies c ON s.customer_id = c.id
                                    WHERE d.id = ? AND d.shipment_id = ?");
        $stmt->bind_param('ii', $docId, $shipmentId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();
        if (!$doc) { die('Document not found'); }

        // Load quotation data if it's a quotation document
        $quote = null;
        if ($doc['document_type'] === 'quotation') {
            $stmtQ = $this->db->prepare("SELECT * FROM quotations WHERE document_id = ?");
            $stmtQ->bind_param('i', $docId);
            $stmtQ->execute();
            $quote = $stmtQ->get_result()->fetch_assoc();
        }

        // Load payment data if it's a receipt
        $payment = null;
        if ($doc['document_type'] === 'receipt') {
            $stmtP = $this->db->prepare("SELECT p.*, q.total_amount as quotation_amount FROM payments p LEFT JOIN quotations q ON p.quotation_id = q.id WHERE p.shipment_id = ? ORDER BY p.created_at DESC LIMIT 1");
            $stmtP->bind_param('i', $shipmentId);
            $stmtP->execute();
            $payment = $stmtP->get_result()->fetch_assoc();
        }

        // Include appropriate view based on document type
        if ($doc['document_type'] === 'quotation' && $quote) {
            include __DIR__ . '/../views/shipments/quotation.php';
        } elseif ($doc['document_type'] === 'receipt' && $payment) {
            include __DIR__ . '/../views/shipments/receipt.php';
        } else {
            // Generic document view
            include __DIR__ . '/../views/shipments/document.php';
        }
    }

    // Render a printable payment receipt
    public function paymentReceipt($paymentId) {
        // Load payment and related data
        $stmt = $this->db->prepare("
            SELECT p.*, s.reference_number, s.id as shipment_id, s.shipping_type,
                   s.origin_port_id, s.destination_port_id, s.vessel_id, s.voyage_number,
                   s.estimated_departure_date, s.estimated_arrival_date,
                   c.name as customer_name, c.address as customer_address,
                   c.email as customer_email, c.phone as customer_phone,
                   q.total_amount as quotation_amount, q.currency as quotation_currency,
                   d.document_number as quotation_number
            FROM payments p
            INNER JOIN shipments s ON p.shipment_id = s.id
            LEFT JOIN companies c ON s.customer_id = c.id
            LEFT JOIN quotations q ON p.quotation_id = q.id
            LEFT JOIN documents d ON q.document_id = d.id
            WHERE p.id = ?
        ");
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $payment = $stmt->get_result()->fetch_assoc();

        if (!$payment) {
            die('Payment not found');
        }

        // Get shipment details for header (including ports, vessels, etc.)
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as company_name, c.address as company_address,
                   c.phone as company_phone, c.email as company_email,
                   p1.name as origin_port_name, p2.name as destination_port_name,
                   v.name as vessel_name
            FROM shipments s
            LEFT JOIN companies c ON s.customer_id = c.id
            LEFT JOIN ports p1 ON s.origin_port_id = p1.id
            LEFT JOIN ports p2 ON s.destination_port_id = p2.id
            LEFT JOIN vessels v ON s.vessel_id = v.id
            WHERE s.id = ?
        ");
        $stmt->bind_param('i', $payment['shipment_id']);
        $stmt->execute();
        $shipment = $stmt->get_result()->fetch_assoc();

        // Load cargo items for this shipment
        $cargoItems = [];
        if ($payment['shipment_id']) {
            $stmt = $this->db->prepare("SELECT * FROM cargo_items WHERE shipment_id = ?");
            $stmt->bind_param("i", $payment['shipment_id']);
            $stmt->execute();
            $cargoResult = $stmt->get_result();
            while ($row = $cargoResult->fetch_assoc()) {
                $cargoItems[] = $row;
            }
        }

        // Include printable receipt view
        include __DIR__ . '/../views/payments/receipt.php';
    }

    // Delete a document
    public function documentDelete($shipmentId, $docId) {
        // Load document
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = ? AND shipment_id = ?");
        $stmt->bind_param('ii', $docId, $shipmentId);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();
        if (!$doc) {
            $_SESSION['error'] = 'المستند غير موجود';
            header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
            exit();
        }

        // Delete file if exists
        if (!empty($doc['file_path'])) {
            $abs = __DIR__ . '/../' . $doc['file_path'];
            if (is_file($abs)) { @unlink($abs); }
        }

        // Delete db row
        $del = $this->db->prepare("DELETE FROM documents WHERE id = ?");
        $del->bind_param('i', $docId);
        $del->execute();

        header('Location: ?page=shipments&action=show&id=' . (int)$shipmentId);
        exit();
    }
}

