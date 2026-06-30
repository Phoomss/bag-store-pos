<?php $title = 'จัดการใบสั่งซื้อสินค้าเข้าคลัง'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-receipt text-primary me-2"></i> ใบสั่งซื้อสินค้าเข้าคลัง (PO)</h4>
    <a href="/purchases/create" class="btn btn-primary btn-sm rounded-pill px-3 no-print">
        <i class="fa-solid fa-plus me-1"></i> สร้างใบสั่งซื้อใหม่
    </a>
</div>

<!-- Purchases List -->
<div class="glass-panel border border-secondary shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="purchasesTable" width="100%">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>เลขที่ใบสั่งซื้อ</th>
                    <th>ผู้จัดจำหน่าย</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>สถานะสต็อก</th>
                    <th class="text-end">ยอดสั่งซื้อรวม</th>
                    <th class="text-end">ชำระแล้ว</th>
                    <th class="text-end text-danger">ยอดคงค้าง</th>
                    <th>สถานะชำระเงิน</th>
                    <th style="width: 200px;" class="text-center no-print">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $p): ?>
                    <tr class="border-secondary text-light">
                        <td>
                            <a href="/purchases/view/<?= $p['id'] ?>" class="text-decoration-none text-info fw-bold">
                                <code><?= htmlspecialchars($p['purchase_order_no']) ?></code>
                            </a>
                            <?php if ($p['invoice_no']): ?>
                                <br><span class="text-secondary small" style="font-size: 11px;">อ้างอิง: <code><?= htmlspecialchars($p['invoice_no']) ?></code></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-dark fw-medium"><?= htmlspecialchars($p['supplier_name']) ?></td>
                        <td class="text-dark"><?= date('d M Y', strtotime($p['order_date'])) ?></td>
                        <td>
                            <?php
                            $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                            $statusText = $p['status'];
                            if ($p['status'] === 'Ordered') {
                                $statusBadge = 'bg-info bg-opacity-10 text-info border border-info';
                                $statusText = 'สั่งซื้อแล้ว';
                            }
                            if ($p['status'] === 'Received') {
                                $statusBadge = 'bg-success bg-opacity-10 text-success border border-success';
                                $statusText = 'รับของแล้ว';
                            }
                            if ($p['status'] === 'Cancelled') {
                                $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                                $statusText = 'ยกเลิกแล้ว';
                            }
                            if ($p['status'] === 'Partial') {
                                $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override';
                                $statusText = 'รับของบางส่วน';
                            }
                            ?>
                            <span class="badge <?= $statusBadge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $statusText ?></span>
                        </td>
                        <td class="text-end fw-semibold text-dark">฿<?= number_format($p['total_amount'], 2) ?></td>
                        <td class="text-end text-success fw-semibold">฿<?= number_format($p['paid_amount'], 2) ?></td>
                        <td class="text-end text-danger fw-bold">฿<?= number_format($p['balance_amount'], 2) ?></td>
                        <td>
                            <?php
                            $payBadge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                            $payText = 'ยังไม่ชำระ';
                            if ($p['payment_status'] === 'Paid') {
                                $payBadge = 'bg-success bg-opacity-10 text-success border border-success';
                                $payText = 'ชำระเงินแล้ว';
                            }
                            if ($p['payment_status'] === 'Partial') {
                                $payBadge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override';
                                $payText = 'ชำระบางส่วน';
                            }
                            ?>
                            <span class="badge <?= $payBadge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $payText ?></span>
                        </td>
                        <td class="no-print">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="/purchases/view/<?= $p['id'] ?>" class="btn btn-outline-secondary btn-xs rounded-pill px-2">
                                    <i class="fa-solid fa-eye"></i> ดูรายละเอียด
                                </a>
                                <?php if ($p['status'] !== 'Received' && $p['status'] !== 'Cancelled'): ?>
                                    <button class="btn btn-outline-success btn-xs rounded-pill px-2 receive-btn" data-id="<?= $p['id'] ?>" data-po="<?= htmlspecialchars($p['purchase_order_no']) ?>">
                                        <i class="fa-solid fa-check"></i> รับของ
                                    </button>
                                <?php endif; ?>
                                <?php if ($p['payment_status'] !== 'Paid' && $p['status'] !== 'Cancelled'): ?>
                                    <button class="btn btn-outline-warning btn-xs rounded-pill px-2 pay-btn" data-id="<?= $p['id'] ?>" data-balance="<?= $p['balance_amount'] ?>" data-po="<?= htmlspecialchars($p['purchase_order_no']) ?>">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> จ่ายเงิน
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pay Modal -->
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border border-secondary shadow" id="payForm">
            <input type="hidden" id="payPurchaseId">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-hand-holding-dollar text-warning me-2"></i> บันทึกการชำระเงินเจ้าหนี้: <span id="payPoNo"></span></h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label text-secondary small fw-semibold">ยอดเงินคงค้างชำระทั้งหมด</label>
                    <input type="text" class="form-control text-dark fw-bold" id="payBalanceVal" disabled readonly style="background-color: rgba(15,23,42,0.02);">
                </div>
                <div class="mb-3">
                    <label for="payAmount" class="form-label small fw-semibold text-secondary">จำนวนเงินที่ชำระเงิน *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control fw-bold" id="payAmount" required placeholder="0.00">
                    </div>
                </div>
                <div class="row mb-3 g-3">
                    <div class="col-6">
                        <label for="payMethod" class="form-label small fw-semibold text-secondary">ช่องทางการชำระเงิน *</label>
                        <select class="form-select" id="payMethod" required>
                            <option value="Cash">เงินสด</option>
                            <option value="Bank Transfer">โอนเงินผ่านธนาคาร</option>
                            <option value="Cheque">เช็คธนาคาร</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="payDate" class="form-label small fw-semibold text-secondary">วันที่ทำรายการชำระ *</label>
                        <input type="date" class="form-control" id="payDate" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="mb-0">
                    <label for="payRef" class="form-label small fw-semibold text-secondary">หมายเลขอ้างอิงสลิป / เลขที่เช็ค (ถ้ามี)</label>
                    <input type="text" class="form-control" id="payRef" placeholder="เช่น เลขที่ธุรกรรมสลิปโอนเงิน...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-warning text-white rounded-pill px-4">บันทึกชำระเงิน</button>
            </div>
        </form>
    </div>
