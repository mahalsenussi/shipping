<?php
require_once __DIR__ . '/../config/database.php';

class VesselController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function create() {
        include __DIR__ . '/../views/vessels/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=vessels&action=create');
            exit();
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'اسم السفينة مطلوب';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=vessels&action=create');
            exit();
        }

        $stmt = $this->db->prepare("INSERT INTO vessels (name) VALUES (?)");
        $stmt->bind_param('s', $name);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'تم إضافة السفينة بنجاح';
            header('Location: ?page=shipments&action=create');
        } else {
            $_SESSION['error'] = 'خطأ في إضافة السفينة';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=vessels&action=create');
        }
        exit();
    }
}
