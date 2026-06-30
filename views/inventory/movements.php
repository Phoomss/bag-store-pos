<?php $title = 'สมุดบัญชีเคลื่อนไหวสินค้า'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-arrow-right-arrow-left text-primary me-2"></i> ความเคลื่อนไหวสต็อกสินค้าคงคลัง</h4>
        <span class="text-secondary small">ประวัติการเคลื่อนย้ายและทำรายการสต็อกแบบละเอียด</span>
    </div>
</div>

<!-- Filters Panel -->
<div class="glass-panel border border-secondary shadow-sm mb-4 no-print">
    <form method="GET" action="/inventory/movements" class="row g-3">
        <div class="col-lg-4 col-md-6">
            <label for="product_id" class="form-label small fw-semibold text-secondary">ตัวกรองสินค้า</label>
            <select class="form-select select2-enable" id="product_id" name="product_id">
                <option value="">สินค้าทั้งหมด</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($filters['product_id'] == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['sku']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label for="type" class="form-label small fw-semibold text-secondary">ประเภทการเคลื่อนไหว</label>
            <select class="form-select" id="type" name="type">
                <option value="">ทุกประเภท</option>
                <option value="Purchase" <?= ($filters['type'] === 'Purchase') ? 'selected' : '' ?>>จัดซื้อเข้าคลัง (Purchase)</option>
                <option value="Sale" <?= ($filters['type'] === 'Sale') ? 'selected' : '' ?>>ขายสินค้าหน้าร้าน (Sale)</option>
                <option value="Adjustment" <?= ($filters['type'] === 'Adjustment') ? 'selected' : '' ?>>ปรับปรุงสต็อก (Adjustment)</option>
                <option value="Transfer" <?= ($filters['type'] === 'Transfer') ? 'selected' : '' ?>>โอนย้ายสต็อก (Transfer)</option>
                <option value="Damage" <?= ($filters['type'] === 'Damage') ? 'selected' : '' ?>>สินค้าชำรุด (Damage)</option>
                <option value="Lost" <?= ($filters['type'] === 'Lost') ? 'selected' : '' ?>>สินค้าสูญหาย (Lost)</option>
                <option value="Return" <?= ($filters['type'] === 'Return') ? 'selected' : '' ?>>รับคืนสินค้า (Return)</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="start_date" class="form-label small fw-semibold text-secondary">วันที่เริ่มต้น</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="end_date" class="form-label small fw-semibold text-secondary">วันที่สิ้นสุด</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
        </div>
        <div class="col-lg-2 col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary w-100 rounded-pill"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูล</button>
        </div>
    </form>
</div>

<!-- Table Panel -->
<div class="glass-panel border border-secondary shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="movementsTable" width="100%">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>วัน-เวลาทำรายการ</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>ประเภทการบันทึก</th>
                    <th class="text-center">ยอดปรับเปลี่ยน</th>
                    <th class="text-center">สต็อกคงเหลือสะสม</th>
                    <th class="text-end">ราคาทุนต่อหน่วย</th>
                    <th class="text-center">เอกสารอ้างอิง</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movements as $m): ?>
                    <tr class="border-secondary text-light">
                        <td class="text-dark"><?= date('d M Y H:i:s', strtotime($m['created_at'])) ?></td>
                        <td>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($m['product_name']) ?></span><br>
                            <span class="text-secondary small" style="font-size: 11px;">SKU: <code><?= htmlspecialchars($m['sku']) ?></code> | บาร์โค้ด: <code><?= htmlspecialchars($m['barcode']) ?></code></span>
                        </td>
                        <td>
                            <?php
                            $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                            $typeText = $m['type'];
                            if ($m['type'] === 'Purchase') {
                                $badge = 'bg-success bg-opacity-10 text-success border border-success';
                                $typeText = 'จัดซื้อเข้าคลัง';
                            }
                            if ($m['type'] === 'Sale') {
                                $badge = 'bg-primary bg-opacity-10 text-primary border border-primary';
                                $typeText = 'ขายออก POS';
                            }
                            if ($m['type'] === 'Adjustment') {
                                $badge = 'bg-info bg-opacity-10 text-info border border-info';
                                $typeText = 'ปรับนับยอด';
                            }
                            if ($m['type'] === 'Transfer') {
                                $badge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override';
                                $typeText = 'โอนย้ายสต็อก';
                            }
                            if ($m['type'] === 'Damage') {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                                $typeText = 'สินค้าชำรุด';
                            }
                            if ($m['type'] === 'Lost') {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                                $typeText = 'สินค้าสูญหาย';
                            }
                            if ($m['type'] === 'Return') {
                                $badge = 'bg-purple bg-opacity-10 text-purple border border-purple';
                                $typeText = 'รับคืนสินค้า';
                            }
                            ?>
                            <span class="badge <?= $badge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $typeText ?></span>
                        </td>
                        <td class="text-center fw-bold <?= ($m['quantity'] > 0) ? 'text-success' : 'text-danger' ?>">
                            <?= ($m['quantity'] > 0) ? '+' . number_format($m['quantity']) : number_format($m['quantity']) ?> ชิ้น
                        </td>
                        <td class="text-center fw-bold text-dark"><?= number_format($m['remaining_stock']) ?> ชิ้น</td>
                        <td class="text-end fw-medium text-dark">฿<?= number_format($m['cost_price'], 2) ?></td>
                        <td class="text-center">
                            <?php if ($m['reference_id']): ?>
                                <?php if ($m['type'] === 'Purchase'): ?>
                                    <a href="/purchases/view/<?= $m['reference_id'] ?>" class="text-decoration-none text-info"><code>#PO-<?= $m['reference_id'] ?></code></a>
                                <?php elseif ($m['type'] === 'Sale' || $m['type'] === 'Return'): ?>
                                    <a href="/sales/view/<?= $m['reference_id'] ?>" class="text-decoration-none text-info"><code>#INV-<?= $m['reference_id'] ?></code></a>
                                <?php else: ?>
                                    <code>#ADJ-<?= $m['reference_id'] ?></code>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-secondary">-</span>
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
    $('#movementsTable').DataTable({
        responsive: true,
        searching: false, // filtered via top panel
        pageLength: 15,
        order: [[0, 'desc']], // sort by date desc
        language: {
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "ไม่พบข้อมูลความเคลื่อนไหวสต็อก",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    $('.select2-enable').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
</script>
