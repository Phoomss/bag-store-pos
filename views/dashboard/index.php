<?php $title = 'แผงควบคุม'; ?>

<!-- Stats Row -->
<div class="row">
    <!-- Today's Sales -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="glass-panel card-stat h-100" style="border-left: 5px solid #3b82f6;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">ยอดขายวันนี้</h6>
                    <h3 class="m-0 fw-bold">฿<?= number_format($stats['today_sales'], 2) ?></h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                    <i class="fa-solid fa-cart-shopping fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Weekly Sales -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="glass-panel card-stat h-100" style="border-left: 5px solid #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">ยอดขายสัปดาห์นี้</h6>
                    <h3 class="m-0 fw-bold">฿<?= number_format($stats['weekly_sales'], 2) ?></h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                    <i class="fa-solid fa-calendar-week fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Monthly Sales -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="glass-panel card-stat h-100" style="border-left: 5px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">ยอดขายเดือนนี้</h6>
                    <h3 class="m-0 fw-bold">฿<?= number_format($stats['monthly_sales'], 2) ?></h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                    <i class="fa-solid fa-calendar-days fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Stock Warnings -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="glass-panel card-stat h-100" style="border-left: 5px solid #ef4444;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase fw-bold mb-1">สต็อกเหลือน้อย / หมด</h6>
                    <h3 class="m-0 fw-bold text-danger"><?= $stats['low_stock_count'] ?> / <?= $stats['out_of_stock_count'] ?></h3>
                </div>
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4">
                    <i class="fa-solid fa-triangle-exclamation fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Row -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="glass-panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-chart-area text-primary me-2"></i> ภาพรวมผลประกอบการการขาย</h5>
                <span class="badge bg-secondary p-2">ย้อนหลัง 30 วัน</span>
            </div>
            <div style="height: 350px; position: relative;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Top Selling & Top Customers -->
    <div class="col-xl-6">
        <!-- Top Products -->
        <div class="glass-panel mb-4">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-fire text-danger me-2"></i> รายการกระเป๋าขายดี</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>ชื่อสินค้า</th>
                            <th>SKU</th>
                            <th class="text-center">จำนวนที่ขายได้</th>
                            <th class="text-end">ยอดขายรวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['top_products'])): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-3">ยังไม่มีประวัติการทำรายการขายสินค้า.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['top_products'] as $prod): ?>
                                <tr class="border-secondary text-light">
                                    <td class="fw-medium"><?= htmlspecialchars($prod['name']) ?></td>
                                    <td><code><?= htmlspecialchars($prod['sku']) ?></code></td>
                                    <td class="text-center fw-bold"><?= $prod['qty_sold'] ?></td>
                                    <td class="text-end text-success fw-bold">฿<?= number_format($prod['total_revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="glass-panel mb-4">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-crown text-warning me-2"></i> ลูกค้าสมาชิกระดับท็อป</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>รหัสลูกค้า</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th class="text-center">จำนวนครั้งที่ซื้อ</th>
                            <th class="text-end">ยอดซื้อรวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['top_customers'])): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-3">ยังไม่มีข้อมูลการซื้อของลูกค้าสมาชิก.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['top_customers'] as $cust): ?>
                                <tr class="border-secondary text-light">
                                    <td><code><?= htmlspecialchars($cust['customer_code']) ?></code></td>
                                    <td class="fw-medium"><?= htmlspecialchars($cust['name']) ?></td>
                                    <td class="text-center fw-bold"><?= $cust['visits'] ?></td>
                                    <td class="text-end text-success fw-bold">฿<?= number_format($cust['total_spent'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Recent Sales & Inventory summary -->
    <div class="col-xl-6">
        <!-- Inventory Value Summary Card -->
        <div class="glass-panel mb-4">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-boxes-packing text-info me-2"></i> มูลค่ารวมคลังสินค้า</h5>
            <div class="row text-center">
                <div class="col-6 mb-3 border-end border-secondary">
                    <h6 class="text-secondary small text-uppercase">จำนวนรายการสินค้า</h6>
                    <h4 class="fw-bold text-light"><?= $stats['inventory_summary']['total_items'] ?> รายการ</h4>
                </div>
                <div class="col-6 mb-3">
                    <h6 class="text-secondary small text-uppercase">จำนวนสินค้าคงคลังทั้งหมด</h6>
                    <h4 class="fw-bold text-light"><?= $stats['inventory_summary']['total_quantity'] ?> ชิ้น</h4>
                </div>
                <div class="col-6 border-end border-secondary">
                    <h6 class="text-secondary small text-uppercase">มูลค่าต้นทุนสินค้า</h6>
                    <h4 class="fw-bold text-warning">฿<?= number_format($stats['inventory_summary']['total_cost_value'], 2) ?></h4>
                </div>
                <div class="col-6">
                    <h6 class="text-secondary small text-uppercase">มูลค่าราคาขายสินค้า</h6>
                    <h4 class="fw-bold text-success">฿<?= number_format($stats['inventory_summary']['total_retail_value'], 2) ?></h4>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="glass-panel mb-4">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i> ประวัติใบเสร็จล่าสุด</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>เลขที่ใบเสร็จ</th>
                            <th>ลูกค้า</th>
                            <th class="text-center">ช่องทาง</th>
                            <th class="text-end">ยอดสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['recent_sales'])): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-3">ยังไม่มีประวัติใบเสร็จการขายล่าสุด.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stats['recent_sales'] as $sale): ?>
                                <tr class="border-secondary text-light">
                                    <td><a href="/sales/view/<?= $sale['id'] ?>" class="text-decoration-none fw-medium text-info"><code><?= htmlspecialchars($sale['invoice_no']) ?></code></a></td>
                                    <td><?= htmlspecialchars($sale['customer_name'] ?? 'ลูกค้าทั่วไป') ?></td>
                                    <td class="text-center small">
                                        <?php
                                        $method = $sale['payment_method'];
                                        if ($method === 'Cash') $method = 'เงินสด';
                                        if ($method === 'PromptPay QR') $method = 'พร้อมเพย์ QR';
                                        if ($method === 'Credit Card') $method = 'บัตรเครดิต';
                                        if ($method === 'Bank Transfer') $method = 'โอนเงิน';
                                        ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($method) ?></span>
                                    </td>
                                    <td class="text-end fw-bold">฿<?= number_format($sale['total_amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/dashboard/stats')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'รายได้รวม (บาท)',
                            data: data.revenue,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3
                        },
                        {
                            label: 'กำไรสุทธิ (บาท)',
                            data: data.profit,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#94a3b8',
                                font: {
                                    family: 'Outfit'
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#94a3b8', font: { family: 'Outfit' } },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        },
                        y: {
                            ticks: { color: '#94a3b8', font: { family: 'Outfit' } },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' }
                        }
                    }
                }
            });
        });
});
</script>
