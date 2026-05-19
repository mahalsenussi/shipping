<?php
// Start session
session_start();

// Include configuration
require_once __DIR__ . '/config/database.php';

// Simple router
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Define allowed pages
$allowed_pages = [
    'dashboard',
    'shipments',
    'companies',
    'documents',
    'reports',
    'customers',
    'shipping_lines',
    'agents',
    'vessels',
    'ports'
];

// Validate page
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Include header
include __DIR__ . '/views/layout/header.php';

// Route pages
if ($page === 'shipments') {
    require_once __DIR__ . '/controllers/ShipmentController.php';
    $controller = new ShipmentController();
    
    switch ($action) {
        case 'index':
            $controller->index();
            break;
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        case 'show':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $controller->show($id);
            break;
        case 'edit':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $controller->edit($id);
            break;
        case 'update':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $controller->update($id);
            break;
        case 'update_status':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $status = isset($_GET['status']) ? $_GET['status'] : 'draft';
            $controller->updateStatus($id, $status);
            break;
        case 'quotation_store':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // shipment id
            $controller->quotationStore($id);
            break;
        case 'quotation_print':
            $documentId = isset($_GET['document_id']) ? (int)$_GET['document_id'] : 0;
            $controller->quotationPrint($documentId);
            break;
        case 'document_store':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // shipment id
            $controller->documentStore($id);
            break;
        case 'document_delete':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // shipment id
            $docId = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
            $controller->documentDelete($id, $docId);
            break;
        case 'payment_receipt':
            $paymentId = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;
            $controller->paymentReceipt($paymentId);
            break;
    }
} elseif ($page === 'customers') {
    require_once __DIR__ . '/controllers/CustomerController.php';
    $controller = new CustomerController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        default:
            $controller->create();
    }
} elseif ($page === 'shipping_lines') {
    require_once __DIR__ . '/controllers/ShippingLineController.php';
    $controller = new ShippingLineController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        default:
            $controller->create();
    }
} elseif ($page === 'agents') {
    require_once __DIR__ . '/controllers/AgentController.php';
    $controller = new AgentController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        default:
            $controller->create();
    }
} elseif ($page === 'vessels') {
    require_once __DIR__ . '/controllers/VesselController.php';
    $controller = new VesselController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        default:
            $controller->create();
    }
} elseif ($page === 'ports') {
    require_once __DIR__ . '/controllers/PortController.php';
    $controller = new PortController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        default:
            $controller->create();
    }
} else {
    // Fallback to view include for other pages
    $page_file = __DIR__ . "/views/{$page}/{$action}.php";
    if (file_exists($page_file)) {
        include $page_file;
    } else {
        include __DIR__ . '/views/404.php';
    }
}

// Include footer
include __DIR__ . '/views/layout/footer.php';
?>
