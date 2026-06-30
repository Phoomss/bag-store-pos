<?php $title = 'รายละเอียดใบสั่งซื้อสินค้าเข้าคลัง'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-eye text-primary me-2"></i> รายละเอียดใบสั่งซื้อ: <code><?= htmlspecialchars($purchase['purchase_order_no']) ?></code></h4>
    <a href="/purchases" class="btn btn-secondary btn-sm rounded-pill px-3 no-print">
        <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าจัดการสั่งซื้อ
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Details panel -->
    <div class="col-lg-8">
        <!-- Items Bought -->
        <div class="glass-panel border border-secondary shadow-sm mb-4">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-boxes-packing text-primary me-2"></i> รายการสินค้าที่จัดซื้อ</h5>
            <div class="table-responsive">
                <table class="table align-middle text-light w-100" style="font-size: 13px;" width="100%">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>ชื่อสินค้า</th>
                            <th>รหัสสินค้า (SKU)</th>
                            <th class="text-end">ราคาทุนต่อหน่วย</th>
                            <th class="text-center">จำนวนสั่งซื้อ</th>
                            <th class="text-center">จำนวนที่ได้รับ</th>
                            <th class="text-end">ยอดรวมย่อย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchase['items'] as $item): ?>
                            <tr class="border-secondary text-light">
                                <td class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><code><?= htmlspecialchars($item['sku']) ?></code></td>
                                <td class="text-end text-dark fw-medium">฿<?= number_format($item['cost_price'], 2) ?></td>
                                <td class="text-center text-dark fw-medium"><?= number_format($item['quantity']) ?> ชิ้น</td>
                                <td class="text-center">
                                    <?php if ($item['received_quantity'] >= $item['quantity']): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-1 fw-bold"><?= number_format($item['received_quantity']) ?> ชิ้น</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3 py-1 fw-bold"><?= number_format($item['received_quantity']) ?> ชิ้น</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-success fw-bold">฿<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments Ledger -->
        <div class="glass-panel border border-secondary shadow-sm">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-list-check text-primary me-2"></i> สมุดบัญชีการจ่ายเงินเจ้าหนี้</h5>
            <div class="table-responsive">
                <table class="table align-middle text-light w-100" style="font-size: 13px;" width="100%">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>หมายเลขอ้างอิง / เลขที่ธุรกรรม</th>
                            <th>วันที่ชำระเงิน</th>
                            <th>ช่องทางการชำระ</th>
                            <th class="text-end">จำนวนเงินที่ชำระ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchase['payments'])): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">ยังไม่มีการบันทึกประวัติการจ่ายเงินสำหรับใบสั่งซื้อนี้</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchase['payments'] as $pay): ?>
                                <tr class="border-secondary text-light">
                                    <td><code><?= htmlspecialchars($pay['reference_no'] ?? 'N/A') ?></code></td>
                                    <td class="text-dark fw-medium"><?= date('d M Y', strtotime($pay['payment_date'])) ?></td>
                                    <td>
                                        <?php
                                        $payMethodText = $pay['payment_method'];
                                        if ($pay['payment_method'] === 'Cash') $payMethodText = 'เงินสด';
                                        if ($pay['payment_method'] === 'Bank Transfer') $payMethodText = 'โอนผ่านธนาคาร';
                                        if ($pay['payment_method'] === 'Cheque') $payMethodText = 'เช็คธนาคาร';
                                        ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3 py-1 fw-bold"><?= htmlspecialchars($payMethodText) ?></span>
                                    </td>
                                    <td class="text-end text-success fw-bold">฿<?= number_format($pay['amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Status info panel -->
    <div class="col-lg-4">
        <div class="glass-panel border border-secondary shadow-sm">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-file-invoice text-primary me-2"></i> สรุปข้อมูลสั่งซื้อ</h5>
            
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">เลขที่ใบสั่งซื้อ (PO):</span>
                <span class="fw-bold"><code><?= htmlspecialchars($purchase['purchase_order_no']) ?></code></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">ผู้จัดจำหน่าย:</span>
                <span class="fw-bold text-dark"><?= htmlspecialchars($purchase['supplier_name']) ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">วันที่สั่งซื้อ:</span>
                <span class="text-dark fw-medium"><?= date('d M Y', strtotime($purchase['order_date'])) ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">วันที่รับสินค้าเข้าคลัง:</span>
                <span class="text-dark fw-medium"><?= $purchase['received_date'] ? date('d M Y', strtotime($purchase['received_date'])) : '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">รอรับของ</span>' ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">ผู้รับผิดชอบสั่งซื้อ:</span>
                <span class="text-dark fw-medium"><?= htmlspecialchars($purchase['user_name']) ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">สถานะใบสั่งซื้อ:</span>
                <?php
                $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                $statusText = $purchase['status'];
                if ($purchase['status'] === 'Ordered') {
                    $statusBadge = 'bg-info bg-opacity-10 text-info border border-info';
                    $statusText = 'สั่งซื้อแล้ว';
                }
                if ($purchase['status'] === 'Received') {
                    $statusBadge = 'bg-success bg-opacity-10 text-success border border-success';
                    $statusText = 'รับของแล้ว';
                }
                if ($purchase['status'] === 'Cancelled') {
                    $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                    $statusText = 'ยกเลิกแล้ว';
                }
                if ($purchase['status'] === 'Partial') {
                    $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override';
                    $statusText = 'รับของบางส่วน';
                }
                ?>
                <span class="badge <?= $statusBadge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $statusText ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-2">
                <span class="text-secondary small fw-semibold">สถานะชำระเงิน:</span>
                <?php
                $payBadge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                $payText = 'ยังไม่ชำระ';
                if ($purchase['payment_status'] === 'Paid') {
                    $payBadge = 'bg-success bg-opacity-10 text-success border border-success';
                    $payText = 'ชำระเงินแล้ว';
                }
                if ($purchase['payment_status'] === 'Partial') {
                    $payBadge = 'bg-warning bg-opacity-10 text-warning border border-warning text-dark-override';
                    $payText = 'ชำระบางส่วน';
                }
                ?>
                <span class="badge <?= $payBadge ?> rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;"><?= $payText ?></span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">ยอดรวมทั้งใบสั่งซื้อ:</span>
                <h5 class="fw-bold m-0 text-success">฿<?= number_format($purchase['total_amount'], 2) ?></h5>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary small fw-semibold">ยอดเงินจ่ายแล้ว:</span>
                <h5 class="fw-bold m-0 text-dark">฿<?= number_format($purchase['paid_amount'], 2) ?></h5>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small fw-semibold">ยอดค้างชำระสะสม:</span>
                <h5 class="fw-bold m-0 text-danger">฿<?= number_format($purchase['balance_amount'], 2) ?></h5>
            </div>
        </div>
    </div>
</div>
