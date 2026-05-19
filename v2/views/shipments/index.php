<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-ship"></i> إدارة الشحنات</h1>
    <a href="?page=shipments&action=create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة شحنة جديدة
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">قائمة الشحنات</h5>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="ابحث عن شحنة...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($shipments)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد شحنات مسجلة حتى الآن</p>
                <a href="?page=shipments&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة شحنة جديدة
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>رقم المرجع</th>
                            <th>العميل</th>
                            <th>نوع الشحن</th>
                            <th>ميناء الشحن</th>
                            <th>ميناء الوصول</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shipments as $shipment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($shipment['reference_number']); ?></td>
                                <td><?php echo htmlspecialchars($shipment['company_name'] ?? 'غير معروف'); ?></td>
                                <td>
                                    <?php
                                    $shippingTypeText = [
                                        'naval' => '<span class="badge bg-primary">بحري</span>',
                                        'air' => '<span class="badge bg-info">جوي</span>',
                                        'land' => '<span class="badge bg-success">بري</span>'
                                    ];
                                    echo $shippingTypeText[$shipment['shipping_type']] ?? '<span class="badge bg-secondary">غير محدد</span>';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($shipment['origin_port'] ?? 'غير محدد'); ?></td>
                                <td><?php echo htmlspecialchars($shipment['destination_port'] ?? 'غير محدد'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $this->getStatusBadgeClass($shipment['status']); ?>">
                                        <?php echo $this->getStatusText($shipment['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($shipment['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=shipments&action=show&id=<?php echo $shipment['id']; ?>" 
                                           class="btn btn-sm btn-info" 
                                           title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?page=shipments&action=edit&id=<?php echo $shipment['id']; ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                title="حذف"
                                                onclick="confirmDelete(<?php echo $shipment['id']; ?>, '<?php echo htmlspecialchars($shipment['reference_number']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">السابق</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">التالي</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف الشحنة <span id="shipmentReference"></span>؟
                <p class="text-danger mt-2">هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" action="">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, reference) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('shipmentReference').textContent = reference;
    document.getElementById('deleteForm').action = `?page=shipments&action=destroy&id=${id}`;
    modal.show();
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>
