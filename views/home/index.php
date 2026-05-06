<!-- ملخص إحصائي أعلى الصفحة -->
<div class="orders-summary">
    <div class="summary-card">
        <div class="summary-number"><?= $_SERVER['ordercount'] ?></div>
        <div class="summary-label">إجمالي الطلبات</div>
    </div>
    <?php foreach ($data['status'] as $value) {
        echo "<div class='summary-card'>
                    <div class='summary-number'>$value->total</div>
                    <div class='summary-label'> $value->order_status</div>
                    </div>";
    }

    ?>

    <!-- يمكنك إضافة بطاقات ملخص أخرى (قيد التنفيذ، مكتملة...) -->
</div>

<!-- شبكة بطاقات الطلبات -->
<div class="orders-grid">
    <?php foreach ($data['order_at'] as $value):
        // تعيين كلاس الحالة حسب النص (يمكن تعديله حسب قاعدة بياناتك)
        $statusClass = '';
        $status = '';
        switch (strtolower($value->order_status)) {
            case 'delivered':
                $statusClass = 'status-completed';
                $status = 'مكتمل';
                break;
            case 'confirmation':
                $statusClass = 'status-completed';
                $status = 'تاكيد';
                break;
            case 'pending':
                $statusClass = 'status-pending';
                $status = 'قيد الانتظار';
                break;
            case 'cancelled':
                $statusClass = 'cancelled';
                $status = 'ملغي';
                break;
            default:
                $statusClass = 'status-pending';
                $status = 'مؤكد';
        }
    ?>
        <article class="order-card">
            <div class="order-card-header">
                <span class="order-date">
                    📅 <?= htmlspecialchars($value->order_date) ?>
                </span>
                <span class="order-status <?= $statusClass ?>">
                    <?= htmlspecialchars($status) ?>
                </span>
            </div>

            <div class="order-card-body">
                <!-- العميل والهاتف -->
                <div class="info-row">
                    <span class="info-icon">👤</span>
                    <span class="info-label">العميل:</span>
                    <span class="info-value"><?= htmlspecialchars($value->full_name) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-icon">📞</span>
                    <span class="info-label">الهاتف:</span>
                    <span class="info-value"><?= htmlspecialchars($value->phone) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-icon">🏷️</span>
                    <span class="info-label">الدور:</span>
                    <span class="info-value"><?= htmlspecialchars($value->role) ?></span>
                </div>

                <!-- عنوان الشحن -->
                <div class="info-row">
                    <span class="info-icon">📍</span>
                    <span class="info-label">العنوان:</span>
                    <span class="info-value"><?= empty($value->shipping_address) ? " not found" : htmlspecialchars($value->shipping_address) ?></span>
                </div>
            </div>

            <div class="order-card-footer">
                <span class="total-amount">
                    <span class="info-label">المبلغ:</span>
                    <?= number_format($value->total_amount, 2) ?> ريال
                </span>
                <div class="order-actions">
                    <a href="#" class="btn btn-sm btn-primary" title="عرض التفاصيل">عرض</a>
                    <a href="#" class="btn btn-sm btn-warning" title="تعديل">تعديل</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<?php if (empty($data['order_at'])): ?>
    <!-- حالة عدم وجود طلبات -->
    <div class="empty-state card">
        <div style="font-size: 3rem; margin-bottom: 15px;">📭</div>
        <h3>لا توجد طلبات حالياً</h3>
        <p>ستظهر هنا الطلبات عند إضافتها</p>
    </div>
<?php endif; ?>