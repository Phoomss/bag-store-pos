<div class="text-center header">
    <h2><?= htmlspecialchars($settings['store_name'] ?? 'ร้านจำหน่ายกระเป๋าพรีเมียม') ?></h2>
    <p><?= htmlspecialchars($settings['store_address'] ?? '123 ถนนแฟชั่น กรุงเทพมหานคร') ?></p>
    <p>โทร: <?= htmlspecialchars($settings['store_phone'] ?? '02-123-4567') ?></p>
</div>

<div class="divider"></div>

<div class="details">
    <p><span class="bold">เลขที่ใบเสร็จ :</span> <?= htmlspecialchars($sale['invoice_no']) ?></p>
    <p><span class="bold">วันที่ทำรายการ:</span> <?= date('d M Y H:i:s', strtotime($sale['created_at'])) ?></p>
    <p><span class="bold">พนักงานขาย  :</span> <?= htmlspecialchars($sale['cashier_name']) ?></p>
    <p><span class="bold">ลูกค้า       :</span> <?= htmlspecialchars($sale['customer_name'] ?? 'ลูกค้าทั่วไป') ?> (<?= htmlspecialchars($sale['customer_code'] ?? 'N/A') ?>)</p>
</div>

<div class="divider"></div>

<table class="table">
    <thead>
        <tr>
            <th style="width: 50%;">รายการสินค้า</th>
            <th style="width: 15%; text-align: center;">จำนวน</th>
            <th style="width: 15%; text-align: right;">ราคา</th>
            <th style="width: 20%; text-align: right;">รวม</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sale['items'] as $item): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($item['product_name']) ?><br>
                    <span style="font-size: 9px; color: #444;">SKU: <?= htmlspecialchars($item['sku']) ?></span>
                </td>
                <td style="text-align: center;"><?= $item['quantity'] ?></td>
                <td style="text-align: right;"><?= number_format($item['selling_price'], 2) ?></td>
                <td style="text-align: right;"><?= number_format($item['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="divider"></div>

<table class="totals-table">
    <tr>
        <td>ยอดรวมสินค้า:</td>
        <td class="text-right">฿<?= number_format($sale['subtotal'], 2) ?></td>
    </tr>
    <?php if ($sale['discount_amount'] > 0): ?>
        <tr>
            <td>ส่วนลดแต้มสะสม:</td>
            <td class="text-right">-฿<?= number_format($sale['discount_amount'], 2) ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td>ภาษีมูลค่าเพิ่ม (<?= $settings['tax_rate'] ?? '7' ?>%):</td>
        <td class="text-right">฿<?= number_format($sale['vat_amount'], 2) ?></td>
    </tr>
    <tr class="bold">
        <td>ยอดชำระสุทธิ:</td>
        <td class="text-right">฿<?= number_format($sale['total_amount'], 2) ?></td>
    </tr>
    <tr class="divider-row">
        <td colspan="2"><div class="divider"></div></td>
    </tr>
    <tr>
        <?php
        $method = $sale['payment_method'];
        if ($method === 'Cash') $method = 'เงินสด';
        if ($method === 'PromptPay QR') $method = 'พร้อมเพย์ QR';
        if ($method === 'Credit Card') $method = 'บัตรเครดิต';
        if ($method === 'Bank Transfer') $method = 'โอนเงินผ่านธนาคาร';
        ?>
        <td>ชำระเงินโดย (<?= htmlspecialchars($method) ?>):</td>
        <td class="text-right">฿<?= number_format($sale['paid_amount'], 2) ?></td>
    </tr>
    <tr>
        <td>เงินทอน:</td>
        <td class="text-right">฿<?= number_format($sale['change_amount'], 2) ?></td>
    </tr>
</table>

<div class="divider"></div>

<div class="text-center" style="font-size: 10px; margin-top: 15px;">
    <p style="white-space: pre-line;"><?= htmlspecialchars($settings['receipt_footer'] ?? "ขอบคุณที่ใช้บริการ!\nกรุณาเก็บใบเสร็จเพื่อเปลี่ยนสินค้าภายใน 7 วัน.") ?></p>
    <p style="margin-top: 10px; font-size: 9px; color: #444;">ระบบพัฒนาโดย Antigravity Bag-POS</p>
</div>
