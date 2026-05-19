<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال دفع - <?php echo htmlspecialchars($payment['reference_number']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-info {
            margin-bottom: 30px;
        }
        .payment-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .shipment-details {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .payment-details h3, .shipment-details h3 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .details-label {
            font-weight: bold;
            color: #495057;
        }
        .details-value {
            color: #212529;
        }
        .amount-section {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .print-button {
            display: none;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .cargo-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .cargo-section h3 {
            color: #007bff;
            margin-bottom: 15px;
        }
        .cargo-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .cargo-table th, .cargo-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: right;
        }
        .cargo-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        @media print {
            .print-button {
                display: none !important;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-receipt"></i> إيصال دفع</h1>
        <h2>رقم الإيصال: <?php echo htmlspecialchars($payment['reference_number']); ?></h2>
    </div>

    <div class="company-info">
        <h3>معلومات الشركة</h3>
        <div class="details-row">
            <span class="details-label">اسم الشركة:</span>
            <span class="details-value"><?php echo htmlspecialchars($shipment['company_name'] ?? 'نظام إدارة الشحن'); ?></span>
        </div>
        <?php if (!empty($shipment['company_address'])): ?>
        <div class="details-row">
            <span class="details-label">العنوان:</span>
            <span class="details-value"><?php echo htmlspecialchars($shipment['company_address']); ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($shipment['company_phone'])): ?>
        <div class="details-row">
            <span class="details-label">الهاتف:</span>
            <span class="details-value"><?php echo htmlspecialchars($shipment['company_phone']); ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($shipment['company_email'])): ?>
        <div class="details-row">
            <span class="details-label">البريد الإلكتروني:</span>
            <span class="details-value"><?php echo htmlspecialchars($shipment['company_email']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Shipment Details Section -->
    <div class="shipment-details">
        <h3><i class="fas fa-shipping-fast"></i> تفاصيل الشحنة</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="detail-label">رقم الشحنة:</span>
                <span class="details-value"><?php echo htmlspecialchars($payment['reference_number']); ?></span>
            </div>

            <div class="detail-item">
                <span class="detail-label">نوع الشحن:</span>
                <span class="details-value">
                    <?php
                    $shippingTypeText = [
                        'naval' => 'بحري',
                        'air' => 'جوي',
                        'land' => 'بري'
                    ];
                    echo htmlspecialchars($shippingTypeText[$shipment['shipping_type']] ?? 'غير محدد');
                    ?>
                </span>
            </div>

            <?php if (!empty($shipment['origin_port_name'])): ?>
            <div class="detail-item">
                <span class="detail-label">ميناء الشحن:</span>
                <span class="details-value"><?php echo htmlspecialchars($shipment['origin_port_name']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($shipment['destination_port_name'])): ?>
            <div class="detail-item">
                <span class="detail-label">ميناء الوصول:</span>
                <span class="details-value"><?php echo htmlspecialchars($shipment['destination_port_name']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($shipment['vessel_name'])): ?>
            <div class="detail-item">
                <span class="detail-label">السفينة/الرحلة:</span>
                <span class="details-value"><?php echo htmlspecialchars($shipment['vessel_name']); ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($shipment['voyage_number'])): ?>
            <div class="detail-item">
                <span class="detail-label">رقم الرحلة:</span>
                <span class="details-value"><?php echo htmlspecialchars($shipment['voyage_number']); ?></span>
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
                    <?php if ($shipment['shipping_type'] === 'air'): ?>
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
                    <?php if ($shipment['shipping_type'] === 'air'): ?>
                        <td><?php echo htmlspecialchars($item['cargo_gross_weight'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($item['cargo_dimensions'] ?? ''); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="payment-details">
        <h3><i class="fas fa-money-bill-wave"></i> تفاصيل الدفع</h3>

        <div class="details-row">
            <span class="details-label">اسم العميل:</span>
            <span class="details-value"><?php echo htmlspecialchars($payment['customer_name'] ?? 'غير محدد'); ?></span>
        </div>

        <?php if (!empty($payment['customer_address'])): ?>
        <div class="details-row">
            <span class="details-label">عنوان العميل:</span>
            <span class="details-value"><?php echo htmlspecialchars($payment['customer_address']); ?></span>
        </div>
        <?php endif; ?>

        <div class="details-row">
            <span class="details-label">رقم عرض السعر:</span>
            <span class="details-value"><?php echo htmlspecialchars($payment['quotation_number'] ?? 'غير محدد'); ?></span>
        </div>

        <div class="details-row">
            <span class="details-label">إجمالي عرض السعر:</span>
            <span class="details-value"><?php echo number_format($payment['quotation_amount'], 2) . ' ' . htmlspecialchars($payment['quotation_currency']); ?></span>
        </div>

        <div class="amount-section">
            <div class="amount">
                <?php echo number_format($payment['amount'], 2) . ' ' . htmlspecialchars($payment['currency']); ?>
            </div>
            <div>مبلغ الدفع</div>
        </div>

        <div class="details-row">
            <span class="details-label">طريقة الدفع:</span>
            <span class="details-value">
                <?php
                switch ($payment['payment_method']) {
                    case 'bank_transfer': echo 'تحويل بنكي'; break;
                    case 'cash': echo 'نقدي'; break;
                    case 'check': echo 'شيك'; break;
                    case 'credit_card': echo 'بطاقة ائتمان'; break;
                    default: echo htmlspecialchars($payment['payment_method']);
                }
                ?>
            </span>
        </div>

        <div class="details-row">
            <span class="details-label">تاريخ الدفع:</span>
            <span class="details-value"><?php echo htmlspecialchars($payment['payment_date']); ?></span>
        </div>

        <?php if (!empty($payment['notes'])): ?>
        <div class="details-row">
            <span class="details-label">ملاحظات:</span>
            <span class="details-value"><?php echo htmlspecialchars($payment['notes']); ?></span>
        </div>
        <?php endif; ?>

        <div class="details-row">
            <span class="details-label">حالة الدفع:</span>
            <span class="details-value">
                <span class="badge bg-success">مدفوع</span>
            </span>
        </div>
    </div>

    <div class="footer">
        <p>تم إنشاء هذا الإيصال في: <?php echo date('Y-m-d H:i:s'); ?></p>
        <p>هذا المستند رسمي ومعتمد من نظام إدارة الشحن</p>
    </div>

    <div class="print-button" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">طباعة الإيصال</button>
        <a href="?page=shipments&action=show&id=<?php echo (int)$payment['shipment_id']; ?>" class="btn btn-secondary">العودة للشحنة</a>
    </div>
</body>
</html>
