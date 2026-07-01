<?php $title = 'แผงควบคุมแคชเชียร์'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 animate-fade-in">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-cash-register text-primary me-2"></i> แผงควบคุมแคชเชียร์</h4>
        <span class="text-secondary small">เข้าถึงระบบคิดเงิน ดูยอดขายประจำวันของคุณ และจัดการบิลที่พักไว้</span>
    </div>
    <div class="text-end">
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill fw-bold">
            <i class="fa-regular fa-clock me-1"></i> <?= date('d M Y') ?>
        </span>
    </div>
</div>

<!-- Navigation Menu Row -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="/pos" class="text-decoration-none text-dark hover-scale d-block">
            <div class="glass-panel p-4 rounded-4 text-center h-100 border border-primary border-opacity-10 d-flex flex-column align-items-center justify-content-center" style="min-height: 140px;">
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle mb-3">
                    <i class="fa-solid fa-cash-register fa-2xl"></i>
                </div>
                <h5 class="fw-bold m-0 text-primary">เปิดหน้าจอขาย (POS)</h5>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="/sales" class="text-decoration-none text-dark hover-scale d-block">
            <div class="glass-panel p-4 rounded-4 text-center h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 140px;">
                <div class="p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle mb-3">
                    <i class="fa-solid fa-file-invoice-dollar fa-2xl"></i>
                </div>
                <h5 class="fw-bold m-0 text-dark">ประวัติใบเสร็จ</h5>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="/customers" class="text-decoration-none text-dark hover-scale d-block">
            <div class="glass-panel p-4 rounded-4 text-center h-100 d-flex flex-column align-items-center justify-content-center" style="min-height: 140px;">
                <div class="p-3 bg-info bg-opacity-10 text-info rounded-circle mb-3">
                    <i class="fa-solid fa-users fa-2xl"></i>
                </div>
                <h5 class="fw-bold m-0 text-dark">ข้อมูลลูกค้า (CRM)</h5>
            </div>
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <!-- Today's Sales Amount -->
    <div class="col-md-4">
        <div class="glass-panel p-4 rounded-4 h-100 card-stat" style="border-left: 5px solid #10b981 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-2">ยอดขายของคุณวันนี้</h6>
                    <h3 class="m-0 fw-bold text-success">฿<?= number_format($stats['today_sales_amount'], 2) ?></h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                    <i class="fa-solid fa-wallet fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Transactions Count -->
    <div class="col-md-4">
        <div class="glass-panel p-4 rounded-4 h-100 card-stat" style="border-left: 5px solid #3b82f6 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-2">จำนวนบิลที่เสร็จสิ้น</h6>
                    <h3 class="m-0 fw-bold text-primary"><?= number_format($stats['today_sales_count']) ?> บิล</h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                    <i class="fa-solid fa-receipt fa-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Held Sales Count -->
    <div class="col-md-4">
        <div class="glass-panel p-4 rounded-4 h-100 card-stat" style="border-left: 5px solid #f59e0b !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-2">บิลที่พักไว้ (ยังขายไม่เสร็จ)</h6>
                    <h3 class="m-0 fw-bold text-warning"><?= number_format($stats['held_sales_count']) ?> รายการ</h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                    <i class="fa-solid fa-clock-rotate-left fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Held Sales & Recent Sales -->
    <div class="col-xl-8">
        <!-- Held Sales Table -->
        <div class="glass-panel p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-clock text-warning me-2"></i> รายการพักบิลของคุณ</h5>
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                    รอเปิดรายการขายต่อ
                </span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small border-light-subtle">
                            <th>เลขที่บิลพัก</th>
                            <th>ลูกค้า</th>
                            <th>เวลาทำรายการ</th>
                            <th class="text-end">ยอดเงินรวม</th>
                            <th class="text-center">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['held_sales'])): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-40"></i>
                                    ไม่มีรายการพักบิลค้างอยู่
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['held_sales'] as $h): ?>
                                <tr class="border-light-subtle">
                                    <td class="text-dark"><code><?= htmlspecialchars($h['invoice_no']) ?></code></td>
                                    <td class="text-dark"><?= htmlspecialchars($h['customer_name'] ?? 'ลูกค้าทั่วไป') ?></td>
                                    <td class="small text-secondary"><?= date('H:i น. (d/m/Y)', strtotime($h['created_at'])) ?></td>
                                    <td class="text-end fw-bold text-success">฿<?= number_format($h['total_amount'], 2) ?></td>
                                    <td class="text-center">
                                        <a href="/pos?resume=<?= $h['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold hover-scale">
                                            <i class="fa-solid fa-play me-1"></i> ดึงบิลนี้
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Sales Table -->
        <div class="glass-panel p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i> ประวัติการขายวันนี้ของคุณ</h5>
                <a href="/sales" class="btn btn-xs btn-outline-primary rounded-pill px-3 fw-semibold">ดูประวัติทั้งหมด</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small border-light-subtle">
                            <th>เลขที่ใบเสร็จ</th>
                            <th>ลูกค้า</th>
                            <th>เวลา</th>
                            <th class="text-center">วิธีชำระเงิน</th>
                            <th class="text-end">ยอดสุทธิ</th>
                            <th class="text-center">พิมพ์ใบเสร็จ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['recent_sales'])): ?>
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">
                                    <i class="fa-solid fa-receipt fa-2x mb-2 d-block opacity-40"></i>
                                    ยังไม่มีประวัติการขายของคุณในวันนี้
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['recent_sales'] as $sale): ?>
                                <tr class="border-light-subtle">
                                    <td>
                                        <a href="/sales/view/<?= $sale['id'] ?>" class="text-decoration-none fw-bold text-info">
                                            <code><?= htmlspecialchars($sale['invoice_no']) ?></code>
                                        </a>
                                    </td>
                                    <td class="text-dark"><?= htmlspecialchars($sale['customer_name'] ?? 'ลูกค้าทั่วไป') ?></td>
                                    <td class="small text-secondary"><?= date('H:i น.', strtotime($sale['created_at'])) ?></td>
                                    <td class="text-center">
                                        <?php
                                        $method = $sale['payment_method'];
                                        $badgeClass = 'bg-secondary bg-opacity-10 text-secondary';
                                        if ($method === 'Cash') { $method = 'เงินสด'; $badgeClass = 'bg-success bg-opacity-10 text-success'; }
                                        if ($method === 'PromptPay QR') { $method = 'พร้อมเพย์ QR'; $badgeClass = 'bg-primary bg-opacity-10 text-primary'; }
                                        if ($method === 'Credit Card') { $method = 'บัตรเครดิต'; $badgeClass = 'bg-warning bg-opacity-10 text-warning text-dark-override'; }
                                        if ($method === 'Bank Transfer') { $method = 'โอนเงิน'; $badgeClass = 'bg-info bg-opacity-10 text-info text-dark-override'; }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-2.5 py-1 small"><?= htmlspecialchars($method) ?></span>
                                    </td>
                                    <td class="text-end fw-bold text-success">฿<?= number_format($sale['total_amount'], 2) ?></td>
                                    <td class="text-center">
                                        <a href="/pos/receipt/<?= $sale['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 30px;">
                                            <i class="fa-solid fa-print"></i>
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

    <!-- Right Column: Payment Breakdown & Quick Guides -->
    <div class="col-xl-4">
        <!-- Payment Breakdown Doughnut Chart -->
        <div class="glass-panel p-4 rounded-4 mb-4">
            <h5 class="fw-bold m-0 text-dark mb-4"><i class="fa-solid fa-chart-pie text-primary me-2"></i> สัดส่วนการรับเงินวันนี้</h5>
            <?php if (empty($stats['payment_breakdown'])): ?>
                <div class="text-center text-secondary py-5">
                    <i class="fa-solid fa-chart-line fa-2x mb-2 d-block opacity-40"></i>
                    ยังไม่มีข้อมูลการชำระเงินของวันนี้
                </div>
            <?php else: ?>
                <div style="position: relative; height: 180px; margin-bottom: 24px;">
                    <canvas id="paymentModeChart"></canvas>
                </div>
                <div class="payment-legend-container mt-3">
                    <div class="row g-2">
                        <?php foreach ($stats['payment_breakdown'] as $pb): 
                            $method = $pb['payment_method'];
                            $icon = 'fa-money-bill-wave';
                            $color = '#10b981';
                            if ($method === 'Cash') { $methodName = 'เงินสด'; $icon = 'fa-money-bill-wave'; $color = '#10b981'; }
                            elseif ($method === 'PromptPay QR') { $methodName = 'พร้อมเพย์ QR'; $icon = 'fa-qrcode'; $color = '#3b82f6'; }
                            elseif ($method === 'Credit Card') { $methodName = 'บัตรเครดิต'; $icon = 'fa-credit-card'; $color = '#f59e0b'; }
                            elseif ($method === 'Bank Transfer') { $methodName = 'โอนเงิน'; $icon = 'fa-building-columns'; $color = '#06b6d4'; }
                            else { $methodName = $method; $color = '#6b7280'; }
                        ?>
                            <div class="col-6">
                                <div class="p-2 border rounded-3 d-flex align-items-center gap-2" style="font-size: 12px; background-color: #f8fafc; border-color: var(--border-color) !important;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background-color: <?= $color ?>15; color: <?= $color ?>;">
                                        <i class="fa-solid <?= $icon ?>" style="font-size: 10px;"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-truncate text-dark"><?= htmlspecialchars($methodName) ?></div>
                                        <div class="text-secondary fw-semibold">฿<?= number_format($pb['total'], 2) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Help Guidelines Card -->
        <div class="glass-panel p-4 rounded-4" style="border-left: 5px solid var(--accent-color) !important;">
            <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-circle-question text-info me-2"></i> คำแนะนำแคชเชียร์</h5>
            <div class="small">
                <div class="d-flex gap-2 mb-3">
                    <div class="text-info"><i class="fa-solid fa-circle-info fa-lg"></i></div>
                    <div>
                        <strong class="text-dark">การพักบิลสินค้า</strong>
                        <p class="text-secondary m-0 mt-1">หากลูกค้าต้องการเลือกซื้อของต่อ สามารถกด "พักบิล" ในหน้า POS เพื่อให้บริการลูกค้าท่านถัดไปได้ทันที และกลับมาดึงข้อมูลต่อได้จากที่นี่</p>
                    </div>
                </div>
                <div class="d-flex gap-2 mb-3">
                    <div class="text-success"><i class="fa-solid fa-print fa-lg"></i></div>
                    <div>
                        <strong class="text-dark">การพิมพ์ใบเสร็จซ้ำ</strong>
                        <p class="text-secondary m-0 mt-1">หากลูกค้าต้องการใบเสร็จใหม่หรือชำรุด สามารถกดไอคอนพิมพ์ใบเสร็จ <i class="fa-solid fa-print text-secondary"></i> ในตารางประวัติเพื่อเปิดไฟล์พิมพ์ซ้ำได้</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="text-warning"><i class="fa-solid fa-user-plus fa-lg"></i></div>
                    <div>
                        <strong class="text-dark">ระบบสมาชิก CRM</strong>
                        <p class="text-secondary m-0 mt-1">สอบถามเบอร์โทรศัพท์ลูกค้าทุกครั้งเพื่อค้นหาชื่อสมาชิกและสะสมคะแนนในการใช้รับส่วนลดโปรโมชั่น</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-scale {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-scale:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08) !important;
}
.card-stat {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08) !important;
}
</style>

<?php if (!empty($stats['payment_breakdown'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('paymentModeChart').getContext('2d');
    
    const chartLabels = [];
    const chartData = [];
    const chartColors = [];
    
    const colorsMap = {
        'Cash': '#10b981',
        'PromptPay QR': '#3b82f6',
        'Credit Card': '#f59e0b',
        'Bank Transfer': '#06b6d4'
    };
    
    const nameMap = {
        'Cash': 'เงินสด',
        'PromptPay QR': 'พร้อมเพย์ QR',
        'Credit Card': 'บัตรเครดิต',
        'Bank Transfer': 'โอนเงิน'
    };

    <?php foreach ($stats['payment_breakdown'] as $pb): ?>
        chartLabels.push(nameMap['<?= $pb['payment_method'] ?>'] || '<?= $pb['payment_method'] ?>');
        chartData.push(<?= (float)$pb['total'] ?>);
        chartColors.push(colorsMap['<?= $pb['payment_method'] ?>'] || '#6b7280');
    <?php endforeach; ?>

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                backgroundColor: chartColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ฿' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
});
</script>
<?php endif; ?>
