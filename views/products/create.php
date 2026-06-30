<?php $title = 'เพิ่มสินค้ากระเป๋าใหม่'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-plus text-primary me-2"></i> เพิ่มสินค้ากระเป๋าใหม่</h4>
    <a href="/products" class="btn btn-secondary btn-sm rounded-pill px-3 no-print">
        <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้ารายการสินค้า
    </a>
</div>

<!-- Form Container -->
<div class="glass-panel border border-secondary shadow-sm">
    <form id="createProductForm" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Left Side: Basic Info -->
            <div class="col-lg-8">
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-semibold text-secondary">ชื่อสินค้ากระเป๋า *</label>
                        <input type="text" class="form-control fw-bold" id="name" name="name" required placeholder="เช่น Herschel Little America Black...">
                    </div>
                    <div class="col-md-3">
                        <label for="sku" class="form-label small fw-semibold text-secondary">รหัส SKU *</label>
                        <div class="input-group">
                            <input type="text" class="form-control fw-bold" id="sku" name="sku" required placeholder="รหัส SKU...">
                            <button class="btn btn-outline-secondary" type="button" id="generateSku" title="สุ่มรหัส SKU"><i class="fa-solid fa-wand-magic-sparkles"></i></button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="barcode" class="form-label small fw-semibold text-secondary">บาร์โค้ดสินค้า *</label>
                        <div class="input-group">
                            <input type="text" class="form-control fw-bold" id="barcode" name="barcode" required placeholder="เลขบาร์โค้ด...">
                            <button class="btn btn-outline-secondary" type="button" id="generateBarcode" title="สุ่มบาร์โค้ด EAN-13"><i class="fa-solid fa-barcode"></i></button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label small fw-semibold text-secondary">หมวดหมู่สินค้า</label>
                        <select class="form-select select2-enable" id="category_id" name="category_id">
                            <option value="">เลือกหมวดหมู่สินค้า...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label small fw-semibold text-secondary">แบรนด์สินค้า</label>
                        <select class="form-select select2-enable" id="brand_id" name="brand_id">
                            <option value="">เลือกแบรนด์สินค้า...</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-4">
                        <label for="color" class="form-label small fw-semibold text-secondary">โทนสีกระเป๋า</label>
                        <input type="text" class="form-control" id="color" name="color" placeholder="เช่น ดำ, น้ำเงิน, แดง...">
                    </div>
                    <div class="col-md-4">
                        <label for="material" class="form-label small fw-semibold text-secondary">วัสดุที่ใช้ผลิต</label>
                        <input type="text" class="form-control" id="material" name="material" placeholder="เช่น ผ้าใบแคนวาส, หนังแท้...">
                    </div>
                    <div class="col-md-4">
                        <label for="size" class="form-label small fw-semibold text-secondary">ขนาด / มิติสินค้า</label>
                        <input type="text" class="form-control" id="size" name="size" placeholder="เช่น กลาง, 40 x 30 x 15 ซม...">
                    </div>
                </div>

                <div class="mb-0">
                    <label for="description" class="form-label small fw-semibold text-secondary">คำอธิบายรายละเอียดสินค้า</label>
                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="กรอกคุณสมบัติสินค้า รายละเอียด จุดเด่นการใช้งาน จำนวนช่องกระเป๋า..."></textarea>
                </div>
            </div>

            <!-- Right Side: Valuation & Status -->
            <div class="col-lg-4 border-start border-secondary ps-lg-4">
                <div class="mb-3">
                    <label for="cost_price" class="form-label small fw-semibold text-secondary">ราคาทุนต่อหน่วย *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold" id="cost_price" name="cost_price" required placeholder="0.00">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="selling_price" class="form-label small fw-semibold text-secondary">ราคาขายหน้าร้าน *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold text-primary" id="selling_price" name="selling_price" required placeholder="0.00">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="promotion_price" class="form-label small fw-semibold text-secondary">ราคาพิเศษ / โปรโมชัน (ถ้ามี)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold text-success" id="promotion_price" name="promotion_price" placeholder="0.00">
                    </div>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-6">
                        <label for="stock_quantity" class="form-label small fw-semibold text-secondary">จำนวนสต็อกเริ่มต้น</label>
                        <input type="number" class="form-control fw-bold" id="stock_quantity" name="stock_quantity" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label for="min_stock" class="form-label small fw-semibold text-secondary">จุดเตือนสต็อกขั้นต่ำ *</label>
                        <input type="number" class="form-control fw-bold text-danger" id="min_stock" name="min_stock" value="5" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label small fw-semibold text-secondary">สถานะการขายสินค้า</label>
                    <select class="form-select fw-medium" id="status" name="status">
                        <option value="Active" class="text-success">เปิดขาย (Active)</option>
                        <option value="Inactive" class="text-danger">ปิดขายชั่วคราว (Inactive)</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label for="images" class="form-label small fw-semibold text-secondary">รูปภาพประกอบสินค้า (เลือกได้หลายรูป)</label>
                    <input class="form-control" type="file" id="images" name="images[]" multiple accept="image/*">
                    <p class="text-secondary small mt-1" style="font-size: 10px; line-height: 1.3;">รูปภาพแรกที่เลือกอัปโหลดจะถูกใช้เป็นภาพหลักในการขายหน้าร้าน</p>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex justify-content-end gap-2 no-print">
            <a href="/products" class="btn btn-outline-secondary rounded-pill px-4">ยกเลิก</a>
            <button type="submit" class="btn btn-primary rounded-pill px-5">บันทึกเพิ่มสินค้าใหม่</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2
    $('.select2-enable').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Auto SKU generation
    document.getElementById('generateSku').addEventListener('click', function() {
        const rand = Math.floor(1000 + Math.random() * 9000);
        document.getElementById('sku').value = 'BAG-SKU-' + Date.now().toString().slice(-6) + '-' + rand;
    });

    // Auto Barcode generation
    document.getElementById('generateBarcode').addEventListener('click', function() {
        // Generate EAN-13 style random barcode numbers starting with '888' (Thailand prefix code)
        const rand = Math.floor(100000000 + Math.random() * 900000000);
        document.getElementById('barcode').value = '888' + rand;
    });

    // Submit AJAX
    const form = document.getElementById('createProductForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Perform validation: Cost cannot be larger than Selling price
        const cost = parseFloat(document.getElementById('cost_price').value);
        const selling = parseFloat(document.getElementById('selling_price').value);
        if (cost > selling) {
            Swal.fire({
                icon: 'warning',
                title: 'อัตรากำไรติดลบ!',
                text: 'ราคาทุนสินค้าสูงกว่าราคาขายปกติหน้าร้าน! กรุณาตรวจสอบอัตรากำไรอีกครั้งก่อนกดบันทึก.',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const formData = new FormData(form);

        fetch('/products/create', {
            method: 'POST',
            body: formData // Browser handles multipart headers automatically
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกข้อมูลแล้ว!',
                text: 'เพิ่มกระเป๋าใบใหม่ลงในระบบจัดเก็บสินค้าเรียบร้อย.',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                window.location.href = '/products';
            });
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.message,
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
        });
    });
});
</script>
