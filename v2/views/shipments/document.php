<?php
// Generic Document View
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $doc['document_type']))); ?> - <?php echo htmlspecialchars($doc['document_number'] ?? 'N/A'); ?></title>
    <link href="/shipping/v2/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body { background: #fff; }
        .document { max-width: 900px; margin: 20px auto; background: #fff; padding: 24px; border: 1px solid #eee; }
        .document header { display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .document h1 { font-size: 22px; margin: 0; }
        .meta { margin-bottom: 16px; }
        .meta .row { display: flex; gap: 20px; }
        .meta .col { flex: 1; }
        .print-actions { text-align: right; margin-bottom: 8px; }
        @media print { .print-actions { display:none; } }
    </style>
</head>
<body>
<div class="document">
    <div class="print-actions">
        <a class="btn btn-sm btn-outline-secondary" href="?page=shipments&action=show&id=<?php echo (int)$doc['shipment_id']; ?>">عودة</a>
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="fa fa-print"></i> طباعة</button>
    </div>
    <header>
        <div>
            <h1><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $doc['document_type']))); ?></h1>
            <div>رقم المستند: <strong><?php echo htmlspecialchars($doc['document_number'] ?? 'N/A'); ?></strong></div>
            <div>رقم الشحنة: <strong><?php echo htmlspecialchars($doc['reference_number']); ?></strong></div>
            <div>تاريخ الإصدار: <strong><?php echo htmlspecialchars($doc['issue_date'] ?? 'N/A'); ?></strong></div>
            <?php if (!empty($doc['expiry_date'])): ?>
            <div>تاريخ الانتهاء: <strong><?php echo htmlspecialchars($doc['expiry_date']); ?></strong></div>
            <?php endif; ?>
        </div>
        <div style="text-align: right;">
            <strong>العميل</strong>
            <div><?php echo htmlspecialchars($doc['customer_name']); ?></div>
            <div><?php echo nl2br(htmlspecialchars($doc['customer_address'] ?? '')); ?></div>
            <div><?php echo htmlspecialchars($doc['customer_email'] ?? ''); ?></div>
            <div><?php echo htmlspecialchars($doc['customer_phone'] ?? ''); ?></div>
        </div>
    </header>

    <div class="meta">
        <div class="row">
            <div class="col">
                <div>الحالة: <strong><?php echo htmlspecialchars($doc['status'] ?? 'صادر'); ?></strong></div>
                <div>نوع المستند: <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $doc['document_type']))); ?></strong></div>
            </div>
            <div class="col" style="text-align:right;">
                <div>رقم الشحنة: <strong><?php echo htmlspecialchars($doc['reference_number']); ?></strong></div>
            </div>
        </div>
    </div>

    <?php if (!empty($doc['file_path'])): ?>
        <div class="alert alert-info">
            <i class="fa fa-file"></i> يحتوي هذا المستند على ملف مرفق
            <a href="/<?php echo htmlspecialchars($doc['file_path']); ?>" class="btn btn-sm btn-outline-primary ms-2" target="_blank">تحميل الملف</a>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <h5>تفاصيل المستند</h5>
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>نوع المستند</th>
                    <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $doc['document_type']))); ?></td>
                </tr>
                <tr>
                    <th>رقم المستند</th>
                    <td><?php echo htmlspecialchars($doc['document_number'] ?? 'غير محدد'); ?></td>
                </tr>
                <tr>
                    <th>تاريخ الإصدار</th>
                    <td><?php echo htmlspecialchars($doc['issue_date'] ?? 'غير محدد'); ?></td>
                </tr>
                <?php if (!empty($doc['expiry_date'])): ?>
                <tr>
                    <th>تاريخ الانتهاء</th>
                    <td><?php echo htmlspecialchars($doc['expiry_date']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>الحالة</th>
                    <td><?php echo htmlspecialchars($doc['status'] ?? 'صادر'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (!empty($doc['notes'])): ?>
        <div class="mt-4">
            <strong>ملاحظات:</strong>
            <div><?php echo nl2br(htmlspecialchars($doc['notes'])); ?></div>
        </div>
    <?php endif; ?>

    <div class="mt-4 text-muted">
        <small>تم إنشاء هذا المستند في: <?php echo htmlspecialchars($doc['created_at'] ?? date('Y-m-d H:i:s')); ?></small>
    </div>
</div>
</body>
</html>
