<?php
include_once __DIR__ . "/../../config/database.php"; 
$mysqli = getDbConnection();

// Load companies, ports, vessels for dropdowns
$customers = $mysqli->query("SELECT id, name FROM companies WHERE type='customer' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$shipping_lines = $mysqli->query("SELECT id, name FROM companies WHERE type='shipping_line' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$agents = $mysqli->query("SELECT id, name FROM companies WHERE type='local_agent' ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$ports = $mysqli->query("SELECT id, name FROM ports ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$vessels = $mysqli->query("SELECT id, name FROM vessels ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-box"></i> إضافة صنف جديد</h1>
    <a href="?page=items&action=index" class="btn btn-secondary">العودة للقائمة</a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form method="POST" action="?page=items&action=store">
    <div class="card">
        <div class="card-body row g-3">

            <div class="col-md-6">
                <label class="form-label">العميل *</label>
                <div class="input-group">
                    <select name="customer_id" class="form-select" required>
                        <option value="">-- اختر عميل --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=companies&action=create&type=customer" class="btn btn-outline-primary">+</a>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">خط الشحن</label>
                <div class="input-group">
                    <select name="shipping_line_id" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php foreach ($shipping_lines as $sl): ?>
                            <option value="<?php echo $sl['id']; ?>"><?php echo htmlspecialchars($sl['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=companies&action=create&type=shipping_line" class="btn btn-outline-primary">+</a>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">الوكيل المحلي</label>
                <div class="input-group">
                    <select name="local_agent_id" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php foreach ($agents as $a): ?>
                            <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=companies&action=create&type=local_agent" class="btn btn-outline-primary">+</a>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">ميناء الشحن</label>
                <div class="input-group">
                    <select name="origin_port_id" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php foreach ($ports as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=ports&action=create" class="btn btn-outline-primary">+</a>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">ميناء الوصول</label>
                <div class="input-group">
                    <select name="destination_port_id" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php foreach ($ports as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=ports&action=create" class="btn btn-outline-primary">+</a>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">السفينة</label>
                <div class="input-group">
                    <select name="vessel_id" class="form-select">
                        <option value="">-- اختر --</option>
                        <?php foreach ($vessels as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <a href="?page=vessels&action=create" class="btn btn-outline-primary">+</a>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">اسم الصنف *</label>
                <input type="text" name="item_name" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">الكمية *</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>

        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
    </div>
</form>