</div>

<!-- Receive Modal -->
<div class="modal fade" id="receiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border border-secondary shadow" id="receiveForm">
            <input type="hidden" id="receivePurchaseId">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-check text-success me-2"></i> บันทึกรับสินค้าส่งมอบเข้าสต็อก: <span id="receivePoNo"></span></h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary small mb-4" style="line-height: 1.4;">
                    <strong>ข้อควรระวัง:</strong> การทำรายการรับมอบสินค้าจะทำการปรับปรุงจำนวนสต็อกสินค้าในระบบเพิ่มเข้ามาโดยอัตโนมัติตามยอดสั่งซื้อ และบันทึกประวัติความเคลื่อนไหวสต็อกทันที ซึ่งรายการนี้ไม่สามารถทำคืนหรือยกเลิกภายหลังการกดยืนยันได้
                </p>
                <div class="mb-0">
                    <label for="receiveInvoice" class="form-label small fw-semibold text-secondary">เลขที่ใบส่งสินค้า / ใบกำกับภาษีของซัพพลายเออร์</label>
                    <input type="text" class="form-control" id="receiveInvoice" placeholder="เช่น INV-1234...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success rounded-pill px-4">ยืนยันการรับของ</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#purchasesTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[2, 'desc']], // sort by order date desc
        language: {
            search: "_INPUT_",
            searchPlaceholder: "ค้นหาใบสั่งซื้อ...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "ไม่พบรายการสั่งซื้อสินค้า",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    // Populate Pay Modal
    $('.pay-btn').on('click', function() {
        const id = $(this).data('id');
        const balance = $(this).data('balance');
        const po = $(this).data('po');

        $('#payPurchaseId').val(id);
        $('#payPoNo').text(po);
        $('#payBalanceVal').val('฿' + parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#payAmount').val(balance).attr('max', balance); // defaults to full outstanding

        $('#payModal').modal('show');
    });

    // Pay Form Submit AJAX
    const payForm = document.getElementById('payForm');
    payForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('payPurchaseId').value;
        const data = {
            amount: parseFloat(document.getElementById('payAmount').value),
            payment_method: document.getElementById('payMethod').value,
            payment_date: document.getElementById('payDate').value,
            reference_no: document.getElementById('payRef').value
        };

        fetch(`/purchases/pay/${id}`, {
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
                title: 'บันทึกสำเร็จ!',
                text: 'บันทึกรายการชำระเงินค่าสินค้าเข้าสต็อกเรียบร้อยแล้ว.',
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

    // Populate Receive Modal
    $('.receive-btn').on('click', function() {
        $('#receivePurchaseId').val($(this).data('id'));
        $('#receivePoNo').text($(this).data('po'));
        $('#receiveModal').modal('show');
    });

    // Receive Form Submit AJAX
    const receiveForm = document.getElementById('receiveForm');
    receiveForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('receivePurchaseId').value;
        const data = {
            status: 'Received',
            invoice_no: document.getElementById('receiveInvoice').value
        };

        fetch(`/purchases/status/${id}`, {
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
                title: 'บันทึกรับของแล้ว!',
                text: 'รับมอบส่งสินค้าเข้าสต็อก และอัปเดตยอดคงคลังสำเร็จ.',
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
