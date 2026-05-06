<div class="form-wrapper">
    <form method="POST" action="<?= BASE_URL ?>product/store" enctype="multipart/form-data" novalidate>

        <!-- ========== القسم 1: معلومات المنتج الأساسية ========== -->
        <div class="form-section">
            <div class="section-title">
                📦 معلومات المنتج الأساسية
            </div>
            <div class="section-body">
                <div class="form-grid col-2">
                    <div class="field-group">
                        <label for="product_name"><span class="required">*</span> اسم المنتج</label>
                        <input type="text" id="product_name" name="product_name" required maxlength="255">
                    </div>

                    <div class="field-group">
                        <label for="brand_id">العلامة التجارية</label>
                        <select id="brand_id" name="brand_id">
                            <option value="">-- اختر العلامة --</option>
                            <?php foreach ($data['brands'] as $brand): ?>
                                <option value="<?= $brand->brand_id ?>"><?= htmlspecialchars($brand->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field-group" style="grid-column: span 2;">
                        <label for="description">الوصف</label>
                        <textarea id="description" name="description"></textarea>
                    </div>

                    <div class="field-group">
                        <label>الصورة الأساسية للمنتج</label>
                        <div class="image-upload">
                            <div class="image-preview" id="baseImagePreview">🖼️</div>
                            <input type="file" name="product_image" accept="image/*" onchange="previewImage(this, 'baseImagePreview')">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="is_active">حالة المنتج</label>
                        <select id="is_active" name="is_active">
                            <option value="1" selected>نشط</option>
                            <option value="0">غير نشط</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== القسم 2: الأقسام ========== -->
        <div class="form-section">
            <div class="section-title">
                📂 الأقسام
            </div>
            <div class="section-body">
                <div class="form-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                    <?php foreach ($data['categories'] as $category): ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="categories[]" value="<?= $category->category_id ?>">
                            <?= htmlspecialchars($category->name) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ========== القسم 3: المتغيرات (Variants) ========== -->
        <div class="form-section">
            <div class="section-title">
                🔄 متغيرات المنتج (SKU، حجم، لون...)
            </div>
            <div class="section-body">
                <div id="variantsContainer" class="repeat-container">
                    <!-- القالب الأساسي لمتغير واحد -->
                    <div class="repeat-item variant-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" title="حذف المتغير">×</button>
                        <div class="form-grid col-2">
                            <div class="field-group">
                                <label><span class="required">*</span> SKU</label>
                                <input type="text" name="variants[0][sku]" required>
                            </div>
                            <div class="field-group">
                                <label>الحجم</label>
                                <input type="text" name="variants[0][size_option]" placeholder="مثال: كبير">
                            </div>
                            <div class="field-group">
                                <label>اللون</label>
                                <input type="text" name="variants[0][color_option]" placeholder="مثال: أحمر">
                            </div>
                            <div class="field-group">
                                <label>التغليف</label>
                                <input type="text" name="variants[0][packaging]" placeholder="قطعة/كرتون">
                            </div>
                            <div class="field-group">
                                <label><span class="required">*</span> السعر</label>
                                <input type="number" step="0.01" name="variants[0][price]" required>
                            </div>
                            <div class="field-group">
                                <label>الوزن (كجم)</label>
                                <input type="number" step="0.001" name="variants[0][weight_kg]">
                            </div>
                            <div class="field-group">
                                <label>صورة المتغير</label>
                                <input type="file" name="variants[0][image]" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="add-more-btn" onclick="addVariant()">
                    ＋ إضافة متغير آخر
                </button>
            </div>
        </div>

        <!-- ========== القسم 4: الموردين والتوريد ========== -->
        <div class="form-section">
            <div class="section-title">
                🚚 الموردين وشروط التوريد
            </div>
            <div class="section-body">
                <div id="suppliersContainer" class="repeat-container">
                    <div class="repeat-item supplier-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" title="حذف">×</button>
                        <div class="form-grid col-2">
                            <div class="field-group">
                                <label><span class="required">*</span> المورد</label>
                                <select name="suppliers[0][supplier_id]" required>
                                    <option value="">-- اختر مورد --</option>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <option value="<?= $supplier->supplier_id ?>"><?= htmlspecialchars($supplier->company_name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field-group">
                                <label><span class="required">*</span> سعر التوريد</label>
                                <input type="number" step="0.01" name="suppliers[0][supply_price]" required>
                            </div>
                            <div class="field-group">
                                <label>مدة التوريد (أيام)</label>
                                <input type="number" name="suppliers[0][lead_time_days]" min="0">
                            </div>
                            <div class="field-group">
                                <label>الحد الأدنى للطلب</label>
                                <input type="number" name="suppliers[0][minimum_order]" value="1" min="1">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="add-more-btn" onclick="addSupplier()">
                    ＋ إضافة مورد آخر
                </button>
            </div>
        </div>

        <!-- ========== القسم 5: المخزون الأولي ========== -->
        <div class="form-section">
            <div class="section-title">
                📊 المخزون الأولي
            </div>
            <div class="section-body">
                <div id="inventoryContainer" class="repeat-container">
                    <div class="repeat-item inventory-item">
                        <button type="button" class="remove-btn" onclick="removeItem(this)" title="حذف">×</button>
                        <div class="form-grid col-2">
                            <div class="field-group">
                                <label><span class="required">*</span> المستودع</label>
                                <select name="inventory[0][warehouse_id]" required>
                                    <option value="">-- اختر مستودع --</option>
                                    <?php foreach ($data['warehouse'] as $warehouse): ?>
                                        <option value="<?= $warehouse->warehouse_id ?>"><?= htmlspecialchars($warehouse->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="field-group">
                                <label><span class="required">*</span> الكمية الأولية</label>
                                <input type="number" name="inventory[0][quantity]" value="0" required>
                            </div>
                            <div class="field-group">
                                <label>حد إعادة الطلب</label>
                                <input type="number" name="inventory[0][reorder_level]" value="10">
                            </div>
                            <div class="field-group">
                                <label>كمية إعادة الطلب</label>
                                <input type="number" name="inventory[0][reorder_quantity]" value="50">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="add-more-btn" onclick="addInventory()">
                    ＋ إضافة مخزون لمستودع آخر
                </button>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div class="form-actions">
            <a href="<?= BASE_URL ?>products" class="btn btn-secondary">إلغاء</a>
            <button type="submit" class="btn btn-primary btn-lg">💾 حفظ المنتج</button>
        </div>
    </form>
</div>

<!-- ========== JavaScript للتحكم بالعناصر المتكررة ========== -->
<script>
    // عداد للمتغيرات
    let variantIndex = 1;
    let supplierIndex = 1;
    let inventoryIndex = 1;

    function addVariant() {
        const container = document.getElementById('variantsContainer');
        const item = document.querySelector('.variant-item').cloneNode(true);
        // تحديث أسماء الحقول
        item.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${variantIndex}]`));
            }
            if (input.tagName === 'INPUT' && input.type !== 'file') input.value = '';
        });
        container.appendChild(item);
        variantIndex++;
    }

    function addSupplier() {
        const container = document.getElementById('suppliersContainer');
        const item = document.querySelector('.supplier-item').cloneNode(true);
        item.querySelectorAll('select, input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${supplierIndex}]`));
            }
            if (input.tagName === 'INPUT' && input.type !== 'file') input.value = '';
        });
        container.appendChild(item);
        supplierIndex++;
    }

    function addInventory() {
        const container = document.getElementById('inventoryContainer');
        const item = document.querySelector('.inventory-item').cloneNode(true);
        item.querySelectorAll('select, input').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${inventoryIndex}]`));
            }
            if (input.tagName === 'INPUT' && input.type !== 'file') input.value = '';
        });
        container.appendChild(item);
        inventoryIndex++;
    }

    function removeItem(button) {
        const item = button.closest('.repeat-item');
        if (item.parentElement.children.length > 1) {
            item.remove();
        } else {
            // تنبيه لطيف بدلاً من alert
            showToast('لا يمكن حذف العنصر الوحيد');
        }
    }

    // معاينة الصورة
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="معاينة">`;
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = '🖼️';
        }
    }

    // رسالة منبثقة بسيطة (اختياري)
    function showToast(msg) {
        const toast = document.createElement('div');
        toast.textContent = msg;
        toast.style.cssText = 'position:fixed;bottom:20px;left:20px;background:#333;color:#fff;padding:10px 20px;border-radius:6px;z-index:9999;opacity:0;transition:opacity 0.3s;';
        document.body.appendChild(toast);
        setTimeout(() => toast.style.opacity = '1', 10);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
</script>