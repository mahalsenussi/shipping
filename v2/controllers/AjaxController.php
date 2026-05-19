<?php
include_once __DIR__ . '/../config/database.php';
$mysqli = getDbConnection();

header('Content-Type: application/json');

// (No changes needed if requests are AJAX and DB schema matches)

if ($_GET['action'] === 'add_company') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    if ($name === '' || !in_array($type, ['customer', 'shipping_line', 'local_agent'])) {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO companies (name, type) VALUES (?, ?)");
    $stmt->bind_param('ss', $name, $type);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'name' => $name,
            'message' => 'تمت الإضافة بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
    exit;
}

// Store customer
if ($_GET['action'] === 'store_customer') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'اسم العميل مطلوب']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO companies (name, type) VALUES (?, 'customer')");
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'name' => $name,
            'message' => 'تمت الإضافة بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
    exit;
}

// Store shipping line
if ($_GET['action'] === 'store_shipping_line') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'اسم خط الشحن مطلوب']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO companies (name, type) VALUES (?, 'shipping_line')");
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'name' => $name,
            'message' => 'تمت الإضافة بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
    exit;
}

// Store agent
if ($_GET['action'] === 'store_agent') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'اسم الوكيل مطلوب']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO companies (name, type) VALUES (?, 'local_agent')");
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'name' => $name,
            'message' => 'تمت الإضافة بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
    exit;
}

if ($_GET['action'] === 'add_port') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'اسم الميناء مطلوب']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO ports (name) VALUES (?)");
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'name' => $name,
            'message' => 'تمت الإضافة بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
    exit;
}

if ($_GET['action'] === 'add_vessel') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        echo json_encode(['success' => false, 'message' => 'اسم السفينة مطلوب']);
        exit;
    }
    $stmt = $mysqli->prepare("INSERT INTO vessels (name) VALUES (?)");
    $stmt->bind_param('s', $name);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'name' => $name,
            'message' => 'تمت الإضافة بنجاح'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
    }
    exit;
}