<?php $title = 'แผงควบคุมคลังสินค้า'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-warehouse text-primary me-2"></i> แผงควบคุมคลังสินค้าและการจัดส่ง</h4>
        <span class="text-secondary small">ภาพรวมสถานะสินค้าคงคลังและรายการจัดซื้อล่าสุด</span>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <!-- Total Catalog Items -->
    <div class="col-lg-3 col-md-6">
        <div class="glass-panel card-stat h-100 border border-secondary shadow-sm" style="border-left: 5px solid #3b82f6 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">จำนวนแบบกระเป๋าในระบบ</h6>
                    <h3 class="m-0 fw-bold text-dark"><?= number_format($stats['total_items']) ?> รายการ</h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                    <i class="fa-solid fa-boxes-stacked fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Quantity in Stock -->
    <div class="col-lg-3 col-md-6">
        <div class="glass-panel card-stat h-100 border border-secondary shadow-sm" style="border-left: 5px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">ยอดสต็อกรวมทุกคลัง</h6>
                    <h3 class="m-0 fw-bold text-dark"><?= number_format($stats['total_quantity']) ?> ชิ้น</h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                    <i class="fa-solid fa-cubes fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending/Ordered POs -->
    <div class="col-lg-3 col-md-6">
        <div class="glass-panel card-stat h-100 border border-secondary shadow-sm" style="border-left: 5px solid #f59e0b !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">ใบสั่งซื้อค้างรับมอบ</h6>
                    <h3 class="m-0 fw-bold text-dark"><?= number_format($stats['pending_purchases_count']) ?> ใบงาน</h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                    <i class="fa-solid fa-truck-ramp-box fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Low and Out of stock -->
    <div class="col-lg-3 col-md-6">
        <div class="glass-panel card-stat h-100 border border-secondary shadow-sm" style="border-left: 5px solid #ef4444 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">วิกฤตสต็อก (ต่ำ/หมด)</h6>
                    <h3 class="m-0 fw-bold text-danger"><?= $stats['low_stock_count'] ?> / <?= $stats['out_of_stock_count'] ?> รายการ</h3>
                </div>
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4">
                    <i class="fa-solid fa-triangle-exclamation fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Low Stock warnings -->
    <div class="col-xl-6">
        <div class="glass-panel border border-secondary shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-bell text-danger me-2"></i> สินค้าจุดเตือนวิกฤต / ต้องสั่งซื้อเพิ่ม</h5>
                <a href="/purchases/create" class="btn btn-xs btn-outline-primary rounded-pill px-2"><i class="fa-solid fa-plus me-1"></i> ออกใบสั่งซื้อ (PO)</a>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>รายการกระเป๋า</th>
                            <th>รหัส SKU</th>
                            <th class="text-center">สต็อกปัจจุบัน</th>
                            <th class="text-center">จุดเตือนขั้นต่ำ</th>
                            <th class="text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['low_stock_products'])): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">ยอดเยี่ยม! ไม่มีสินค้าต่ำกว่าจุดเตือนในขณะนี้</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['low_stock_products'] as $p): ?>
                                <tr class="border-secondary text-light">
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></td>
                                    <td><code><?= htmlspecialchars($p['sku']) ?></code></td>
                                    <td class="text-center fw-bold text-dark"><?= number_format($p['stock_quantity']) ?> ชิ้น</td>
                                    <td class="text-center text-secondary"><?= number_format($p['min_stock']) ?> ชิ้น</td>
                                    <td class="text-center">
                                        <?php if ($p['stock_quantity'] <= 0): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2 py-1 fw-bold" style="font-size: 10px;">สินค้าหมด</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-2 py-1 fw-bold text-dark-override" style="font-size: 10px;">สต็อกเหลือน้อย</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Recent Adjustments -->
    <div class="col-xl-6">
        <div class="glass-panel border border-secondary shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i> ประวัติการปรับปรุงยอดสต็อกล่าสุด</h5>
                <a href="/inventory" class="btn btn-xs btn-outline-secondary rounded-pill px-2"><i class="fa-solid fa-list me-1"></i> ดูประวัติทั้งหมด</a>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>วัน-เวลา</th>
                            <th>รายการสินค้า</th>
                            <th class="text-center">ประเภท</th>
                            <th class="text-center">จำนวนปรับ</th>
                            <th>เหตุผล</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['recent_adjustments'])): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">ไม่มีรายการปรับปรุงสต็อกล่าสุด</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['recent_adjustments'] as $adj): ?>
                                <tr class="border-secondary text-light">
                                    <td class="text-dark"><?= date('d M H:i', strtotime($adj['created_at'])) ?></td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($adj['product_name']) ?></span><br>
                                        <span class="text-secondary small" style="font-size: 10px;">SKU: <code><?= htmlspecialchars($adj['sku']) ?></code></span>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                                        $typeText = $adj['type'];
                                        if ($adj['type'] === 'Adjustment') { $badge = 'bg-info bg-opacity-10 text-info border border-info'; $typeText = 'ปรับยอด'; }
                                        if ($adj['type'] === 'Transfer') { $badge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override'; $typeText = 'โอนสต็อก'; }
                                        if ($adj['type'] === 'Damaged') { $badge = 'bg-danger bg-opacity-10 text-danger border border-danger'; $typeText = 'ชำรุด'; }
                                        if ($adj['type'] === 'Lost') { $badge = 'bg-danger bg-opacity-10 text-danger border border-danger'; $typeText = 'สูญหาย'; }
                                        ?>
                                        <span class="badge <?= $badge ?> rounded-pill px-2" style="font-size: 10px;"><?= $typeText ?></span>
                                    </td>
                                    <td class="text-center fw-bold <?= ($adj['quantity'] > 0) ? 'text-success' : 'text-danger' ?>">
                                        <?= ($adj['quantity'] > 0) ? '+' . $adj['quantity'] : $adj['quantity'] ?>
                                    </td>
                                    <td class="text-dark small text-truncate" style="max-width: 120px;" title="<?= htmlspecialchars($adj['reason']) ?>"><?= htmlspecialchars($adj['reason']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Full Width: Recent Purchase Orders -->
<div class="row">
    <div class="col-12">
        <div class="glass-panel border border-secondary shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-receipt text-warning me-2"></i> ใบสั่งซื้อสินค้าเข้าคลังล่าสุด (PO)</h5>
                <a href="/purchases" class="btn btn-xs btn-outline-secondary rounded-pill px-2"><i class="fa-solid fa-list me-1"></i> จัดการใบสั่งซื้อทั้งหมด</a>
            </div>
            
            <div class="table-responsive">
                <table class="table align-middle w-100" width="100%">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>เลขที่ใบสั่งซื้อ</th>
                            <th>ผู้จัดจำหน่าย (ซัพพลายเออร์)</th>
                            <th>วันที่ส่งใบสั่งซื้อ</th>
                            <th>สถานะรับมอบสินค้า</th>
                            <th class="text-end">ยอดราคารวม</th>
                            <th class="text-center">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['recent_purchases'])): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">ไม่พบรายการใบสั่งซื้อสินค้าล่าสุดในระบบ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['recent_purchases'] as $po): ?>
                                <tr class="border-secondary text-light">
                                    <td>
                                        <a href="/purchases/view/<?= $po['id'] ?>" class="text-decoration-none text-info fw-bold">
                                            <code><?= htmlspecialchars($po['purchase_order_no']) ?></code>
                                        </a>
                                    </td>
                                    <td class="text-dark fw-medium"><?= htmlspecialchars($po['supplier_name']) ?></td>
                                    <td class="text-dark"><?= date('d M Y', strtotime($po['order_date'])) ?></td>
                                    <td>
                                        <?php
                                        $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                                        $statusText = $po['status'];
                                        if ($po['status'] === 'Ordered') { $statusBadge = 'bg-info bg-opacity-10 text-info border border-info'; $statusText = 'สั่งซื้อแล้ว'; }
                                        if ($po['status'] === 'Received') { $statusBadge = 'bg-success bg-opacity-10 text-success border border-success'; $statusText = 'รับของแล้ว'; }
                                        if ($po['status'] === 'Cancelled') { $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger'; $statusText = 'ยกเลิกแล้ว'; }
                                        if ($po['status'] === 'Partial') { $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override'; $statusText = 'รับของบางส่วน'; }
                                        ?>
                                        <span class="badge <?= $statusBadge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $statusText ?></span>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">฿<?= number_format($po['total_amount'], 2) ?></td>
                                    <td class="text-center">
                                        <a href="/purchases/view/<?= $po['id'] ?>" class="btn btn-outline-secondary btn-xs rounded-pill px-2">
                                            <i class="fa-solid fa-eye me-1"></i> ดูรายละเอียด
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
