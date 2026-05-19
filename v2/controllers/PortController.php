<?php
require_once __DIR__ . '/../config/database.php';

class PortController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function create() {
        include __DIR__ . '/../views/ports/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=ports&action=create');
            exit();
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'اسم الميناء مطلوب';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=ports&action=create');
            exit();
        }

        $stmt = $this->db->prepare("INSERT INTO ports (name) VALUES (?)");
        $stmt->bind_param('s', $name);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'تم إضافة الميناء بنجاح';
            header('Location: ?page=shipments&action=create');
        } else {
            $_SESSION['error'] = 'خطأ في إضافة الميناء';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=ports&action=create');
        }
        exit();
    }
}
