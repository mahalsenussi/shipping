<?php
// This view expects $shipment, $containers, $cargoItems, $documents to be set by ShipmentController::show()
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-file-invoice"></i> تفاصيل الشحنة #<?php echo htmlspecialchars($shipment['reference_number']); ?></h1>
    <div>
        <a href="?page=shipments&action=index" class="btn btn-secondary me-2">العودة للقائمة</a>
        <a href="?page=shipments&action=edit&id=<?php echo (int)$shipment['id']; ?>" class="btn btn-warning">تعديل</a>
    </div>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">معلومات الشحنة</h5>
                <span class="badge bg-<?php echo $this->getStatusBadgeClass($shipment['status']); ?>">
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>نوع الشحن:</strong>
                        <div>
                            <?php
                            $shippingTypeText = [
                                'naval' => 'بحري',
                                'air' => 'جوي',
                                'land' => 'بري'
                            ];
                            echo htmlspecialchars($shippingTypeText[$shipment['shipping_type']] ?? 'غير محدد');
                            ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <strong>العميل:</strong>
                        <div><?php echo htmlspecialchars($shipment['company_name'] ?? 'غير معروف'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <strong>الوكيل المحلي:</strong>
                        <div><?php echo htmlspecialchars($shipment['local_agent_name'] ?? 'غير محدد'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <strong>الميناء (شحن):</strong>
                        <div><?php echo htmlspecialchars($shipment['origin_port_name'] ?? 'غير محدد'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <strong>الميناء (وصول):</strong>
                        <div><?php echo htmlspecialchars($shipment['destination_port_name'] ?? 'غير محدد'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <strong>السفينة / الرحلة:</strong>
                        <div>
                            <?php echo htmlspecialchars($shipment['vessel_name'] ?? ''); ?>
                            <?php if (!empty($shipment['voyage_number'])) echo ' - ' . htmlspecialchars($shipment['voyage_number']); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ المغادرة المتوقع:</strong>
                        <div><?php echo htmlspecialchars($shipment['estimated_departure_date'] ?? '-'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ الوصول المتوقع:</strong>
                        <div><?php echo htmlspecialchars($shipment['estimated_arrival_date'] ?? '-'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">الحاويات</h5>
                <a href="?page=shipments&action=edit&id=<?php echo (int)$shipment['id']; ?>#containers" class="btn btn-sm btn-outline-primary">إدارة</a>
            </div>
            <div class="card-body">
                <?php if (empty($containers)): ?>
                    <div class="text-muted">لا توجد حاويات</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم الحاوية</th>
                                    <th>النوع</th>
                                    <th>الوزن (كجم)</th>
                                    <th>الحجم (CBM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($containers as $c): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($c['container_number']); ?></td>
                                    <td><?php echo htmlspecialchars($c['container_type_code'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($c['weight_kg'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($c['volume_cbm'] ?? ''); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">المدفوعات</h5>
                <?php
                // Payment data is already available from the controller
                ?>
            </div>
            <div class="card-body">
                <?php if ($paymentData['quotation_amount'] > 0): ?>
                    <!-- Payment Progress -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>إجمالي المبلغ:</strong>
                                <div class="h4 text-primary"><?php echo number_format($paymentData['quotation_amount'], 2) . ' ' . htmlspecialchars($paymentData['quotation_currency']); ?></div>
                            </div>
                            <div class="col-md-3">
                                <strong>المبلغ المدفوع:</strong>
                                <div class="h4 text-success"><?php echo number_format($paymentData['total_paid'], 2) . ' ' . htmlspecialchars($paymentData['quotation_currency']); ?></div>
                            </div>
                            <div class="col-md-3">
                                <strong>المبلغ المتبقي:</strong>
                                <div class="h4 <?php echo $paymentData['remaining_amount'] > 0 ? 'text-warning' : 'text-success'; ?>">
                                    <?php echo number_format($paymentData['remaining_amount'], 2) . ' ' . htmlspecialchars($paymentData['quotation_currency']); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <strong>نسبة الدفع:</strong>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php echo $paymentData['payment_progress'] >= 100 ? 'success' : ($paymentData['payment_progress'] > 50 ? 'warning' : 'danger'); ?>"
                                         role="progressbar"
                                         style="width: <?php echo min($paymentData['payment_progress'], 100); ?>%">
                                        <?php echo round($paymentData['payment_progress'], 1); ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Payment Form -->
                    <?php if ($paymentData['remaining_amount'] > 0): ?>
                        <div class="mb-4">
                            <h6>إضافة دفعة جديدة</h6>
                            <form method="POST" action="?page=shipments&action=payment_store&id=<?php echo (int)$shipment['id']; ?>" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">مبلغ الدفع</label>
                                    <input type="number" step="0.01" min="0.01" max="<?php echo $paymentData['remaining_amount']; ?>"
                                           name="amount" class="form-control" required
                                           placeholder="أدخل المبلغ">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">طريقة الدفع</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="bank_transfer">تحويل بنكي</option>
                                        <option value="cash">نقدي</option>
                                        <option value="check">شيك</option>
                                        <option value="credit_card">بطاقة ائتمان</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">رقم المرجع (اختياري)</label>
                                    <input type="text" name="reference_number" class="form-control"
                                           placeholder="رقم الإيصال أو المرجع">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">تاريخ الدفع</label>
                                    <input type="date" name="payment_date" class="form-control"
                                           value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">ملاحظات (اختياري)</label>
                                    <textarea name="notes" rows="2" class="form-control"
                                              placeholder="تفاصيل إضافية عن الدفعة"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> إضافة الدفعة
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (empty($paymentData['payments'])): ?>
                    <div class="text-muted">لا توجد مدفوعات</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم المرجع</th>
                                    <th>المبلغ</th>
                                    <th>العملة</th>
                                    <th>تاريخ الدفع</th>
                                    <th>طريقة الدفع</th>
                                    <th>الحالة</th>
                                    <th>ملاحظات</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentData['payments'] as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['reference_number']); ?></td>
                                    <td><?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($payment['currency']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($payment['status'] === 'paid' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger')); ?>">
                                            <?php echo htmlspecialchars($payment['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['notes'] ?? ''); ?></td>
                                    <td>
                                        <a href="?page=shipments&action=payment_receipt&payment_id=<?php echo (int)$payment['id']; ?>"
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fa fa-print"></i> طباعة
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">إنشاء عرض سعر</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=shipments&action=quotation_store&id=<?php echo (int)$shipment['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">القيمة (Subtotal)</label>
                        <input type="number" step="0.01" min="0" name="subtotal" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">الضريبة %</label>
                                <input type="number" step="0.01" min="0" name="tax_rate" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">العملة</label>
                                <select name="currency" class="form-select">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="LYD">LYD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ساري حتى</label>
                        <input type="date" name="valid_until" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="تفاصيل العرض..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-file-invoice-dollar"></i> حفظ وعرض الطباعة
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">تحديث الحالة</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=quotation_sent" class="btn btn-outline-secondary">إرسال عرض السعر</a>
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=approved" class="btn btn-outline-primary">اعتماد</a>
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=in_transit" class="btn btn-outline-info">بدء النقل</a>
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=arrived" class="btn btn-outline-success">وصول</a>
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=customs_cleared" class="btn btn-outline-success">تخليص جمركي</a>
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=delivered" class="btn btn-outline-dark">تسليم</a>
                    <a href="?page=shipments&action=update_status&id=<?php echo (int)$shipment['id']; ?>&status=cancelled" class="btn btn-outline-danger">إلغاء</a>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">المستندات</h5>
            </div>
            <div class="card-body">
                <form action="?page=shipments&action=document_store&id=<?php echo (int)$shipment['id']; ?>" method="POST" enctype="multipart/form-data" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">نوع المستند</label>
                        <select name="document_type" class="form-select" required>
                            <option value="quotation">Quotation</option>
                            <option value="bill_of_lading">Bill of Lading</option>
                            <option value="commercial_invoice">Commercial Invoice</option>
                            <option value="packing_list">Packing List</option>
                            <option value="customs_declaration">Customs Declaration</option>
                            <option value="delivery_order">Delivery Order</option>
                            <option value="receipt">Receipt</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">رقم المستند</label>
                        <input type="text" name="document_number" class="form-control" placeholder="اختياري">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">تاريخ الإصدار</label>
                        <input type="date" name="issue_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">تاريخ الإنتهاء</label>
                        <input type="date" name="expiry_date" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ملف</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> إضافة</button>
                    </div>
                </form>

                <?php if (empty($documents)): ?>
                    <div class="text-muted">لا توجد مستندات</div>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($documents as $doc): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($doc['document_type']); ?></strong>
                                    <div class="small text-muted"><?php echo htmlspecialchars($doc['document_number']); ?></div>
                                </div>
                                <div class="btn-group">
                                    <a href="?page=shipments&action=document_view&id=<?php echo (int)$shipment['id']; ?>&doc_id=<?php echo (int)$doc['id']; ?>" class="btn btn-sm btn-outline-info" target="_blank">عرض</a>
                                    <?php if (!empty($doc['file_path'])): ?>
                                        <a href="/<?php echo htmlspecialchars($doc['file_path']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">تحميل</a>
                                    <?php endif; ?>
                                    <a href="?page=shipments&action=document_delete&id=<?php echo (int)$shipment['id']; ?>&doc_id=<?php echo (int)$doc['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('حذف المستند؟');">حذف</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <div class="mt-3">
                    <a href="#" class="btn btn-sm btn-primary disabled" title="لاحقاً">إنشاء B/L</a>
                    <a href="#" class="btn btn-sm btn-outline-secondary disabled" title="لاحقاً">إيصال</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
