<?php $title = 'จัดการรายชื่อผู้จัดจำหน่าย'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-truck-field text-primary me-2"></i> รายชื่อคู่ค้า / ผู้จัดจำหน่าย</h4>
    <button class="btn btn-primary btn-sm rounded-pill px-3 no-print" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-plus me-1"></i> เพิ่มผู้จัดจำหน่ายใหม่
    </button>
</div>

<!-- Table Panel -->
<div class="glass-panel border border-secondary shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="suppliersTable" width="100%">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>ผู้จัดจำหน่าย</th>
                    <th>ผู้ประสานงาน / ช่องทางติดต่อ</th>
                    <th class="text-end">ยอดสั่งคลังรวม</th>
                    <th class="text-end">ชำระสะสม</th>
                    <th class="text-end text-danger">ยอดคงค้างสะสม</th>
                    <th style="width: 260px;" class="text-center no-print">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $sup): ?>
                    <tr class="border-secondary text-light">
                        <td>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($sup['name']) ?></span><br>
                            <span class="text-secondary small" style="font-size: 11px;"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($sup['address'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <span class="text-dark fw-medium"><?= htmlspecialchars($sup['contact_name'] ?? 'N/A') ?></span><br>
                            <span class="text-secondary small" style="font-size: 11px;"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($sup['phone'] ?? 'N/A') ?> | <i class="fa-solid fa-envelope me-1"></i> <?= htmlspecialchars($sup['email'] ?? 'N/A') ?></span>
                        </td>
                        <td class="text-end text-info fw-semibold">฿<?= number_format($sup['total_ordered'], 2) ?></td>
                        <td class="text-end text-success fw-semibold">฿<?= number_format($sup['total_paid'], 2) ?></td>
                        <td class="text-end text-danger fw-bold">฿<?= number_format($sup['outstanding_balance'], 2) ?></td>
                        <td class="no-print">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-outline-warning btn-xs rounded-pill px-2 ledger-btn" data-id="<?= $sup['id'] ?>">
                                    <i class="fa-solid fa-book"></i> สมุดบัญชี
                                </button>
                                <button class="btn btn-outline-info btn-xs rounded-pill px-2 edit-btn" 
                                        data-id="<?= $sup['id'] ?>" 
                                        data-name="<?= htmlspecialchars($sup['name']) ?>" 
                                        data-contact="<?= htmlspecialchars($sup['contact_name'] ?? '') ?>"
                                        data-phone="<?= htmlspecialchars($sup['phone'] ?? '') ?>"
                                        data-email="<?= htmlspecialchars($sup['email'] ?? '') ?>"
                                        data-address="<?= htmlspecialchars($sup['address'] ?? '') ?>">
                                    <i class="fa-solid fa-pencil"></i> แก้ไข
                                </button>
                                <button class="btn btn-outline-danger btn-xs rounded-pill px-2 delete-btn" data-id="<?= $sup['id'] ?>">
                                    <i class="fa-solid fa-trash"></i> ลบ
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
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-plus text-primary me-2"></i> เพิ่มรายชื่อผู้จัดจำหน่ายใหม่</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="createName" class="form-label small fw-semibold text-secondary">ชื่อผู้จัดจำหน่าย *</label>
                    <input type="text" class="form-control" id="createName" name="name" required placeholder="เช่น บริษัท กระเป๋าสินค้าพรีเมียม จำกัด...">
                </div>
                <div class="mb-3">
                    <label for="createContact" class="form-label small fw-semibold text-secondary">ชื่อผู้ติดต่อ / ตัวแทนผู้แทนขาย</label>
                    <input type="text" class="form-control" id="createContact" name="contact_name" placeholder="เช่น คุณณรงค์ชัย...">
                </div>
                <div class="row mb-3 g-3">
                    <div class="col-6">
                        <label for="createPhone" class="form-label small fw-semibold text-secondary">เบอร์โทรศัพท์ติดต่อ</label>
                        <input type="text" class="form-control" id="createPhone" name="phone" placeholder="เช่น 0891234567...">
                    </div>
                    <div class="col-6">
                        <label for="createEmail" class="form-label small fw-semibold text-secondary">อีเมลแอดเดรส</label>
                        <input type="email" class="form-control" id="createEmail" name="email" placeholder="เช่น supplier@domain.com...">
                    </div>
                </div>
                <div class="mb-0">
                    <label for="createAddress" class="form-label small fw-semibold text-secondary">ที่อยู่ / พิกัดคลังส่งสินค้า</label>
                    <textarea class="form-control" id="createAddress" name="address" rows="3" placeholder="ระบุข้อมูลที่อยู่สำหรับส่งสินค้าอย่างละเอียด..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกผู้จัดจำหน่าย</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border border-secondary shadow" id="editForm">
            <input type="hidden" id="editId" name="id">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pencil text-info me-2"></i> แก้ไขข้อมูลผู้จัดจำหน่าย</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editName" class="form-label small fw-semibold text-secondary">ชื่อผู้จัดจำหน่าย *</label>
                    <input type="text" class="form-control" id="editName" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="editContact" class="form-label small fw-semibold text-secondary">ชื่อผู้ติดต่อ / ตัวแทนผู้แทนขาย</label>
                    <input type="text" class="form-control" id="editContact" name="contact_name">
                </div>
                <div class="row mb-3 g-3">
                    <div class="col-6">
                        <label for="editPhone" class="form-label small fw-semibold text-secondary">เบอร์โทรศัพท์ติดต่อ</label>
                        <input type="text" class="form-control" id="editPhone" name="phone">
                    </div>
                    <div class="col-6">
                        <label for="editEmail" class="form-label small fw-semibold text-secondary">อีเมลแอดเดรส</label>
                        <input type="email" class="form-control" id="editEmail" name="email">
                    </div>
                </div>
                <div class="mb-0">
                    <label for="editAddress" class="form-label small fw-semibold text-secondary">ที่อยู่ / พิกัดคลังส่งสินค้า</label>
                    <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4">อัปเดตข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<!-- Ledger Modal -->
<div class="modal fade" id="ledgerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border border-secondary shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-book text-warning me-2"></i> สมุดบัญชีคู่ค้า: <span id="ledgerSupName"></span></h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Navigation Tabs -->
                <ul class="nav nav-pills mb-3 border-bottom border-secondary pb-2" id="ledgerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-dark active btn-sm rounded-pill px-3 me-2" id="purchases-tab" data-bs-toggle="pill" data-bs-target="#tab-purchases" type="button" role="tab"><i class="fa-solid fa-receipt me-1"></i> ประวัติสั่งซื้อสินค้า (PO)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn btn-dark btn-sm rounded-pill px-3" id="payments-tab" data-bs-toggle="pill" data-bs-target="#tab-payments" type="button" role="tab"><i class="fa-solid fa-wallet me-1"></i> ประวัติจ่ายเงิน</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="ledgerTabsContent">
                    <!-- Purchases List -->
                    <div class="tab-pane fade show active" id="tab-purchases" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle text-light w-100" style="font-size: 13px;" width="100%">
                                <thead>
                                    <tr class="text-secondary small border-secondary">
                                        <th>เลขใบสั่งซื้อ</th>
                                        <th>วันที่สั่งซื้อ</th>
                                        <th>สถานะสต็อก</th>
                                        <th class="text-end">ยอดชำระสุทธิ</th>
                                        <th class="text-end">จ่ายเงินแล้ว</th>
                                        <th class="text-end text-danger">คงเหลือยอดหนี้</th>
                                    </tr>
                                </thead>
                                <tbody id="ledgerPurchasesBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Payments History -->
                    <div class="tab-pane fade" id="tab-payments" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle text-light w-100" style="font-size: 13px;" width="100%">
                                <thead>
                                    <tr class="text-secondary small border-secondary">
                                        <th>เลขที่อ้างอิงธุรกรรม</th>
                                        <th>ใบสั่งซื้ออ้างอิง</th>
                                        <th>วันที่ชำระเงิน</th>
                                        <th>ช่องทางจ่ายเงิน</th>
                                        <th class="text-end">จำนวนจ่ายจริง</th>
                                    </tr>
                                </thead>
                                <tbody id="ledgerPaymentsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">ปิดหน้าจอ</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#suppliersTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "ค้นหาข้อมูลคู่ค้า...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "ไม่พบข้อมูลรายชื่อคู่ค้า",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    // Create AJAX
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            name: document.getElementById('createName').value,
            contact_name: document.getElementById('createContact').value,
            phone: document.getElementById('createPhone').value,
            email: document.getElementById('createEmail').value,
            address: document.getElementById('createAddress').value
        };

        fetch('/suppliers/create', {
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
                text: 'เพิ่มรายชื่อคู่ค้า/ผู้จัดจำหน่ายใหม่เรียบร้อยแล้ว.',
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

    // Populate Edit Modal
    $('.edit-btn').on('click', function() {
        $('#editId').val($(this).data('id'));
        $('#editName').val($(this).data('name'));
        $('#editContact').val($(this).data('contact'));
        $('#editPhone').val($(this).data('phone'));
        $('#editEmail').val($(this).data('email'));
        $('#editAddress').val($(this).data('address'));

        $('#editModal').modal('show');
    });

    // Edit AJAX
    const editForm = document.getElementById('editForm');
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const data = {
            name: document.getElementById('editName').value,
            contact_name: document.getElementById('editContact').value,
            phone: document.getElementById('editPhone').value,
            email: document.getElementById('editEmail').value,
            address: document.getElementById('editAddress').value
        };

        fetch(`/suppliers/update/${id}`, {
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
                title: 'บันทึกข้อมูลแล้ว!',
                text: 'ปรับปรุงรายละเอียดข้อมูลผู้จัดจำหน่ายสำเร็จ.',
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

    // Fetch Supplier Ledger AJAX
    $('.ledger-btn').on('click', function() {
        const id = $(this).data('id');
        
        fetch(`/suppliers/${id}/history`)
            .then(res => res.json())
            .then(data => {
                $('#ledgerSupName').text(data.supplier.name);
                
                // Populate Purchases
                let purHtml = '';
                if (data.purchases.length === 0) {
                    purHtml = '<tr><td colspan="6" class="text-center text-secondary py-4">ไม่พบรายการใบสั่งซื้อสินค้าสำหรับผู้จัดจำหน่ายรายนี้</td></tr>';
                } else {
                    data.purchases.forEach(p => {
                        let badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary';
                        let statusText = p.status;
                        if (p.status === 'Received') {
                            badge = 'bg-success bg-opacity-10 text-success border border-success';
                            statusText = 'รับของแล้ว';
                        }
                        if (p.status === 'Ordered') {
                            badge = 'bg-info bg-opacity-10 text-info border border-info';
                            statusText = 'สั่งซื้อแล้ว';
                        }
                        if (p.status === 'Cancelled') {
                            badge = 'bg-danger bg-opacity-10 text-danger border border-danger';
                            statusText = 'ยกเลิกแล้ว';
                        }

                        purHtml += `
                            <tr class="border-secondary text-light">
                                <td><a href="/purchases/view/${p.id}" class="text-decoration-none text-info"><code>${p.purchase_order_no}</code></a></td>
                                <td class="text-dark fw-medium">${p.order_date}</td>
                                <td><span class="badge ${badge} rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">${statusText}</span></td>
                                <td class="text-end text-dark">฿${parseFloat(p.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="text-end text-success fw-medium">฿${parseFloat(p.paid_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="text-end text-danger fw-bold">฿${parseFloat(p.balance_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `;
                    });
                }
                $('#ledgerPurchasesBody').html(purHtml);

                // Populate Payments
                let payHtml = '';
                if (data.payments.length === 0) {
                    payHtml = '<tr><td colspan="5" class="text-center text-secondary py-4">ไม่พบรายการประวัติการจ่ายเงินสำหรับผู้จัดจำหน่ายรายนี้</td></tr>';
                } else {
                    data.payments.forEach(pay => {
                        let payMethodText = pay.payment_method;
                        if (pay.payment_method === 'Cash') payMethodText = 'เงินสด';
                        if (pay.payment_method === 'Bank Transfer') payMethodText = 'โอนผ่านธนาคาร';
                        if (pay.payment_method === 'Cheque') payMethodText = 'เช็คธนาคาร';

                        payHtml += `
                            <tr class="border-secondary text-light">
                                <td><code>${pay.reference_no || 'N/A'}</code></td>
                                <td><code>${pay.purchase_order_no}</code></td>
                                <td class="text-dark fw-medium">${pay.payment_date}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">${payMethodText}</span></td>
                                <td class="text-end text-success fw-bold">฿${parseFloat(pay.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                            </tr>
                        `;
                    });
                }
                $('#ledgerPaymentsBody').html(payHtml);

                $('#ledgerModal').modal('show');
            });
    });

    // Delete Supplier
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'ลบข้อมูลผู้จัดจำหน่าย?',
            text: "ข้อมูลรายชื่อผู้จัดจำหน่ายนี้จะถูกลบออกจากระบบอย่างถาวร!",
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            color: '#1e293b',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'ยืนยัน, ลบข้อมูล!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/suppliers/delete/${id}`, {
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
                        text: 'ลบข้อมูลผู้จัดจำหน่ายเรียบร้อยแล้ว.',
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
            }
        });
    });
});
</script>
