<?php
require_once __DIR__ . '/../config/database.php';

class AgentController {
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function create() {
        include __DIR__ . '/../views/agents/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?page=agents&action=create');
            exit();
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['error'] = 'اسم الوكيل مطلوب';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=agents&action=create');
            exit();
        }

        $stmt = $this->db->prepare("INSERT INTO companies (name, type) VALUES (?, 'local_agent')");
        $stmt->bind_param('s', $name);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'تم إضافة الوكيل بنجاح';
            header('Location: ?page=shipments&action=create');
        } else {
            $_SESSION['error'] = 'خطأ في إضافة الوكيل';
            $_SESSION['form_data'] = $_POST;
            header('Location: ?page=agents&action=create');
        }
        exit();
    }
}
