<?php $title = 'ประวัติใบเสร็จการขาย'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 animate-fade-in">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> ประวัติใบเสร็จการขาย</h4>
        <span class="text-secondary small">ค้นหา ตรวจสอบสถานะการชำระเงิน และดำเนินการคืนเงินใบเสร็จ</span>
    </div>
    <a href="/pos" class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm hover-scale">
        <i class="fa-solid fa-cash-register me-2"></i> เปิดหน้าจอขาย (POS)
    </a>
</div>

<!-- Filters Panel -->
<div class="glass-panel mb-4 animate-fade-in" style="animation-delay: 0.1s;">
    <form method="GET" action="/sales" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="search" class="form-label small fw-semibold text-secondary">ค้นหาใบเสร็จ หรือลูกค้า</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="form-control form-control-sm border-start-0 ps-0" id="search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="เลขที่ใบเสร็จ, ชื่อ, เบอร์โทร...">
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="status" class="form-label small fw-semibold text-secondary">สถานะรายการ</label>
            <select class="form-select form-select-sm" id="status" name="status">
                <option value="">ทั้งหมด</option>
                <option value="Completed" <?= ($filters['status'] === 'Completed') ? 'selected' : '' ?>>เสร็จสิ้น</option>
                <option value="Held" <?= ($filters['status'] === 'Held') ? 'selected' : '' ?>>พักบิล</option>
                <option value="Cancelled" <?= ($filters['status'] === 'Cancelled') ? 'selected' : '' ?>>ยกเลิก (คืนเงิน)</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="payment_status" class="form-label small fw-semibold text-secondary">การชำระเงิน</label>
            <select class="form-select form-select-sm" id="payment_status" name="payment_status">
                <option value="">ทั้งหมด</option>
                <option value="Paid" <?= ($filters['payment_status'] === 'Paid') ? 'selected' : '' ?>>ชำระแล้ว</option>
                <option value="Refunded" <?= ($filters['payment_status'] === 'Refunded') ? 'selected' : '' ?>>คืนเงินแล้ว</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="start_date" class="form-label small fw-semibold text-secondary">วันที่เริ่มต้น</label>
            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="end_date" class="form-label small fw-semibold text-secondary">วันที่สิ้นสุด</label>
            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
        </div>
        <div class="col-lg-1 col-md-12 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-pill fw-bold"><i class="fa-solid fa-filter me-1"></i> กรอง</button>
        </div>
    </form>
</div>

<!-- Table Panel -->
<div class="glass-panel animate-fade-in" style="animation-delay: 0.2s;">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="salesTable">
            <thead>
                <tr class="text-secondary small border-light-subtle">
                    <th>เลขที่ใบเสร็จ</th>
                    <th>ชื่อลูกค้า / สมาชิก</th>
                    <th>พนักงานขาย</th>
                    <th>วัน-เวลาที่ขาย</th>
                    <th class="text-center">วิธีชำระเงิน</th>
                    <th class="text-end">ยอดสุทธิ</th>
                    <th class="text-center">สถานะ</th>
                    <th style="width: 160px;" class="text-center">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr class="border-light-subtle">
                        <td>
                            <a href="/sales/view/<?= $sale['id'] ?>" class="text-decoration-none fw-bold text-info">
                                <code><?= htmlspecialchars($sale['invoice_no']) ?></code>
                            </a>
                        </td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($sale['customer_name'] ?? 'ลูกค้าทั่วไป (Walk-in)') ?></td>
                        <td class="text-secondary small"><?= htmlspecialchars($sale['cashier_name']) ?></td>
                        <td class="text-secondary small"><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?> น.</td>
                        <td class="text-center">
                            <?php
                            $method = $sale['payment_method'];
                            $badgePayClass = 'bg-secondary bg-opacity-10 text-secondary';
                            if ($method === 'Cash') { $method = 'เงินสด'; $badgePayClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                            if ($method === 'PromptPay QR') { $method = 'พร้อมเพย์ QR'; $badgePayClass = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10'; }
                            if ($method === 'Credit Card') { $method = 'บัตรเครดิต'; $badgePayClass = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                            if ($method === 'Bank Transfer') { $method = 'โอนเงิน'; $badgePayClass = 'bg-info bg-opacity-10 text-info text-dark-override border border-info border-opacity-10'; }
                            ?>
                            <span class="badge <?= $badgePayClass ?> rounded-pill px-2.5 py-1 small"><?= htmlspecialchars($method) ?></span>
                        </td>
                        <td class="text-end fw-bold text-success">฿<?= number_format($sale['total_amount'], 2) ?></td>
                        <td class="text-center">
                            <?php
                            $badge = 'bg-secondary bg-opacity-10 text-secondary';
                            $statusThai = $sale['status'];
                            if ($sale['status'] === 'Completed') { $statusThai = 'เสร็จสิ้น'; $badge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                            if ($sale['status'] === 'Held') { $statusThai = 'พักบิล'; $badge = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                            if ($sale['status'] === 'Cancelled') { $statusThai = 'ยกเลิกแล้ว'; $badge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10'; }
                            ?>
                            <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill fw-bold" style="font-size: 11px;"><?= $statusThai ?></span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="/sales/view/<?= $sale['id'] ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary">
                                    <i class="fa-solid fa-eye me-1"></i> ดูรายละเอียด
                                </a>
                                <?php if ($sale['status'] === 'Completed' && \App\Helpers\Session::checkRole(['Owner', 'Admin'])): ?>
                                    <button class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-danger refund-btn" data-id="<?= $sale['id'] ?>" data-inv="<?= htmlspecialchars($sale['invoice_no']) ?>">
                                        <i class="fa-solid fa-rotate-left me-1"></i> คืนเงิน
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

<style>
.hover-scale {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-scale:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#salesTable').DataTable({
        responsive: true,
        searching: false, // filtered via top panel
        pageLength: 10,
        order: [[3, 'desc']], // sort by date desc
        language: {
            emptyTable: "ไม่พบข้อมูลรายการใบเสร็จ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            lengthMenu: "แสดง _MENU_ รายการ",
            loadingRecords: "กำลังโหลด...",
            processing: "กำลังประมวลผล...",
            zeroRecords: "ไม่พบรายการที่ค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    // Refund AJAX submission
    $('.refund-btn').on('click', function() {
        const id = $(this).data('id');
        const inv = $(this).data('inv');

        Swal.fire({
            title: 'ยืนยันการคืนเงินใบเสร็จ?',
            text: `ต้องการทำรายการคืนเงินสำหรับใบเสร็จเลขที่ ${inv} ใช่หรือไม่? การดำเนินการนี้จะยกเลิกการชำระเงิน นำสินค้าในบิลทั้งหมดกลับเข้าสต็อกคลังสินค้า และหักคืนคะแนนสะสมที่ได้จากบิลนี้คืน`,
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            color: '#0f172a',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ยืนยัน คืนเงินรายการขาย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'กำลังประมวลผลการคืนเงิน...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/sales/refund/${id}`, {
                    method: 'POST'
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
                    return res.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'ทำการคืนเงินสำเร็จ!',
                        text: 'ยกเลิกรายการขายและนำสินค้ากลับเข้าคลังเรียบร้อยแล้ว.',
                        background: '#ffffff',
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'ตกลง'
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
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'ตกลง'
                    });
                });
            }
        });
    });
});
</script>
