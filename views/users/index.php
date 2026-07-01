<?php $title = 'จัดการพนักงาน'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 animate-fade-in">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-user-shield text-primary me-2"></i> จัดการบัญชีผู้ใช้งานพนักงาน</h4>
        <span class="text-secondary small">เพิ่ม แก้ไขสิทธิ์ และลบบัญชีผู้ใช้งานระบบเพื่อกำหนดบทบาทการควบคุมร้านค้า</span>
    </div>
    <button class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm hover-scale" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-user-plus me-1"></i> ลงทะเบียนพนักงานใหม่
    </button>
</div>

<!-- Table Panel -->
<div class="glass-panel animate-fade-in" style="animation-delay: 0.1s;">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="usersTable">
            <thead>
                <tr class="text-secondary small border-light-subtle">
                    <th>รหัสพนักงาน</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมลที่ใช้ล็อกอิน</th>
                    <th>ระดับสิทธิ์ / บทบาท</th>
                    <th>สถานะบัญชี</th>
                    <th>วันที่ลงทะเบียน</th>
                    <th style="width: 160px;" class="text-center">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-light-subtle">
                        <td><code>#<?= $user['id'] ?></code></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="text-secondary"><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php
                            $roleName = $user['role_name'];
                            $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10';
                            if ($user['role_name'] === 'Owner') { $roleName = 'เจ้าของร้าน (Owner)'; $badge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10'; }
                            if ($user['role_name'] === 'Admin') { $roleName = 'ผู้ดูแลระบบ (Admin)'; $badge = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10'; }
                            if ($user['role_name'] === 'Cashier') { $roleName = 'แคชเชียร์ (Cashier)'; $badge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-10'; }
                            if ($user['role_name'] === 'Warehouse') { $roleName = 'คลังสินค้า (Warehouse)'; $badge = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10'; }
                            ?>
                            <span class="badge <?= $badge ?> rounded-pill px-2.5 py-1 small"><?= $roleName ?></span>
                        </td>
                        <td>
                            <?php if ($user['status'] === 'Active'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-2.5 py-1 small">เปิดใช้งาน (Active)</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill px-2.5 py-1 small">ปิดการใช้งาน (Inactive)</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary small"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary edit-btn" 
                                        data-id="<?= $user['id'] ?>" 
                                        data-name="<?= htmlspecialchars($user['name']) ?>" 
                                        data-email="<?= htmlspecialchars($user['email']) ?>"
                                        data-role="<?= $user['role_id'] ?>"
                                        data-status="<?= $user['status'] ?>"
                                        <?= ($user['id'] === 1) ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-pencil me-1"></i> แก้ไข
                                </button>
                                <button class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-danger delete-btn" data-id="<?= $user['id'] ?>" <?= ($user['id'] === 1) ? 'disabled' : '' ?>>
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
        <form class="modal-content border-0 shadow-lg rounded-4" id="createForm">
            <div class="modal-header border-light-subtle">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i> ลงทะเบียนบัญชีพนักงานใหม่</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="createName" class="form-label small fw-semibold text-secondary">ชื่อ-นามสกุล *</label>
                    <input type="text" class="form-control rounded-3" id="createName" required placeholder="เช่น นายสมจิต คิดไว">
                </div>
                <div class="mb-3">
                    <label for="createEmail" class="form-label small fw-semibold text-secondary">อีเมลสำหรับล็อกอิน *</label>
                    <input type="email" class="form-control rounded-3" id="createEmail" required placeholder="somchit@company.com">
                </div>
                <div class="mb-3">
                    <label for="createPass" class="form-label small fw-semibold text-secondary">รหัสผ่านบัญชี *</label>
                    <input type="password" class="form-control rounded-3" id="createPass" required placeholder="ระบุรหัสผ่าน (ขั้นต่ำ 6 ตัวอักษร)...">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="createRoleId" class="form-label small fw-semibold text-secondary">บทบาทสิทธิ์การใช้งาน *</label>
                        <select class="form-select rounded-3" id="createRoleId" required>
                            <?php foreach ($roles as $role): 
                                $thaiRole = $role['name'];
                                if ($role['name'] === 'Owner') $thaiRole = 'เจ้าของร้าน (Owner)';
                                if ($role['name'] === 'Admin') $thaiRole = 'ผู้ดูแลระบบ (Admin)';
                                if ($role['name'] === 'Cashier') $thaiRole = 'แคชเชียร์ (Cashier)';
                                if ($role['name'] === 'Warehouse') $thaiRole = 'พนักงานคลังสินค้า (Warehouse)';
                            ?>
                                <option value="<?= $role['id'] ?>"><?= $thaiRole ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="createStatus" class="form-label small fw-semibold text-secondary">สถานะเริ่มต้น</label>
                        <select class="form-select rounded-3" id="createStatus">
                            <option value="Active">เปิดใช้งาน (Active)</option>
                            <option value="Inactive">ปิดใช้งาน (Inactive)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-light-subtle">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">ลงทะเบียนพนักงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-4" id="editForm">
            <input type="hidden" id="editId">
            <div class="modal-header border-light-subtle">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pencil text-info me-2"></i> แก้ไขข้อมูลบัญชีพนักงาน</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="editName" class="form-label small fw-semibold text-secondary">ชื่อ-นามสกุล *</label>
                    <input type="text" class="form-control rounded-3" id="editName" required>
                </div>
                <div class="mb-3">
                    <label for="editEmail" class="form-label small fw-semibold text-secondary">อีเมลสำหรับล็อกอิน *</label>
                    <input type="email" class="form-control rounded-3" id="editEmail" required>
                </div>
                <div class="mb-3">
                    <label for="editPass" class="form-label small fw-semibold text-secondary">เปลี่ยนรหัสผ่านใหม่ (เว้นว่างไว้หากไม่เปลี่ยน)</label>
                    <input type="password" class="form-control rounded-3" id="editPass" placeholder="ระบุรหัสผ่านใหม่ที่ต้องการ...">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="editRoleId" class="form-label small fw-semibold text-secondary">บทบาทสิทธิ์การใช้งาน *</label>
                        <select class="form-select rounded-3" id="editRoleId" required>
                            <?php foreach ($roles as $role): 
                                $thaiRole = $role['name'];
                                if ($role['name'] === 'Owner') $thaiRole = 'เจ้าของร้าน (Owner)';
                                if ($role['name'] === 'Admin') $thaiRole = 'ผู้ดูแลระบบ (Admin)';
                                if ($role['name'] === 'Cashier') $thaiRole = 'แคชเชียร์ (Cashier)';
                                if ($role['name'] === 'Warehouse') $thaiRole = 'พนักงานคลังสินค้า (Warehouse)';
                            ?>
                                <option value="<?= $role['id'] ?>"><?= $thaiRole ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="editStatus" class="form-label small fw-semibold text-secondary">สถานะบัญชี</label>
                        <select class="form-select rounded-3" id="editStatus">
                            <option value="Active">เปิดใช้งาน (Active)</option>
                            <option value="Inactive">ปิดใช้งาน (Inactive)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-light-subtle">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4">บันทึกการแก้ไข</button>
            </div>
        </form>
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
    $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']],
        language: {
            emptyTable: "ไม่พบข้อมูลบัญชีพนักงานในตาราง",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            lengthMenu: "แสดง _MENU_ รายการ",
            loadingRecords: "กำลังโหลด...",
            processing: "กำลังประมวลผล...",
            zeroRecords: "ไม่พบข้อมูลพนักงานที่ตรงกับการค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            },
            search: "ค้นหาด่วน:",
            searchPlaceholder: "ชื่อพนักงาน, อีเมล..."
        }
    });

    // Create AJAX
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        Swal.fire({
            title: 'กำลังบันทึกข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = {
            name: document.getElementById('createName').value,
            email: document.getElementById('createEmail').value,
            password: document.getElementById('createPass').value,
            role_id: parseInt(document.getElementById('createRoleId').value),
            status: document.getElementById('createStatus').value
        };

        fetch('/users/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            $('#createModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'ลงทะเบียนสำเร็จ!',
                text: 'บัญชีพนักงานใหม่ถูกสร้างในระบบแล้ว.',
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
    });

    // Populate Edit Modal
    $('.edit-btn').on('click', function() {
        $('#editId').val($(this).data('id'));
        $('#editName').val($(this).data('name'));
        $('#editEmail').val($(this).data('email'));
        $('#editRoleId').val($(this).data('role'));
        $('#editStatus').val($(this).data('status'));
        $('#editPass').val(''); // clear reset box

        $('#editModal').modal('show');
    });

    // Edit AJAX
    const editForm = document.getElementById('editForm');
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        
        // Show loading state
        Swal.fire({
            title: 'กำลังอัปเดตข้อมูล...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = {
            name: document.getElementById('editName').value,
            email: document.getElementById('editEmail').value,
            password: document.getElementById('editPass').value,
            role_id: parseInt(document.getElementById('editRoleId').value),
            status: document.getElementById('editStatus').value
        };

        fetch(`/users/update/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            $('#editModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'อัปเดตสำเร็จ!',
                text: 'แก้ไขข้อมูลบัญชีผู้ใช้งานของพนักงานเรียบร้อยแล้ว.',
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
    });

    // Delete AJAX
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'ยืนยันการลบบัญชีพนักงาน?',
            text: "บัญชีและรหัสเข้าใช้งานของพนักงานท่านนี้จะถูกระงับและลบออกจากระบบอย่างถาวรทันที!",
            icon: 'warning',
            showCancelButton: true,
            background: '#ffffff',
            color: '#0f172a',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ยืนยัน ลบข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'กำลังลบข้อมูลพนักงาน...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/users/delete/${id}`, {
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
                        text: 'ลบข้อมูลบัญชีพนักงานออกจากระบบแล้ว.',
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
