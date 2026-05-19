<?php
require_once __DIR__ . '/../../config/database.php';
$db = getDbConnection();

// Get payment statistics
$paymentStats = [];
$stmt = $db->prepare("
    SELECT
        SUM(CASE WHEN payment_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01') AND payment_date <= LAST_DAY(CURDATE()) AND status = 'paid' THEN amount ELSE 0 END) as monthly_paid,
        SUM(CASE WHEN payment_date >= DATE_FORMAT(CURDATE(), '%Y-01-01') AND payment_date <= CURDATE() AND status = 'paid' THEN amount ELSE 0 END) as yearly_paid,
        SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count
    FROM payments
");
$stmt->execute();
$paymentStats = $stmt->get_result()->fetch_assoc();

// Totals
$totalShipments = (int)$db->query("SELECT COUNT(*) AS c FROM shipments")->fetch_assoc()['c'];
$pendingApprovals = (int)$db->query("SELECT COUNT(*) AS c FROM shipments WHERE status='quotation_sent'")->fetch_assoc()['c'];
$inTransit = (int)$db->query("SELECT COUNT(*) AS c FROM shipments WHERE status='in_transit'")->fetch_assoc()['c'];
$arrived = (int)$db->query("SELECT COUNT(*) AS c FROM shipments WHERE status='arrived'")->fetch_assoc()['c'];

// Recent shipments with payment info
$recent = $db->query(
    "SELECT s.id, s.reference_number, s.status, s.created_at, p2.name AS destination_port, c.name AS company_name,
            (SELECT SUM(amount) FROM payments WHERE shipment_id = s.id AND status = 'paid') as total_paid
     FROM shipments s
     LEFT JOIN ports p2 ON s.destination_port_id = p2.id
     LEFT JOIN companies c ON s.customer_id = c.id
     ORDER BY s.created_at DESC
     LIMIT 10"
);
$recentRows = [];
if ($recent) {
    while ($r = $recent->fetch_assoc()) { $recentRows[] = $r; }
}

function dashStatusBadge($status) {
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

function dashStatusText($status) {
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
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h1>
    </div>
    </div>

<div class="row">
    <!-- Shipments Summary -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">إجمالي الشحنات</h5>
                        <h2 class="mb-0"><?php echo $totalShipments; ?></h2>
                    </div>
                    <i class="fas fa-ship fa-3x opacity-50"></i>
                </div>
                <a href="?page=shipments" class="text-white">عرض التفاصيل <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">في انتظار الموافقة</h5>
                        <h2 class="mb-0"><?php echo $pendingApprovals; ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
                <a href="?page=shipments&action=index&status=quotation_sent" class="text-dark">عرض التفاصيل <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <!-- In Transit -->
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">قيد النقل</h5>
                        <h2 class="mb-0"><?php echo $inTransit; ?></h2>
                    </div>
                    <i class="fas fa-truck fa-3x opacity-50"></i>
                </div>
                <a href="?page=shipments&action=index&status=in_transit" class="text-white">عرض التفاصيل <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>

    <!-- Approved Shipments -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">الشحنات المعتمدة</h5>
                        <h2 class="mb-0"><?php echo (int)$db->query("SELECT COUNT(*) AS c FROM shipments WHERE status='approved'")->fetch_assoc()['c']; ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
                <a href="?page=shipments&action=index&status=approved" class="text-white">عرض التفاصيل <i class="fas fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Statistics Row -->
<div class="row">
    <!-- Monthly Payments -->
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">المدفوعات الشهرية</h5>
                        <h2 class="mb-0"><?php echo number_format($paymentStats['monthly_paid'] ?? 0, 2); ?> USD</h2>
                    </div>
                    <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                </div>
                <small class="text-white-50">هذا الشهر</small>
            </div>
        </div>
    </div>

    <!-- Yearly Payments -->
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">المدفوعات السنوية</h5>
                        <h2 class="mb-0"><?php echo number_format($paymentStats['yearly_paid'] ?? 0, 2); ?> USD</h2>
                    </div>
                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                </div>
                <small class="text-white-50">هذا العام</small>
            </div>
        </div>
    </div>

    <!-- Pending Payments -->
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">مدفوعات في الانتظار</h5>
                        <h2 class="mb-0"><?php echo number_format($paymentStats['pending_amount'] ?? 0, 2); ?> USD</h2>
                        <small><?php echo (int)($paymentStats['pending_count'] ?? 0); ?> مدفوعة</small>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Rate -->
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">معدل الدفع</h5>
                        <h2 class="mb-0">
                            <?php
                            $approvedCount = (int)$db->query("SELECT COUNT(*) AS c FROM shipments WHERE status='approved'")->fetch_assoc()['c'];
                            if ($approvedCount > 0) {
                                $paidCount = (int)$db->query("SELECT COUNT(*) AS c FROM payments WHERE status='paid'")->fetch_assoc()['c'];
                                $paymentRate = round(($paidCount / $approvedCount) * 100, 1);
                                echo $paymentRate . '%';
                            } else {
                                echo '0%';
                            }
                            ?>
                        </h2>
                    </div>
                    <i class="fas fa-percentage fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

<!-- Recent Shipments -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">أحدث الشحنات</h5>
        <a href="?page=shipments&action=create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> إضافة شحنة جديدة
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>رقم المرجع</th>
                        <th>العميل</th>
                        <th>ميناء الوصول</th>
                        <th>الحالة</th>
                        <th>المبلغ المدفوع</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentRows)): ?>
                        <tr>
                            <td colspan="7" class="text-center">لا توجد شحنات</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentRows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['company_name'] ?? 'غير معروف'); ?></td>
                                <td><?php echo htmlspecialchars($row['destination_port'] ?? 'غير محدد'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo dashStatusBadge($row['status']); ?>">
                                        <?php echo dashStatusText($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['total_paid']): ?>
                                        <span class="text-success fw-bold"><?php echo number_format($row['total_paid'], 2); ?> USD</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="?page=shipments&action=show&id=<?php echo (int)$row['id']; ?>">
                                        عرض
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
