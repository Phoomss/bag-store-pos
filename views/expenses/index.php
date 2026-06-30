<?php $title = 'บันทึกค่าใช้จ่าย'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-wallet text-primary me-2"></i> บันทึกค่าใช้จ่ายการดำเนินงาน</h4>
    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-plus me-1"></i> บันทึกค่าใช้จ่าย
    </button>
</div>

<?php
$categoriesMapping = [
    'Utilities' => 'สาธารณูปโภค (น้ำ/ไฟ)',
    'Salary' => 'เงินเดือนพนักงาน',
    'Rent' => 'ค่าเช่าสถานที่',
    'Internet' => 'ค่าอินเทอร์เน็ต',
    'Transportation' => 'ค่าเดินทาง/ขนส่ง',
    'Marketing' => 'ค่าโฆษณา/การตลาด',
    'Maintenance' => 'ค่าซ่อมแซม/บำรุงรักษา',
    'Others' => 'ค่าใช้จ่ายอื่นๆ'
];

$categoryTotals = [];
$totalExpensesAmt = 0.00;
foreach ($expenses as $exp) {
    $categoryTotals[$exp['category']] = ($categoryTotals[$exp['category']] ?? 0.00) + $exp['amount'];
    $totalExpensesAmt += $exp['amount'];
}
?>

<!-- Category Valuation Summary -->
<div class="glass-panel mb-4 shadow-sm border border-secondary">
    <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-chart-pie me-2"></i> สรุปยอดค่าใช้จ่ายแยกตามหมวดหมู่</h5>
    <div class="row text-center">
        <div class="col-12 mb-4">
            <h6 class="text-secondary small text-uppercase fw-semibold">รวมค่าใช้จ่ายทั้งหมดในรอบระยะเวลา</h6>
            <h2 class="fw-bold text-danger">฿<?= number_format($totalExpensesAmt, 2) ?></h2>
        </div>
        
        <?php foreach ($categoriesMapping as $key => $label): ?>
            <?php $total = $categoryTotals[$key] ?? 0.00; ?>
            <div class="col-lg-3 col-6 mb-3">
                <div class="p-3 rounded-4 bg-light border border-secondary h-100 d-flex flex-column justify-content-center">
                    <span class="text-secondary small fw-medium d-block mb-1"><?= $label ?></span>
                    <h5 class="fw-bold text-dark m-0">฿<?= number_format($total, 2) ?></h5>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Filters Panel -->
<div class="glass-panel mb-4 shadow-sm border border-secondary">
    <form method="GET" action="/expenses" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="category" class="form-label small text-secondary fw-semibold">หมวดหมู่ค่าใช้จ่าย</label>
            <select class="form-select form-select-sm" id="category" name="category">
                <option value="">ทุกหมวดหมู่</option>
                <?php foreach ($categoriesMapping as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($filters['category'] === $key) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-3 col-md-6">
            <label for="start_date" class="form-label small text-secondary fw-semibold">วันที่เริ่มต้น</label>
            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
        </div>
        <div class="col-lg-3 col-md-6">
            <label for="end_date" class="form-label small text-secondary fw-semibold">วันที่สิ้นสุด</label>
            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
        </div>
        <div class="col-lg-3 col-md-6 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-pill"><i class="fa-solid fa-filter me-1"></i> กรองประวัติ</button>
        </div>
    </form>
</div>

<!-- Table Panel -->
<div class="glass-panel shadow-sm border border-secondary">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="expensesTable">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>วันที่จ่าย</th>
                    <th>หมวดหมู่</th>
                    <th>รายละเอียดค่าใช้จ่าย</th>
                    <th class="text-end">จำนวนเงิน</th>
                    <th>พนักงานผู้บันทึก</th>
                    <th style="width: 120px;" class="text-center">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expenses as $exp): ?>
                    <tr class="border-secondary text-light">
                        <td><?= date('d M Y', strtotime($exp['expense_date'])) ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($categoriesMapping[$exp['category']] ?? $exp['category']) ?></span>
                        </td>
                        <td class="small text-secondary"><?= htmlspecialchars($exp['description'] ?? 'ไม่ได้ระบุ') ?></td>
                        <td class="text-end fw-bold text-danger">฿<?= number_format($exp['amount'], 2) ?></td>
                        <td><?= htmlspecialchars($exp['user_name']) ?></td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3 delete-btn" data-id="<?= $exp['id'] ?>">
                                    <i class="fa-solid fa-trash me-1"></i> ลบ
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border border-secondary shadow" id="createForm">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus text-primary me-2"></i> บันทึกค่าใช้จ่ายการดำเนินงาน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="createCategory" class="form-label fw-semibold">หมวดหมู่ค่าใช้จ่าย *</label>
                    <select class="form-select" id="createCategory" name="category" required>
                        <?php foreach ($categoriesMapping as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="createAmount" class="form-label fw-semibold">จำนวนเงิน (บาท) *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                            <input type="number" step="0.01" class="form-control" id="createAmount" name="amount" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="createDate" class="form-label fw-semibold">วันที่จ่าย *</label>
                        <input type="date" class="form-control" id="createDate" name="expense_date" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="createDesc" class="form-label fw-semibold">รายละเอียดการจ่ายเงิน (เพิ่มเติม)</label>
                    <textarea class="form-control" id="createDesc" name="description" rows="3" placeholder="ระบุวัตถุประสงค์การจ่าย เช่น ค่าไฟฟ้าประจำสาขาเดือน มิ.ย., ค่าซ่อมคอมพิวเตอร์พนักงาน..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกค่าใช้จ่าย</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#expensesTable').DataTable({
        responsive: true,
        searching: false,
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            search: "_INPUT_"
        }
    });

    // Create AJAX
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            category: document.getElementById('createCategory').value,
            amount: parseFloat(document.getElementById('createAmount').value),
            expense_date: document.getElementById('createDate').value,
            description: document.getElementById('createDesc').value
        };

        fetch('/expenses/create', {
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
                text: 'บันทึกค่าใช้จ่ายใหม่เข้าระบบเรียบร้อยแล้ว.',
                background: '#ffffff',
                color: '#0f172a',
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
                color: '#0f172a',
                confirmButtonColor: '#3b82f6'
            });
        });
    });

    // Delete AJAX
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'ต้องการลบประวัตินี้?',
            text: "ประวัติบันทึกรายการจ่ายเงินนี้จะถูกลบออกอย่างถาวร!",
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            color: '#0f172a',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'ยืนยัน, ลบประวัติ!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/expenses/delete/${id}`, {
                    method: 'POST'
                })
                .then(res => {
                    if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
                    return res.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ!',
                        text: 'ลบรายการค่าใช้จ่ายออกจากระบบแล้ว.',
                        background: '#ffffff',
                        color: '#0f172a',
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
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6'
                    });
                });
            }
        });
    });
});
</script>
