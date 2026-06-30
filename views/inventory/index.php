<?php $title = 'ปรับปรุงสต็อกคงคลัง'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-warehouse text-primary me-2"></i> ปรับปรุงยอดสต็อกสินค้า</h4>
    <button class="btn btn-primary btn-sm rounded-pill px-3 no-print" data-bs-toggle="modal" data-bs-target="#adjustModal">
        <i class="fa-solid fa-calculator me-1"></i> ทำรายการปรับปรุงสต็อก
    </button>
</div>

<!-- adjustments List -->
<div class="glass-panel border border-secondary shadow-sm">
    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> ประวัติการปรับปรุงสต็อก</h5>
    <div class="table-responsive">
        <table class="table align-middle w-100" id="adjustmentsTable" width="100%">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>วัน-เวลาทำรายการ</th>
                    <th>รายละเอียดสินค้า</th>
                    <th>ประเภทการปรับปรุง</th>
                    <th class="text-center">จำนวนปรับปรุง</th>
                    <th>สาเหตุ / หมายเหตุ</th>
                    <th>ผู้ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($adjustments as $adj): ?>
                    <tr class="border-secondary text-light">
                        <td class="text-dark"><?= date('d M Y H:i', strtotime($adj['created_at'])) ?></td>
                        <td>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($adj['product_name']) ?></span><br>
                            <span class="text-secondary small" style="font-size: 11px;">SKU: <code><?= htmlspecialchars($adj['sku']) ?></code> | บาร์โค้ด: <code><?= htmlspecialchars($adj['barcode']) ?></code></span>
                        </td>
                        <td>
                            <?php
                            $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                            $typeText = $adj['type'];
                            if ($adj['type'] === 'Adjustment') {
                                $badge = 'bg-info bg-opacity-10 text-info border border-info';
                                $typeText = 'ปรับปรุงยอดนับ';
                            }
                            if ($adj['type'] === 'Transfer') {
                                $badge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override';
                                $typeText = 'โอนย้ายสต็อก';
                            }
                            if ($adj['type'] === 'Damaged') {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                                $typeText = 'ตัดจำหน่ายสินค้าชำรุด';
                            }
                            if ($adj['type'] === 'Lost') {
                                $badge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                                $typeText = 'สินค้าสูญหาย';
                            }
                            ?>
                            <span class="badge <?= $badge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $typeText ?></span>
                        </td>
                        <td class="text-center fw-bold <?= ($adj['quantity'] > 0) ? 'text-success' : 'text-danger' ?>">
                            <?= ($adj['quantity'] > 0) ? '+' . number_format($adj['quantity']) : number_format($adj['quantity']) ?> ชิ้น
                        </td>
                        <td class="small text-dark"><?= htmlspecialchars($adj['reason']) ?></td>
                        <td class="text-dark"><?= htmlspecialchars($adj['user_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Adjust Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border border-secondary shadow" id="adjustForm">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-calculator text-primary me-2"></i> บันทึกรายการปรับปรุงสต็อก</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="product_id" class="form-label small fw-semibold text-secondary">สินค้า *</label>
                    <select class="form-select select2-enable" id="product_id" name="product_id" required>
                        <option value="">ค้นหาหรือเลือกสินค้า...</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>" data-stock="<?= $p['stock_quantity'] ?>"><?= htmlspecialchars($p['name']) ?> (จำนวนเหลือ: <?= $p['stock_quantity'] ?> ชิ้น) [<?= htmlspecialchars($p['sku']) ?>]</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label small fw-semibold text-secondary">ประเภทรายการปรับปรุง *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="Adjustment">ปรับปรุงยอดนับ (Audit Correction)</option>
                            <option value="Transfer">โอนย้ายสต็อก (Stock Transfer)</option>
                            <option value="Damaged">ตัดจำหน่ายสินค้าชำรุด (Damaged Write-off)</option>
                            <option value="Lost">สินค้าสูญหาย (Shrinkage / Lost)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="quantity" class="form-label small fw-semibold text-secondary">จำนวนปรับสต็อก *</label>
                        <input type="number" class="form-control fw-bold" id="quantity" name="quantity" required placeholder="เช่น +10, -5">
                        <p class="text-secondary small mt-1" style="font-size: 10px; line-height: 1.3;">พิมพ์เครื่องหมายลบเพื่อลดจำนวนสต็อก (เช่น -5), เครื่องหมายบวกเพื่อเพิ่มสต็อก (เช่น +10)</p>
                    </div>
                </div>
                
                <div class="mb-0">
                    <label for="reason" class="form-label small fw-semibold text-secondary">เหตุผลและบันทึกเพิ่มเติม *</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="อธิบายสาเหตุโดยสังเขป เช่น ตรวจนับสต็อกคลาดเคลื่อน, น้ำท่วมสินค้าเสียหาย, โอนสลับระหว่างตู้โชว์กับหลังร้าน..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกข้อมูลปรับสต็อก</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#adjustmentsTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']], // sorted by timestamp desc
        language: {
            search: "_INPUT_",
            searchPlaceholder: "ค้นหาข้อมูลการปรับปรุง...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "ไม่พบรายการปรับปรุงสต็อก",
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
        width: '100%',
        dropdownParent: $('#adjustModal')
    });

    const form = document.getElementById('adjustForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const prod = document.getElementById('product_id');
        const selectedOpt = prod.options[prod.selectedIndex];
        const currentStock = parseInt(selectedOpt.getAttribute('data-stock')) || 0;
        const qtyChange = parseInt(document.getElementById('quantity').value) || 0;

        if (qtyChange === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ระบุจำนวนไม่ถูกต้อง',
                text: 'จำนวนตัวเลขในการปรับปรุงสต็อกต้องไม่เป็นศูนย์ (0).',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        // Validate stock reduction limits
        if (qtyChange < 0 && currentStock < Math.abs(qtyChange)) {
            Swal.fire({
                icon: 'warning',
                title: 'จำนวนสต็อกไม่เพียงพอ',
                text: `ไม่สามารถหักจำนวนสต็อกออกจำนวน ${Math.abs(qtyChange)} ชิ้นได้ เนื่องจากสินค้ามีเหลืออยู่จริงเพียง ${currentStock} ชิ้น.`,
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const data = {
            product_id: parseInt(prod.value),
            type: document.getElementById('type').value,
            quantity: qtyChange,
            reason: document.getElementById('reason').value
        };

        fetch('/inventory/adjust', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'ปรับสต็อกสำเร็จ!',
                text: 'ยอดสต็อกคงคลังได้รับการอัปเดตเรียบร้อยแล้ว.',
                background: '#ffffff',
                color: '#1e293b',
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
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
        });
    });
});
</script>
