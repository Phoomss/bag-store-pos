<?php $title = 'ตรวจสอบสต็อกและมูลค่าสินทรัพย์'; ?>

<style>
    .card-stat {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-clipboard-check text-primary me-2"></i> ตรวจนับสต็อกคงคลังและมูลค่าสินทรัพย์</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 no-print" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> พิมพ์รายงานสรุป</button>
    </div>
</div>

<!-- Stock Totals Widget -->
<div class="row mb-4" id="auditSummaryWidgets">
    <?php
    $totalItems = 0;
    $totalQty = 0;
    $totalCostVal = 0.00;
    $totalRetailVal = 0.00;
    foreach ($products as $p) {
        if ($p['status'] !== 'Active') continue;
        $totalItems++;
        $totalQty += $p['stock_quantity'];
        $totalCostVal += ($p['stock_quantity'] * $p['cost_price']);
        $totalRetailVal += ($p['stock_quantity'] * $p['selling_price']);
    }
    ?>
    
    <!-- Active SKUs -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #3b82f6;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">รายการสินค้าที่จำหน่าย</h6>
                    <h3 class="fw-bold text-light m-0"><?= $totalItems ?> <span class="small text-secondary" style="font-size: 13px;">รายการ</span></h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                    <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total stock qty -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #a855f7;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">สินค้าในคลังทั้งหมด</h6>
                    <h3 class="fw-bold text-light m-0"><?= number_format($totalQty) ?> <span class="small text-secondary" style="font-size: 13px;">ชิ้น</span></h3>
                </div>
                <div class="p-3 bg-purple bg-opacity-10 text-purple rounded-4" style="color: #a855f7;">
                    <i class="fa-solid fa-warehouse fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total cost valuation -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">มูลค่าสินทรัพย์ (ราคาทุน)</h6>
                    <h3 class="fw-bold text-warning m-0">฿<?= number_format($totalCostVal, 2) ?></h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                    <i class="fa-solid fa-vault fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total retail valuation -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="glass-panel h-100 p-3 card-stat" style="border-left: 5px solid #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">มูลค่าสินทรัพย์ (ราคาขาย)</h6>
                    <h3 class="fw-bold text-success m-0">฿<?= number_format($totalRetailVal, 2) ?></h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                    <i class="fa-solid fa-tags fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audit Sheet Table -->
<div class="glass-panel border border-secondary shadow-sm">
    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-list-check text-primary me-2"></i> สมุดบัญชีมูลค่าสินทรัพย์คงคลัง</h5>
    <div class="table-responsive">
        <table class="table align-middle w-100" id="auditTable" width="100%">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>รหัสสินค้า / บาร์โค้ด</th>
                    <th>ชื่อสินค้า</th>
                    <th class="text-center">จำนวนในสต็อก</th>
                    <th class="text-end">ราคาทุน</th>
                    <th class="text-end">ราคาขาย</th>
                    <th class="text-end text-warning">มูลค่ารวม (ราคาทุน)</th>
                    <th class="text-end text-success">มูลค่ารวม (ราคาขาย)</th>
                    <th class="text-center" style="width: 100px;">สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <?php
                    $costVal = $p['stock_quantity'] * $p['cost_price'];
                    $retailVal = $p['stock_quantity'] * $p['selling_price'];
                    ?>
                    <tr class="border-secondary text-light">
                        <td>
                            <code><?= htmlspecialchars($p['sku']) ?></code><br>
                            <span class="text-secondary small" style="font-size: 11px;">บาร์โค้ด: <code><?= htmlspecialchars($p['barcode']) ?></code></span>
                        </td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="text-center">
                            <?php if ($p['stock_quantity'] <= 0): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i> สินค้าหมด</span>
                            <?php elseif ($p['stock_quantity'] <= $p['min_stock']): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning text-dark-override rounded-pill px-2 py-1"><i class="fa-solid fa-circle-exclamation me-1"></i> เหลือน้อย (<?= $p['stock_quantity'] ?>)</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> <?= $p['stock_quantity'] ?> ชิ้น</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end fw-medium">฿<?= number_format($p['cost_price'], 2) ?></td>
                        <td class="text-end fw-medium">฿<?= number_format($p['selling_price'], 2) ?></td>
                        <td class="text-end text-warning fw-bold">฿<?= number_format($costVal, 2) ?></td>
                        <td class="text-end text-success fw-bold">฿<?= number_format($retailVal, 2) ?></td>
                        <td class="text-center">
                            <?php if ($p['status'] === 'Active'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2">ปิดใช้งาน</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#auditTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']], // sort alphabetically
        language: {
            search: "_INPUT_",
            searchPlaceholder: "ค้นหาในสมุดบัญชีสต็อก...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "ไม่พบรายการ",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });
});
</script>
