<?php $title = 'รายงานการวิเคราะห์ธุรกิจ'; ?>

<style>
    .report-tab-btn {
        background-color: var(--bg-secondary) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .report-tab-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }
    .report-tab-btn:hover:not(.active) {
        color: var(--accent-color);
        background-color: rgba(15, 23, 42, 0.04);
    }
    .card-stat {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .date-preset-btn {
        background-color: var(--bg-secondary) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        font-weight: 500;
        font-size: 11px;
        transition: all 0.2s ease;
    }
    .date-preset-btn:hover {
        color: var(--accent-color);
        background-color: rgba(15, 23, 42, 0.04) !important;
        border-color: var(--accent-color) !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-chart-pie text-primary me-2"></i> รายงานวิเคราะห์และผลประกอบการ</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 no-print" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> พิมพ์รายงานสรุป</button>
    </div>
</div>

<!-- Filters Panel -->
<div class="glass-panel mb-4 no-print shadow-sm border border-secondary">
    <form method="GET" action="/reports" id="reportFilterForm">
        <div class="row g-3">
            <div class="col-md-5">
                <label for="start_date" class="form-label small text-secondary fw-semibold">วันที่เริ่มต้น</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-5">
                <label for="end_date" class="form-label small text-secondary fw-semibold">วันที่สิ้นสุด</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100 rounded-pill"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูล</button>
            </div>
        </div>
    </form>
    
    <hr class="border-secondary my-3">
    
    <!-- Quick Date Presets Row -->
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <span class="text-secondary small fw-semibold me-2">ดึงรายงานด่วน:</span>
        <button type="button" class="btn date-preset-btn btn-xs rounded-pill px-3" data-preset="today">วันนี้</button>
        <button type="button" class="btn date-preset-btn btn-xs rounded-pill px-3" data-preset="7days">7 วันล่าสุด</button>
        <button type="button" class="btn date-preset-btn btn-xs rounded-pill px-3" data-preset="month">เดือนนี้</button>
        <button type="button" class="btn date-preset-btn btn-xs rounded-pill px-3" data-preset="year">ปีนี้</button>
    </div>
</div>

<!-- Summary Row -->
<div class="row mb-4">
    <!-- Revenue -->
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="glass-panel p-3 card-stat h-100" style="border-left: 5px solid #3b82f6;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">รายรับรวมรอบบิล</h6>
                    <h3 class="fw-bold text-success m-0">฿<?= number_format($sales_totals['total_sales'], 2) ?></h3>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                    <i class="fa-solid fa-money-bill-wave fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Net Profit -->
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="glass-panel p-3 card-stat h-100" style="border-left: 5px solid #10b981;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">กำไรสุทธิคาดการณ์</h6>
                    <h3 class="fw-bold text-success m-0">฿<?= number_format($sales_totals['total_profit'], 2) ?></h3>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                    <i class="fa-solid fa-vault fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Cash Flow Net -->
    <div class="col-md-4">
        <div class="glass-panel p-3 card-stat h-100" style="border-left: 5px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-secondary small text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">กระแสเงินสดสุทธิ</h6>
                    <h3 class="fw-bold m-0 <?= ($cash_flow_totals['net'] >= 0) ? 'text-success' : 'text-danger' ?>">฿<?= number_format($cash_flow_totals['net'], 2) ?></h3>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                    <i class="fa-solid fa-arrow-right-arrow-left fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Combination Chart Panel -->
<div class="glass-panel mb-4 shadow-sm border border-secondary">
    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-line text-success me-2"></i> แผนภูมิเปรียบเทียบกระแสเงินสดเข้า-ออก</h5>
    <div style="height: 320px; position: relative;">
        <canvas id="cashFlowReportChart"></canvas>
    </div>
</div>

<!-- Reports Details Tabs -->
<div class="glass-panel border border-secondary shadow-sm">
    <!-- Tabs Nav -->
    <ul class="nav nav-pills mb-4 border-bottom border-secondary pb-3 no-print" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn report-tab-btn active btn-sm rounded-pill px-4 me-2" id="sales-tab" data-bs-toggle="pill" data-bs-target="#tab-sales" type="button" role="tab"><i class="fa-solid fa-money-bill-wave me-1"></i> ยอดขายและอัตรากำไร</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn report-tab-btn btn-sm rounded-pill px-4 me-2" id="cashflow-tab" data-bs-toggle="pill" data-bs-target="#tab-cashflow" type="button" role="tab"><i class="fa-solid fa-arrow-right-to-line me-1"></i> สมุดบัญชีกระแสเงินสด</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn report-tab-btn btn-sm rounded-pill px-4" id="expenses-tab" data-bs-toggle="pill" data-bs-target="#tab-expenses" type="button" role="tab"><i class="fa-solid fa-wallet me-1"></i> สรุปประเภทรายจ่าย</button>
        </li>
    </ul>

    <div class="tab-content" id="reportTabsContent">
        <!-- Sales & Profit Margins Tab -->
        <div class="tab-pane fade show active" id="tab-sales" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> รายการใบเสร็จรับเงินและกำไรส่วนต่าง</h6>
            <div class="table-responsive">
                <table class="table align-middle text-light w-100" id="reportSalesTable" style="font-size: 13px;" width="100%">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>เลขที่ใบเสร็จ</th>
                            <th>ลูกค้า</th>
                            <th>พนักงานขาย</th>
                            <th>วัน-เวลาที่ทำรายการ</th>
                            <th class="text-end">ภาษี (VAT)</th>
                            <th class="text-end">ส่วนลดจ่าย</th>
                            <th class="text-end">ยอดชำระสุทธิ</th>
                            <th class="text-end text-success">กำไรสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <tr class="border-secondary text-light">
                                <td><a href="/sales/view/<?= $sale['id'] ?>" class="text-decoration-none fw-medium text-info"><code><?= htmlspecialchars($sale['invoice_no']) ?></code></a></td>
                                <td><?= htmlspecialchars($sale['customer_name'] ?? 'ลูกค้าทั่วไป') ?></td>
                                <td><?= htmlspecialchars($sale['cashier_name']) ?></td>
                                <td><?= date('d M Y H:i', strtotime($sale['created_at'])) ?></td>
                                <td class="text-end">฿<?= number_format($sale['vat_amount'], 2) ?></td>
                                <td class="text-end text-danger">-฿<?= number_format($sale['discount_amount'], 2) ?></td>
                                <td class="text-end fw-bold">฿<?= number_format($sale['total_amount'], 2) ?></td>
                                <td class="text-end fw-bold text-success">฿<?= number_format($sale['profit'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cash Flow Tab -->
        <div class="tab-pane fade" id="tab-cashflow" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-arrow-right-arrow-left text-success me-2"></i> งบกระแสเงินสดรายวัน</h6>
            
            <?php
            $totalDays = count($cash_flow);
            $totalInflow = 0;
            $totalOutflow = 0;
            $positiveDays = 0;

            foreach ($cash_flow as $day) {
                $totalInflow += $day['inflow'];
                $totalOutflow += $day['outflow'];
                if ($day['net'] >= 0) {
                    $positiveDays++;
                }
            }

            $avgInflow = $totalDays > 0 ? $totalInflow / $totalDays : 0;
            $avgOutflow = $totalDays > 0 ? $totalOutflow / $totalDays : 0;
            $profitDayRatio = $totalDays > 0 ? ($positiveDays / $totalDays) * 100 : 0;
            ?>

            <!-- Tab Specific Stats -->
            <div class="row g-3 mb-4 no-print">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-4 border border-secondary">
                        <span class="text-secondary small d-block mb-1" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">เฉลี่ยไหลเข้า / วัน</span>
                        <span class="fw-bold text-success" style="font-size: 1.1rem;"><i class="fa-solid fa-arrow-trend-up me-1"></i> ฿<?= number_format($avgInflow, 2) ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-4 border border-secondary">
                        <span class="text-secondary small d-block mb-1" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">เฉลี่ยไหลออก / วัน</span>
                        <span class="fw-bold text-danger" style="font-size: 1.1rem;"><i class="fa-solid fa-arrow-trend-down me-1"></i> ฿<?= number_format($avgOutflow, 2) ?></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-4 border border-secondary">
                        <span class="text-secondary small d-block mb-1" style="font-size: 10px; font-weight: 600; text-transform: uppercase;">อัตราวันกระแสเป็นบวก</span>
                        <span class="fw-bold text-primary" style="font-size: 1.1rem;"><i class="fa-solid fa-percent me-1"></i> <?= number_format($profitDayRatio, 1) ?>% <span class="small text-secondary fw-normal" style="font-size: 11px;">ของรอบบิล</span></span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle text-light w-100" id="reportCashTable" style="font-size: 13px;" width="100%">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>วันที่</th>
                            <th class="text-end text-success">เงินสดไหลเข้า (ยอดขาย)</th>
                            <th class="text-end text-danger">เงินสดไหลออก (รายจ่าย + ชำระเจ้าหนี้)</th>
                            <th class="text-center" style="width: 220px;">สัดส่วนกระแสเข้า-ออก</th>
                            <th class="text-end">ยอดเงินสดสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cash_flow as $day): 
                            $totalFlow = $day['inflow'] + $day['outflow'];
                            $inflowPercent = $totalFlow > 0 ? ($day['inflow'] / $totalFlow) * 100 : 0;
                            $outflowPercent = $totalFlow > 0 ? ($day['outflow'] / $totalFlow) * 100 : 0;
                        ?>
                            <tr class="border-secondary text-light">
                                <td class="fw-bold text-dark"><?= date('d M Y', strtotime($day['date'])) ?></td>
                                <td class="text-end text-success fw-medium">
                                    <i class="fa-solid fa-arrow-up-long me-1" style="font-size: 10px;"></i> ฿<?= number_format($day['inflow'], 2) ?>
                                </td>
                                <td class="text-end text-danger fw-medium">
                                    <i class="fa-solid fa-arrow-down-long me-1" style="font-size: 10px;"></i> ฿<?= number_format($day['outflow'], 2) ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                                        <span class="text-secondary" style="font-size: 9px; font-weight: bold; width: 15px;">เข้า</span>
                                        <div class="progress flex-grow-1" style="height: 6px; background-color: var(--bg-primary); border-radius: 3px; overflow: hidden; width: 100px;">
                                            <div class="progress-bar bg-success" style="width: <?= $inflowPercent ?>%"></div>
                                            <div class="progress-bar bg-danger" style="width: <?= $outflowPercent ?>%"></div>
                                        </div>
                                        <span class="text-secondary" style="font-size: 9px; font-weight: bold; width: 15px;">ออก</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <?php if ($day['net'] >= 0): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 fw-bold" style="font-size: 11px; display: inline-block; min-width: 110px;">
                                            <i class="fa-solid fa-caret-up me-1"></i> +฿<?= number_format($day['net'], 2) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2 fw-bold" style="font-size: 11px; display: inline-block; min-width: 110px;">
                                            <i class="fa-solid fa-caret-down me-1"></i> ฿<?= number_format($day['net'], 2) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Expenses Tab -->
        <div class="tab-pane fade" id="tab-expenses" role="tabpanel">
            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-wallet text-danger me-2"></i> สรุปประเภทค่าใช้จ่ายการดำเนินงาน</h6>
            <div class="table-responsive">
                <table class="table align-middle text-light w-100" id="reportExpenseTable" style="font-size: 13px;" width="100%">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>ประเภทรายจ่าย</th>
                            <th class="text-end">ยอดรวมรายจ่ายสะสม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="2" class="text-center text-secondary py-4">ไม่มีรายการบันทึกค่าใช้จ่ายในรอบบิลนี้</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $exp): ?>
                                <tr class="border-secondary text-light">
                                    <td class="fw-bold"><?= htmlspecialchars($exp['category']) ?></td>
                                    <td class="text-end text-danger fw-bold">฿<?= number_format($exp['total'], 2) ?></td>
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
    // Initialize DataTables
    $('#reportSalesTable').DataTable({ responsive: true, pageLength: 10, order: [[3, 'desc']] });
    $('#reportCashTable').DataTable({ responsive: true, pageLength: 10, order: [[0, 'desc']] });

    // Auto-adjust column sizing when switching tabs to prevent table collapsing (width: 0px bug)
    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // Quick Date Presets Helper
    $('.date-preset-btn').on('click', function() {
        const preset = $(this).data('preset');
        const today = new Date();
        let start = new Date();
        let end = new Date();

        if (preset === 'today') {
            start = today;
            end = today;
        } else if (preset === '7days') {
            start.setDate(today.getDate() - 7);
            end = today;
        } else if (preset === 'month') {
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        } else if (preset === 'year') {
            start = new Date(today.getFullYear(), 0, 1);
            end = new Date(today.getFullYear(), 12, 0);
        }

        // Format dates as YYYY-MM-DD local time format
        const tzOffset = today.getTimezoneOffset() * 60000; // offset in milliseconds
        const localStart = new Date(start.getTime() - tzOffset);
        const localEnd = new Date(end.getTime() - tzOffset);

        document.getElementById('start_date').value = localStart.toISOString().split('T')[0];
        document.getElementById('end_date').value = localEnd.toISOString().split('T')[0];
        
        document.getElementById('reportFilterForm').submit();
    });

    // Render Analytics Chart
    const cfData = <?= json_encode($cash_flow) ?>;
    
    if (cfData && cfData.length > 0) {
        // Sort chronological order for visual representation
        cfData.sort((a, b) => new Date(a.date) - new Date(b.date));

        const labels = cfData.map(d => {
            const dateObj = new Date(d.date);
            return dateObj.toLocaleDateString('th-TH', { day: 'numeric', month: 'short' });
        });
        
        const inflow = cfData.map(d => parseFloat(d.inflow));
        const outflow = cfData.map(d => parseFloat(d.outflow));
        const net = cfData.map(d => parseFloat(d.net));

        const ctx = document.getElementById('cashFlowReportChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'กระแสเงินสดเข้า (ยอดขาย)',
                        data: inflow,
                        backgroundColor: 'rgba(59, 130, 246, 0.65)',
                        borderColor: '#3b82f6',
                        borderWidth: 1.5,
                        order: 2
                    },
                    {
                        label: 'กระแสเงินสดออก (รายจ่าย)',
                        data: outflow,
                        backgroundColor: 'rgba(239, 68, 68, 0.65)',
                        borderColor: '#ef4444',
                        borderWidth: 1.5,
                        order: 2
                    },
                    {
                        label: 'กระแสเงินสดสุทธิ (Net)',
                        data: net,
                        type: 'line',
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: false,
                        tension: 0.35,
                        borderWidth: 3.5,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#64748b',
                            font: { family: 'Outfit', weight: '600', size: 11 }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#64748b', font: { family: 'Outfit' } },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { color: '#64748b', font: { family: 'Outfit' } },
                        grid: { color: 'rgba(15, 23, 42, 0.04)' }
                    }
                }
            }
        });
    }
});
</script>
