<?php $title = 'แคตตาล็อกสินค้า'; ?>

<style>
    .view-toggle-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 12px;
        padding: 5px 14px;
        border-radius: 20px;
        transition: all 0.2s;
    }
    .view-toggle-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }
    .view-toggle-btn:hover:not(.active) {
        color: var(--accent-color);
        background-color: rgba(15, 23, 42, 0.04);
    }
    .card-stat {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .product-grid-card {
        border-radius: 18px;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }
    .product-grid-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        border-color: var(--accent-color);
    }
    .grid-img-container {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        background-color: var(--bg-primary);
        height: 180px;
    }
    .grid-img-container img {
        transition: transform 0.5s ease;
    }
    .product-grid-card:hover .grid-img-container img {
        transform: scale(1.06);
    }
</style>

<?php
// Compute Statistics
$totalSkus = count($products);
$outOfStock = 0;
$lowStock = 0;
$totalValue = 0;
foreach ($products as $p) {
    if ($p['stock_quantity'] <= 0) {
        $outOfStock++;
    } elseif ($p['stock_quantity'] <= $p['min_stock']) {
        $lowStock++;
    }
    $totalValue += $p['stock_quantity'] * $p['cost_price'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i> แคตตาล็อกสินค้า</h4>
    <div class="d-flex gap-3 align-items-center">
        <!-- View Toggle Switcher -->
        <div class="d-flex border border-secondary rounded-pill p-1 bg-white shadow-sm align-items-center" style="background-color: var(--bg-secondary) !important; height: 36px;">
            <button class="view-toggle-btn active" id="btnListView">
                <i class="fa-solid fa-list me-1"></i> ตาราง
            </button>
            <button class="view-toggle-btn" id="btnGridView">
                <i class="fa-solid fa-table-cells me-1"></i> การ์ด
            </button>
        </div>
        
        <a href="/products/create" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-medium shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มสินค้าใหม่
        </a>
    </div>
</div>

<!-- Catalog Statistics Cards -->
<div class="row mb-4">
    <!-- Stat 1: Total SKUs -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #3b82f6;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">จำนวนสินค้าทั้งหมด</h6>
                    <h3 class="m-0 fw-bold text-light"><?= $totalSkus ?> <span class="small text-secondary" style="font-size: 13px;">รายการ</span></h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                    <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stat 2: Out of Stock -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #ef4444;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">สินค้าหมดสต็อก</h6>
                    <h3 class="m-0 fw-bold text-danger"><?= $outOfStock ?> <span class="small text-secondary" style="font-size: 13px;">รายการ</span></h3>
                </div>
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4">
                    <i class="fa-solid fa-circle-xmark fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stat 3: Low Stock -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">สต็อกเหลือน้อย</h6>
                    <h3 class="m-0 fw-bold text-warning"><?= $lowStock ?> <span class="small text-secondary" style="font-size: 13px;">รายการ</span></h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                    <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stat 4: Inventory Total Cost Value -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.5px;">มูลค่าต้นทุนคงเหลือ</h6>
                    <h3 class="m-0 fw-bold text-success">฿<?= number_format($totalValue, 2) ?></h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                    <i class="fa-solid fa-vault fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Panel -->
<div class="glass-panel mb-4 shadow-sm border border-secondary">
    <form method="GET" action="/products" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="search" class="form-label small text-secondary fw-semibold">ค้นหาด้วย SKU, บาร์โค้ด, หรือชื่อสินค้า</label>
            <input type="text" class="form-control form-control-sm" id="search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="ค้นหาสินค้า...">
        </div>
        <div class="col-lg-2 col-md-6">
            <label for="category_id" class="form-label small text-secondary fw-semibold">หมวดหมู่สินค้า</label>
            <select class="form-select form-select-sm" id="category_id" name="category_id">
                <option value="">ทุกหมวดหมู่</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label for="brand_id" class="form-label small text-secondary fw-semibold">แบรนด์สินค้า</label>
            <select class="form-select form-select-sm" id="brand_id" name="brand_id">
                <option value="">ทุกแบรนด์</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['id'] ?>" <?= ($filters['brand_id'] == $brand['id']) ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label for="status" class="form-label small text-secondary fw-semibold">สถานะ</label>
            <select class="form-select form-select-sm" id="status" name="status">
                <option value="">สถานะทั้งหมด</option>
                <option value="Active" <?= ($filters['status'] === 'Active') ? 'selected' : '' ?>>เปิดใช้งาน</option>
                <option value="Inactive" <?= ($filters['status'] === 'Inactive') ? 'selected' : '' ?>>ปิดใช้งาน</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label for="stock_status" class="form-label small text-secondary fw-semibold">ระดับสินค้าในคลัง</label>
            <select class="form-select form-select-sm" id="stock_status" name="stock_status">
                <option value="">ทุกสถานะสต็อก</option>
                <option value="low" <?= ($filters['stock_status'] === 'low') ? 'selected' : '' ?>>สต็อกเหลือน้อย</option>
                <option value="out" <?= ($filters['stock_status'] === 'out') ? 'selected' : '' ?>>สินค้าหมดสต็อก</option>
            </select>
        </div>
        <div class="col-lg-1 col-md-6 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-pill"><i class="fa-solid fa-filter me-1"></i> กรอง</button>
        </div>
    </form>
</div>

<!-- 1. Table View Container -->
<div class="glass-panel border border-secondary shadow-sm" id="productsTableView">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="productsTable">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th style="width: 70px;">รูปภาพ</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>แบรนด์ & หมวดหมู่</th>
                    <th class="text-end">ราคาทุน</th>
                    <th class="text-end">ราคาขาย</th>
                    <th class="text-center">คงเหลือในคลัง</th>
                    <th class="text-center">สถานะ</th>
                    <th style="width: 160px;" class="text-center">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $prod): ?>
                    <tr class="border-secondary text-light">
                        <td>
                            <?php if (!empty($prod['primary_image'])): ?>
                                <img src="<?= htmlspecialchars($prod['primary_image']) ?>" alt="Bag Image" class="rounded-3 border" style="width: 50px; height: 50px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <?php else: ?>
                                <div class="bg-light rounded-3 border text-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($prod['name']) ?></span><br>
                            <span class="text-secondary small">SKU: <code><?= htmlspecialchars($prod['sku']) ?></code> | บาร์โค้ด: <code><?= htmlspecialchars($prod['barcode']) ?></code></span>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($prod['brand_name'] ?? 'ไม่มีแบรนด์') ?></span>
                            <span class="badge bg-success border text-light"><?= htmlspecialchars($prod['category_name'] ?? 'ไม่มีหมวดหมู่') ?></span>
                        </td>
                        <td class="text-end fw-medium">฿<?= number_format($prod['cost_price'], 2) ?></td>
                        <td class="text-end">
                            <?php if (!empty($prod['promotion_price']) && $prod['promotion_price'] < $prod['selling_price']): ?>
                                <span class="text-decoration-line-through text-secondary small" style="font-size: 11px;">฿<?= number_format($prod['selling_price'], 2) ?></span><br>
                                <span class="text-success fw-bold">฿<?= number_format($prod['promotion_price'], 2) ?></span>
                            <?php else: ?>
                                <span class="fw-bold text-dark">฿<?= number_format($prod['selling_price'], 2) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($prod['stock_quantity'] <= 0): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i> สินค้าหมด (<?= $prod['stock_quantity'] ?>)</span>
                            <?php elseif ($prod['stock_quantity'] <= $prod['min_stock']): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning text-dark-override rounded-pill px-2 py-1"><i class="fa-solid fa-circle-exclamation me-1"></i> เหลือน้อย (<?= $prod['stock_quantity'] ?>)</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> มีสินค้า (<?= $prod['stock_quantity'] ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($prod['status'] === 'Active'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2">ปิดใช้งาน</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/products/edit/<?= $prod['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fa-solid fa-pencil me-1"></i> แก้ไข
                                </a>
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3 delete-btn" data-id="<?= $prod['id'] ?>">
                                    <i class="fa-solid fa-trash me-1"></i> ลบ
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 2. Grid (Card) View Container -->
<div class="row g-3 d-none" id="productsGridView">
    <?php if (empty($products)): ?>
        <div class="col-12 text-center text-secondary py-5 glass-panel">
            <i class="fa-solid fa-boxes-stacked fa-3x mb-3 text-secondary bg-opacity-10"></i>
            <p class="m-0">ไม่พบรายการสินค้าในแคตตาล็อก</p>
        </div>
    <?php else: ?>
        <?php foreach ($products as $prod): ?>
            <div class="col-xxl-3 col-xl-4 col-md-6 col-12">
                <div class="product-grid-card shadow-sm">
                    <div>
                        <!-- Header Status Badge -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-secondary"><?= htmlspecialchars($prod['brand_name'] ?? 'ไม่มีแบรนด์') ?></span>
                            <?php if ($prod['stock_quantity'] <= 0): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2">สินค้าหมด</span>
                            <?php elseif ($prod['stock_quantity'] <= $prod['min_stock']): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning text-dark-override rounded-pill px-2">สต็อกเหลือน้อย</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">มีสินค้า (<?= $prod['stock_quantity'] ?>)</span>
                            <?php endif; ?>
                        </div>

                        <!-- Image display container -->
                        <div class="grid-img-container text-center mb-3 d-flex align-items-center justify-content-center">
                            <?php if (!empty($prod['primary_image'])): ?>
                                <img src="<?= htmlspecialchars($prod['primary_image']) ?>" alt="Product Image" class="w-100 h-100" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="text-secondary d-flex flex-column align-items-center">
                                    <i class="fa-solid fa-bag-shopping fa-3x mb-1 text-muted opacity-50"></i>
                                    <span class="small text-secondary" style="font-size: 11px;">ไม่มีรูปภาพ</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Product Title and SKU -->
                        <h6 class="fw-bold text-dark mb-1" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($prod['name']) ?></h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small" style="font-size: 11px;">SKU: <code><?= htmlspecialchars($prod['sku']) ?></code></span>
                            <?php if ($prod['status'] === 'Active'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2" style="font-size: 9px;">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2" style="font-size: 9px;">ปิดใช้งาน</span>
                            <?php endif; ?>
                        </div>

                        <!-- Additional Meta Info -->
                        <div class="p-2 rounded bg-light mb-3" style="font-size: 11px;">
                            <div class="row text-secondary">
                                <div class="col-6 mb-1">หมวดหมู่: <span class="fw-semibold text-dark"><?= htmlspecialchars($prod['category_name'] ?? 'ไม่มี') ?></span></div>
                                <div class="col-6 mb-1 text-end">สี: <span class="fw-semibold text-dark"><?= htmlspecialchars($prod['color'] ?? '-') ?></span></div>
                                <div class="col-12">ขนาด: <span class="fw-semibold text-dark"><?= htmlspecialchars($prod['size'] ?? '-') ?></span></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <!-- Cost & Sale Price Row -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary mb-3">
                            <div>
                                <span class="text-secondary small" style="font-size: 10px; display: block; text-transform: uppercase;">ราคาขาย</span>
                                <?php if (!empty($prod['promotion_price']) && $prod['promotion_price'] < $prod['selling_price']): ?>
                                    <span class="text-decoration-line-through text-secondary small" style="font-size: 11px; margin-right: 4px;">฿<?= number_format($prod['selling_price'], 2) ?></span>
                                    <span class="fw-bold text-success" style="font-size: 15px;">฿<?= number_format($prod['promotion_price'], 2) ?></span>
                                <?php else: ?>
                                    <span class="fw-bold text-dark" style="font-size: 15px;">฿<?= number_format($prod['selling_price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <span class="text-secondary small" style="font-size: 10px; display: block; text-transform: uppercase;">ราคาทุน</span>
                                <span class="fw-bold text-warning" style="font-size: 14px;">฿<?= number_format($prod['cost_price'], 2) ?></span>
                            </div>
                        </div>

                        <!-- Actions Buttons -->
                        <div class="d-flex gap-2">
                            <a href="/products/edit/<?= $prod['id'] ?>" class="btn btn-outline-primary btn-sm w-50 rounded-pill py-2"><i class="fa-solid fa-pencil me-1"></i> แก้ไข</a>
                            <button class="btn btn-outline-danger btn-sm w-50 rounded-pill py-2 delete-btn" data-id="<?= $prod['id'] ?>"><i class="fa-solid fa-trash me-1"></i> ลบ</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $('#productsTable').DataTable({
        responsive: true,
        searching: false, // filtered via top panel
        pageLength: 10,
        language: {
            search: "_INPUT_"
        }
    });

    // View Switcher logic
    const btnListView = document.getElementById('btnListView');
    const btnGridView = document.getElementById('btnGridView');
    const productsTableView = document.getElementById('productsTableView');
    const productsGridView = document.getElementById('productsGridView');

    if (btnListView && btnGridView && productsTableView && productsGridView) {
        btnListView.addEventListener('click', function() {
            productsTableView.classList.remove('d-none');
            productsGridView.classList.add('d-none');
            btnListView.classList.add('active');
            btnGridView.classList.remove('active');
            localStorage.setItem('productViewPreference', 'list');
        });

        btnGridView.addEventListener('click', function() {
            productsTableView.classList.add('d-none');
            productsGridView.classList.remove('d-none');
            btnGridView.classList.add('active');
            btnListView.classList.remove('active');
            localStorage.setItem('productViewPreference', 'grid');
        });

        // Load preferred view from localStorage
        const preferredView = localStorage.getItem('productViewPreference');
        if (preferredView === 'grid') {
            btnGridView.click();
        }
    }

    // Delete Product Action
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'ต้องการลบสินค้านี้?',
            text: "สินค้านี้และข้อมูลรูปภาพ/รายละเอียดสต็อกทั้งหมดจะถูกลบออกอย่างถาวร!",
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            color: '#0f172a',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'ยืนยัน, ลบสินค้า!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/products/delete/${id}`, {
                    method: 'POST'
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
                    return res.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ!',
                        text: 'ลบสินค้าออกจากระบบแล้ว.',
                        background: '#ffffff',
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: err.message,
                        background: '#ffffff',
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6'
                    });
                });
            }
        });
    });
});
</script>
