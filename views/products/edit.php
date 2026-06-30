<?php $title = 'แก้ไขข้อมูลสินค้ากระเป๋า'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-pencil text-info me-2"></i> แก้ไขข้อมูลสินค้ากระเป๋า</h4>
    <a href="/products" class="btn btn-secondary btn-sm rounded-pill px-3 no-print">
        <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้ารายการสินค้า
    </a>
</div>

<!-- Form Container -->
<div class="glass-panel border border-secondary shadow-sm">
    <form id="editProductForm" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Left Side: Basic Info -->
            <div class="col-lg-8">
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-semibold text-secondary">ชื่อสินค้ากระเป๋า *</label>
                        <input type="text" class="form-control fw-bold" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="sku" class="form-label small fw-semibold text-secondary">รหัส SKU *</label>
                        <input type="text" class="form-control fw-bold" id="sku" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="barcode" class="form-label small fw-semibold text-secondary">บาร์โค้ดสินค้า *</label>
                        <input type="text" class="form-control fw-bold" id="barcode" name="barcode" value="<?= htmlspecialchars($product['barcode']) ?>" required>
                    </div>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label small fw-semibold text-secondary">หมวดหมู่สินค้า</label>
                        <select class="form-select select2-enable" id="category_id" name="category_id">
                            <option value="">เลือกหมวดหมู่สินค้า...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label small fw-semibold text-secondary">แบรนด์สินค้า</label>
                        <select class="form-select select2-enable" id="brand_id" name="brand_id">
                            <option value="">เลือกแบรนด์สินค้า...</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>" <?= ($product['brand_id'] == $brand['id']) ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-4">
                        <label for="color" class="form-label small fw-semibold text-secondary">โทนสีกระเป๋า</label>
                        <input type="text" class="form-control" id="color" name="color" value="<?= htmlspecialchars($product['color'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="material" class="form-label small fw-semibold text-secondary">วัสดุที่ใช้ผลิต</label>
                        <input type="text" class="form-control" id="material" name="material" value="<?= htmlspecialchars($product['material'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="size" class="form-label small fw-semibold text-secondary">ขนาด / มิติสินค้า</label>
                        <input type="text" class="form-control" id="size" name="size" value="<?= htmlspecialchars($product['size'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-0">
                    <label for="description" class="form-label small fw-semibold text-secondary">คำอธิบายรายละเอียดสินค้า</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Right Side: Valuation & Status -->
            <div class="col-lg-4 border-start border-secondary ps-lg-4">
                <div class="mb-3">
                    <label for="cost_price" class="form-label small fw-semibold text-secondary">ราคาทุนต่อหน่วย *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold" id="cost_price" name="cost_price" value="<?= $product['cost_price'] ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="selling_price" class="form-label small fw-semibold text-secondary">ราคาขายหน้าร้าน *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold text-primary" id="selling_price" name="selling_price" value="<?= $product['selling_price'] ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="promotion_price" class="form-label small fw-semibold text-secondary">ราคาพิเศษ / โปรโมชัน (ถ้ามี)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold text-success" id="promotion_price" name="promotion_price" value="<?= $product['promotion_price'] ?? '' ?>">
                    </div>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-secondary">จำนวนคงเหลือในคลัง</label>
                        <input type="text" class="form-control fw-bold text-dark" value="<?= number_format($product['stock_quantity']) ?> ชิ้น" disabled readonly style="background-color: rgba(15,23,42,0.02);">
                    </div>
                    <div class="col-6">
                        <label for="min_stock" class="form-label small fw-semibold text-secondary">จุดเตือนสต็อกขั้นต่ำ *</label>
                        <input type="number" class="form-control fw-bold text-danger" id="min_stock" name="min_stock" value="<?= $product['min_stock'] ?>" min="1" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label small fw-semibold text-secondary">สถานะการขายสินค้า</label>
                    <select class="form-select fw-medium" id="status" name="status">
                        <option value="Active" <?= ($product['status'] === 'Active') ? 'selected' : '' ?> class="text-success">เปิดขาย (Active)</option>
                        <option value="Inactive" <?= ($product['status'] === 'Inactive') ? 'selected' : '' ?> class="text-danger">ปิดขายชั่วคราว (Inactive)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label small fw-semibold text-secondary">เพิ่มรูปภาพกระเป๋าเพิ่มเติม</label>
                    <input class="form-control" type="file" id="images" name="images[]" multiple accept="image/*">
                </div>

                <!-- Existing Images Previews -->
                <?php if (!empty($product['images'])): ?>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold text-secondary">รูปภาพกระเป๋าปัจจุบัน</label>
                        <div class="row g-2">
                            <?php foreach ($product['images'] as $img): ?>
                                <div class="col-3 position-relative">
                                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Bag Thumbnail" class="rounded border border-secondary w-100" style="height: 60px; object-fit: cover;">
                                    <?php if ($img['is_primary'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success position-absolute bottom-0 start-0 m-1" style="font-size: 7px; padding: 2px 4px;">รูปหลัก</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex justify-content-end gap-2 no-print">
            <a href="/products" class="btn btn-outline-secondary rounded-pill px-4">ยกเลิก</a>
            <button type="submit" class="btn btn-info text-white rounded-pill px-5">บันทึกอัปเดตข้อมูล</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.select2-enable').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    const form = document.getElementById('editProductForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

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

        fetch(`/products/update/<?= $product['id'] ?>`, {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'บันทึกข้อมูลแล้ว!',
                text: 'ปรับปรุงรายละเอียดข้อมูลสินค้ากระเป๋าเรียบร้อยแล้ว.',
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
