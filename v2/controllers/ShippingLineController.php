<?php
require_once __DIR__ . '/../config/database.php';

class ShippingLineController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function create() {
        include __DIR__ . '/../views/shipping_lines/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=shipping_lines&action=create');
            exit();
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'اسم خط الشحن مطلوب';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipping_lines&action=create');
            exit();
        }

        $stmt = $this->db->prepare("INSERT INTO companies (name, type) VALUES (?, 'shipping_line')");
        $stmt->bind_param('s', $name);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'تم إضافة خط الشحن بنجاح';
            header('Location: ?page=shipments&action=create');
        } else {
            $_SESSION['error'] = 'خطأ في إضافة خط الشحن';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=shipping_lines&action=create');
        }
        exit();
    }
}
