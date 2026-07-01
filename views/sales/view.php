<?php $title = 'รายละเอียดใบเสร็จรับเงิน'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 animate-fade-in">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> รายละเอียดใบเสร็จ: <code><?= htmlspecialchars($sale['invoice_no']) ?></code></h4>
        <span class="text-secondary small">ข้อมูลรายการสินค้า ข้อมูลการชำระเงิน และการทำรายการของบิลนี้</span>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold hover-scale" onclick="window.open('/pos/receipt/<?= $sale['id'] ?>', '_blank', 'width=450,height=600')">
            <i class="fa-solid fa-print me-1"></i> พิมพ์ใบเสร็จ
        </button>
        <a href="/sales" class="btn btn-secondary btn-sm rounded-pill px-3 fw-semibold hover-scale">
            <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าประวัติ
        </a>
    </div>
</div>

<div class="row g-4 animate-fade-in" style="animation-delay: 0.1s;">
    <!-- Left Column: Items and payments -->
    <div class="col-lg-8">
        <!-- Items table -->
        <div class="glass-panel mb-4 p-4 rounded-4">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-list-check text-primary me-2"></i> รายการสินค้าในใบเสร็จ</h5>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 14px;">
                    <thead>
                        <tr class="text-secondary small border-light-subtle">
                            <th>ชื่อสินค้า</th>
                            <th>รหัส SKU</th>
                            <th class="text-end">ราคา/ชิ้น</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">ราคารวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sale['items'] as $item): ?>
                            <tr class="border-light-subtle">
                                <td class="fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><code><?= htmlspecialchars($item['sku']) ?></code></td>
                                <td class="text-end text-secondary">฿<?= number_format($item['selling_price'], 2) ?></td>
                                <td class="text-center text-dark fw-semibold"><?= $item['quantity'] ?> ชิ้น</td>
                                <td class="text-end text-success fw-bold">฿<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments list -->
        <div class="glass-panel p-4 rounded-4">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-receipt text-success me-2"></i> บันทึกข้อมูลการชำระเงิน</h5>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-light-subtle">
                            <th>วิธีชำระเงิน</th>
                            <th>จำนวนเงินที่ชำระ</th>
                            <th>หมายเลขอ้างอิงการชำระ</th>
                            <th>วัน-เวลาที่ชำระ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sale['payments'])): ?>
                            <tr class="border-light-subtle">
                                <td>
                                    <?php
                                    $method = $sale['payment_method'];
                                    $badgePayClass = 'bg-secondary bg-opacity-10 text-secondary';
                                    if ($method === 'Cash') { $method = 'เงินสด'; $badgePayClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                                    if ($method === 'PromptPay QR') { $method = 'พร้อมเพย์ QR'; $badgePayClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10'; }
                                    if ($method === 'Credit Card') { $method = 'บัตรเครดิต'; $badgePayClass = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                                    if ($method === 'Bank Transfer') { $method = 'โอนเงิน'; $badgePayClass = 'bg-info bg-opacity-10 text-info text-dark-override border border-info border-opacity-10'; }
                                    ?>
                                    <span class="badge <?= $badgePayClass ?> rounded-pill px-2.5 py-1"><?= htmlspecialchars($method) ?></span>
                                </td>
                                <td class="text-success fw-bold">฿<?= number_format($sale['paid_amount'], 2) ?></td>
                                <td><code><?= htmlspecialchars($sale['reference_no'] ?? 'ไม่มีข้อมูล') ?></code></td>
                                <td class="text-secondary"><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?> น.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sale['payments'] as $pay): ?>
                                <tr class="border-light-subtle">
                                    <td>
                                        <?php
                                        $method = $pay['payment_method'];
                                        $badgePayClass = 'bg-secondary bg-opacity-10 text-secondary';
                                        if ($method === 'Cash') { $method = 'เงินสด'; $badgePayClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                                        if ($method === 'PromptPay QR') { $method = 'พร้อมเพย์ QR'; $badgePayClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10'; }
                                        if ($method === 'Credit Card') { $method = 'บัตรเครดิต'; $badgePayClass = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                                        if ($method === 'Bank Transfer') { $method = 'โอนเงิน'; $badgePayClass = 'bg-info bg-opacity-10 text-info text-dark-override border border-info border-opacity-10'; }
                                        ?>
                                        <span class="badge <?= $badgePayClass ?> rounded-pill px-2.5 py-1"><?= htmlspecialchars($method) ?></span>
                                    </td>
                                    <td class="text-success fw-bold">฿<?= number_format($pay['amount'], 2) ?></td>
                                    <td><code><?= htmlspecialchars($pay['reference_no'] ?? 'ไม่มีข้อมูล') ?></code></td>
                                    <td class="text-secondary"><?= date('d/m/Y H:i', strtotime($pay['created_at'])) ?> น.</td>
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
        <div class="glass-panel p-4 rounded-4">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-line text-primary me-2"></i> สรุปผลการทำรายการ</h5>
            
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">เลขที่ใบเสร็จ:</span>
                <span class="fw-bold text-dark"><code><?= htmlspecialchars($sale['invoice_no']) ?></code></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">ชื่อลูกค้า / สมาชิก:</span>
                <span class="fw-bold text-dark"><?= htmlspecialchars($sale['customer_name'] ?? 'ลูกค้าทั่วไป (Walk-in)') ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">รหัสสมาชิก CRM:</span>
                <span><code><?= htmlspecialchars($sale['customer_code'] ?? 'N/A') ?></code></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">พนักงานแคชเชียร์:</span>
                <span class="text-dark fw-semibold"><?= htmlspecialchars($sale['cashier_name']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">วัน-เวลาทำรายการ:</span>
                <span class="text-secondary small"><?= date('d/m/Y H:i:s', strtotime($sale['created_at'])) ?> น.</span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">ช่องทางชำระเงิน:</span>
                <?php
                $method = $sale['payment_method'];
                $badgePayClass = 'bg-secondary bg-opacity-10 text-secondary';
                if ($method === 'Cash') { $method = 'เงินสด'; $badgePayClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                if ($method === 'PromptPay QR') { $method = 'พร้อมเพย์ QR'; $badgePayClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10'; }
                if ($method === 'Credit Card') { $method = 'บัตรเครดิต'; $badgePayClass = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                if ($method === 'Bank Transfer') { $method = 'โอนเงิน'; $badgePayClass = 'bg-info bg-opacity-10 text-info text-dark-override border border-info border-opacity-10'; }
                ?>
                <span class="badge <?= $badgePayClass ?> rounded-pill px-2.5 py-1 small"><?= htmlspecialchars($method) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-light-subtle pb-2">
                <span class="text-secondary small">สถานะใบเสร็จ:</span>
                <?php
                $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10';
                $statusThai = $sale['status'];
                if ($sale['status'] === 'Completed') { $statusThai = 'เสร็จสิ้น'; $statusBadge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                if ($sale['status'] === 'Cancelled') { $statusThai = 'ยกเลิกแล้ว'; $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10'; }
                if ($sale['status'] === 'Held') { $statusThai = 'พักบิล'; $statusBadge = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                ?>
                <span class="badge <?= $statusBadge ?> px-2.5 py-1 rounded-pill fw-bold"><?= $statusThai ?></span>
            </div>
            
            <?php if ($sale['notes']): ?>
                <div class="mb-3 border-bottom border-light-subtle pb-2">
                    <span class="text-secondary small d-block mb-1">หมายเหตุเพิ่มเติม:</span>
                    <p class="small text-dark m-0" style="white-space: pre-wrap;"><?= htmlspecialchars($sale['notes']) ?></p>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-2 mt-4">
                <span class="text-secondary">ยอดรวมก่อนหัก:</span>
                <span class="text-dark fw-semibold">฿<?= number_format($sale['subtotal'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">ส่วนลดคะแนนสะสม:</span>
                <span class="text-danger fw-semibold">-฿<?= number_format($sale['discount_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">ภาษีมูลค่าเพิ่ม (VAT 7%):</span>
                <span class="text-dark">฿<?= number_format($sale['vat_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2 pt-2 border-top border-light-subtle">
                <span class="text-dark fw-bold">ยอดเงินสุทธิ:</span>
                <h5 class="fw-bold m-0 text-success">฿<?= number_format($sale['total_amount'], 2) ?></h5>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">รับเงินมา:</span>
                <span class="fw-bold text-dark">฿<?= number_format($sale['paid_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">เงินทอน:</span>
                <span class="fw-bold text-warning">฿<?= number_format($sale['change_amount'], 2) ?></span>
            </div>
        </div>
    </div>
</div>
