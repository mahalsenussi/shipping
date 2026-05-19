<?php
// Payment Receipt View
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إيصال دفع - <?php echo htmlspecialchars($doc['document_number'] ?? 'N/A'); ?></title>
    <link href="/shipping/v2/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body { background: #fff; }
        .receipt { max-width: 900px; margin: 20px auto; background: #fff; padding: 24px; border: 1px solid #eee; }
        .receipt header { display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .receipt h1 { font-size: 22px; margin: 0; }
        .meta { margin-bottom: 16px; }
        .meta .row { display: flex; gap: 20px; }
        .meta .col { flex: 1; }
        .totals { text-align: left; }
        .totals table { width: 300px; float: left; }
        .print-actions { text-align: right; margin-bottom: 8px; }
        @media print { .print-actions { display:none; } }
    </style>
</head>
<body>
<div class="receipt">
    <div class="print-actions">
        <a class="btn btn-sm btn-outline-secondary" href="?page=shipments&action=show&id=<?php echo (int)$doc['shipment_id']; ?>">عودة</a>
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="fa fa-print"></i> طباعة</button>
    </div>
    <header>
        <div>
            <h1>إيصال دفع</h1>
            <div>رقم الإيصال: <strong><?php echo htmlspecialchars($doc['document_number'] ?? 'N/A'); ?></strong></div>
            <div>رقم الشحنة: <strong><?php echo htmlspecialchars($doc['reference_number']); ?></strong></div>
            <div>تاريخ الدفع: <strong><?php echo htmlspecialchars($payment['payment_date'] ?? date('Y-m-d')); ?></strong></div>
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
                <div>رقم المرجع: <strong><?php echo htmlspecialchars($payment['reference_number'] ?? 'N/A'); ?></strong></div>
                <div>طريقة الدفع: <strong><?php echo htmlspecialchars($payment['payment_method'] ?? 'تحويل بنكي'); ?></strong></div>
            </div>
            <div class="col" style="text-align:right;">
                <div>الحالة: <strong><?php echo htmlspecialchars($payment['status'] ?? 'مدفوع'); ?></strong></div>
                <div>العملة: <strong><?php echo htmlspecialchars($payment['currency'] ?? 'USD'); ?></strong></div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>الوصف</th>
                <th class="text-end">المبلغ</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>دفعة مقابل الشحنة رقم <?php echo htmlspecialchars($doc['reference_number']); ?></td>
                <td class="text-end"><?php echo number_format($payment['amount'] ?? 0, 2) . ' ' . htmlspecialchars($payment['currency'] ?? 'USD'); ?></td>
            </tr>
            <?php if (isset($payment['quotation_amount']) && $payment['quotation_amount'] != $payment['amount']): ?>
            <tr>
                <td>المبلغ الأصلي من العرض السعري</td>
                <td class="text-end"><?php echo number_format($payment['quotation_amount'], 2) . ' ' . htmlspecialchars($payment['currency'] ?? 'USD'); ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="totals">
        <table class="table">
            <tbody>
                <tr>
                    <th>إجمالي المدفوعات</th>
                    <td class="text-end"><strong><?php echo number_format($payment['amount'] ?? 0, 2) . ' ' . htmlspecialchars($payment['currency'] ?? 'USD'); ?></strong></td>
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
</div>
</body>
</html>
