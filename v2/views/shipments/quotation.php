<?php
// Printable Quotation View - Enhanced with shipment details
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عرض سعر - <?php echo htmlspecialchars($doc['document_number']); ?></title>
    <link href="/shipping/v2/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body { background: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .quotation { max-width: 900px; margin: 20px auto; background: #fff; padding: 30px; border: 2px solid #007bff; border-radius: 10px; }
        .quotation header { display:flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .quotation h1 { font-size: 28px; margin: 0; color: #007bff; }
        .company-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .shipment-details { background: #e9ecef; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .shipment-details h3 { color: #495057; margin-bottom: 15px; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .detail-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
        .detail-label { font-weight: bold; color: #495057; }
        .detail-value { color: #212529; }
        .cargo-section { margin-top: 30px; }
        .cargo-section h3 { color: #007bff; margin-bottom: 15px; }
        .cargo-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .cargo-table th, .cargo-table td { border: 1px solid #dee2e6; padding: 10px; text-align: right; }
        .cargo-table th { background-color: #f8f9fa; font-weight: bold; }
        .totals { text-align: left; margin-top: 30px; }
        .totals table { width: 300px; float: left; border-collapse: collapse; }
        .totals th, .totals td { border: 1px solid #dee2e6; padding: 8px; text-align: right; }
        .totals th { background-color: #f8f9fa; }
        .print-actions { text-align: right; margin-bottom: 20px; }
        .notes-section { margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 8px; }
        @media print { .print-actions { display:none; } body { margin: 0; } }
    </style>
</head>
<body>
<div class="quotation">
    <div class="print-actions">
        <a class="btn btn-sm btn-outline-secondary" href="?page=shipments&action=show&id=<?php echo (int)$doc['shipment_id']; ?>">عودة</a>
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="fa fa-print"></i> طباعة</button>
    </div>

    <header>
        <div>
            <h1><i class="fas fa-file-invoice-dollar"></i> عرض سعر</h1>
            <div>رقم العرض: <strong><?php echo htmlspecialchars($doc['document_number']); ?></strong></div>
            <div>رقم الشحنة: <strong><?php echo htmlspecialchars($doc['reference_number']); ?></strong></div>
            <div>تاريخ الإصدار: <strong><?php echo htmlspecialchars($doc['issue_date']); ?></strong></div>
        </div>
        <div style="text-align: right;">
            <strong>العميل</strong>
            <div style="font-size: 18px; font-weight: bold; color: #007bff;"><?php echo htmlspecialchars($doc['customer_name']); ?></div>
            <div><?php echo nl2br(htmlspecialchars($doc['customer_address'] ?? '')); ?></div>
            <div><?php echo htmlspecialchars($doc['customer_email'] ?? ''); ?></div>
            <div><?php echo htmlspecialchars($doc['customer_phone'] ?? ''); ?></div>
        </div>
    </header>

    <!-- Shipment Details Section -->
    <div class="shipment-details">
        <h3><i class="fas fa-shipping-fast"></i> تفاصيل الشحنة</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">نوع الشحن:</span>
                <span class="detail-value">
                    <?php
                    $shippingTypeText = [
                        'naval' => 'بحري',
                        'air' => 'جوي',
                        'land' => 'بري'
                    ];
                    echo htmlspecialchars($shippingTypeText[$doc['shipping_type']] ?? 'غير محدد');
                    ?>
                </span>
            </div>

            <?php if (!empty($doc['origin_port_name'])): ?>
            <div class="detail-item">
                <span class="detail-label">ميناء الشحن:</span>
                <span class="detail-value"><?php echo htmlspecialchars($doc['origin_port_name']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($doc['destination_port_name'])): ?>
            <div class="detail-item">
                <span class="detail-label">ميناء الوصول:</span>
                <span class="detail-value"><?php echo htmlspecialchars($doc['destination_port_name']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($doc['vessel_name'])): ?>
            <div class="detail-item">
                <span class="detail-label">السفينة/الرحلة:</span>
                <span class="detail-value"><?php echo htmlspecialchars($doc['vessel_name']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($doc['voyage_number'])): ?>
            <div class="detail-item">
                <span class="detail-label">رقم الرحلة:</span>
                <span class="detail-value"><?php echo htmlspecialchars($doc['voyage_number']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($doc['estimated_departure_date'])): ?>
            <div class="detail-item">
                <span class="detail-label">تاريخ المغادرة المتوقع:</span>
                <span class="detail-value"><?php echo htmlspecialchars($doc['estimated_departure_date']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($doc['estimated_arrival_date'])): ?>
            <div class="detail-item">
                <span class="detail-label">تاريخ الوصول المتوقع:</span>
                <span class="detail-value"><?php echo htmlspecialchars($doc['estimated_arrival_date']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cargo Items Section (if available) -->
    <?php if (!empty($cargoItems)): ?>
    <div class="cargo-section">
        <h3><i class="fas fa-boxes"></i> تفاصيل البضائع</h3>
        <table class="cargo-table">
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>الكمية</th>
                    <th>الوحدة</th>
                    <th>الوزن (كجم)</th>
                    <th>الحجم (CBM)</th>
                    <?php if ($doc['shipping_type'] === 'air'): ?>
                        <th>الوزن الإجمالي (كجم)</th>
                        <th>الأبعاد (سم)</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cargoItems as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['unit_type'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['weight_kg'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['volume_cbm'] ?? ''); ?></td>
                    <?php if ($doc['shipping_type'] === 'air'): ?>
                        <td><?php echo htmlspecialchars($item['cargo_gross_weight'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['cargo_dimensions'] ?? ''); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Quotation Items -->
    <table class="table">
        <thead>
            <tr>
                <th>الوصف</th>
                <th class="text-end">المجموع</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>خدمات الشحن واللوجستيات للشحنة رقم <?php echo htmlspecialchars($doc['reference_number']); ?></td>
                <td class="text-end"><?php echo number_format($quote['subtotal'], 2) . ' ' . htmlspecialchars($quote['currency']); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <table class="table">
            <tbody>
                <tr>
                    <th>المجموع الفرعي</th>
                    <td class="text-end"><?php echo number_format($quote['subtotal'], 2) . ' ' . htmlspecialchars($quote['currency']); ?></td>
                </tr>
                <tr>
                    <th>الضريبة (<?php echo htmlspecialchars($quote['tax_rate'] ?? 0); ?>%)</th>
                    <td class="text-end"><?php echo number_format($quote['tax_amount'], 2) . ' ' . htmlspecialchars($quote['currency']); ?></td>
                </tr>
                <tr style="background-color: #f8f9fa;">
                    <th style="font-size: 18px;">الإجمالي</th>
                    <td class="text-end" style="font-size: 18px; font-weight: bold; color: #007bff;"><?php echo number_format($quote['total_amount'], 2) . ' ' . htmlspecialchars($quote['currency']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="meta">
        <div class="row">
            <div class="col">
                <div>ساري حتى: <strong><?php echo htmlspecialchars($quote['valid_until'] ?? ''); ?></strong></div>
            </div>
            <div class="col" style="text-align:right;">
                <div>العملة: <strong><?php echo htmlspecialchars($quote['currency']); ?></strong></div>
            </div>
        </div>
    </div>

    <?php if (!empty($quote['notes'])): ?>
        <div class="notes-section">
            <strong>ملاحظات:</strong>
            <div><?php echo nl2br(htmlspecialchars($quote['notes'])); ?></div>
        </div>
    <?php endif; ?>

    <div style="margin-top: 40px; text-align: center; color: #6c757d; font-size: 12px;">
        <p>تم إنشاء هذا العرض في: <?php echo date('Y-m-d H:i:s'); ?></p>
        <p>هذا المستند رسمي ومعتمد من نظام إدارة الشحن</p>
    </div>
</div>
</body>
</html>
