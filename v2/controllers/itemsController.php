<?php
include_once __DIR__ . '/../config/database.php';
$mysqli = getDbConnection();

if ($_GET['action'] === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id'] ?? 0);
    $shipping_line_id = intval($_POST['shipping_line_id'] ?? 0);
    $local_agent_id = intval($_POST['local_agent_id'] ?? 0);
    $origin_port_id = intval($_POST['origin_port_id'] ?? 0);
    $destination_port_id = intval($_POST['destination_port_id'] ?? 0);
    $vessel_id = intval($_POST['vessel_id'] ?? 0);
    $item_name = trim($_POST['item_name'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);

    if ($customer_id && $item_name !== '' && $quantity > 0) {
        $stmt = $mysqli->prepare("INSERT INTO items (customer_id, shipping_line_id, local_agent_id, origin_port_id, destination_port_id, vessel_id, name, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiiissi', $customer_id, $shipping_line_id, $local_agent_id, $origin_port_id, $destination_port_id, $vessel_id, $item_name, $quantity);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'تمت إضافة الصنف بنجاح';
            header('Location: ?page=items&action=index');
            exit;
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إضافة الصنف';
        }
    } else {
        $_SESSION['error'] = 'يرجى تعبئة جميع الحقول المطلوبة';
    }
    header('Location: ?page=items&action=create_item');
    exit;
}