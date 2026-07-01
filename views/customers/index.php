<?php $title = 'ข้อมูลลูกค้า CRM'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 animate-fade-in">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-users text-primary me-2"></i> ข้อมูลลูกค้าสมาชิก (CRM)</h4>
        <span class="text-secondary small">จัดการประวัติสมาชิกลูกค้า ระบบสะสมแต้ม และระดับสมาชิกสำหรับการส่งเสริมการขาย</span>
    </div>
    <button class="btn btn-primary fw-bold px-4 py-2 rounded-pill shadow-sm hover-scale" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-user-plus me-1"></i> ลงทะเบียนลูกค้าใหม่
    </button>
</div>

<!-- CRM Table -->
<div class="glass-panel animate-fade-in" style="animation-delay: 0.1s;">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="customersTable">
            <thead>
                <tr class="text-secondary small border-light-subtle">
                    <th>รหัสลูกค้า</th>
                    <th>ชื่อ-นามสกุล / ข้อมูลเพิ่ม</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>อีเมล</th>
                    <th class="text-center">ระดับสมาชิก</th>
                    <th class="text-end">คะแนนสะสม</th>
                    <th>ที่อยู่</th>
                    <th style="width: 160px;" class="text-center">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $cust): ?>
                    <tr class="border-light-subtle">
                        <td><code><?= htmlspecialchars($cust['customer_code']) ?></code></td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($cust['name']) ?></div>
                            <span class="text-secondary small" style="font-size: 11px;">
                                <?php
                                $gender = $cust['gender'];
                                if ($gender === 'Male') $gender = 'ชาย';
                                elseif ($gender === 'Female') $gender = 'หญิง';
                                elseif ($gender === 'Other') $gender = 'อื่นๆ';
                                else $gender = 'ไม่ระบุ';
                                ?>
                                เพศ: <?= $gender ?> <?= ($cust['birthday']) ? ' | วันเกิด: ' . date('d M', strtotime($cust['birthday'])) : '' ?>
                            </span>
                        </td>
                        <td class="text-dark fw-semibold"><?= htmlspecialchars($cust['phone']) ?></td>
                        <td class="text-secondary small"><?= htmlspecialchars($cust['email'] ?? 'N/A') ?></td>
                        <td class="text-center">
                            <?php
                            $badge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10';
                            if ($cust['membership_level'] === 'Silver') $badge = 'bg-info bg-opacity-10 text-info border border-info border-opacity-10';
                            if ($cust['membership_level'] === 'Gold') $badge = 'bg-warning bg-opacity-10 text-warning text-dark-override border border-warning border-opacity-10';
                            if ($cust['membership_level'] === 'Platinum') $badge = 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10';
                            ?>
                            <span class="badge <?= $badge ?> rounded-pill px-2.5 py-1 small"><?= $cust['membership_level'] ?></span>
                        </td>
                        <td class="text-end fw-bold text-success"><?= number_format($cust['reward_points']) ?> คะแนน</td>
                        <td class="text-secondary small text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($cust['address'] ?? '') ?>"><?= htmlspecialchars($cust['address'] ?? 'N/A') ?></td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary edit-btn" 
                                        data-id="<?= $cust['id'] ?>" 
                                        data-name="<?= htmlspecialchars($cust['name']) ?>" 
                                        data-phone="<?= htmlspecialchars($cust['phone']) ?>"
                                        data-email="<?= htmlspecialchars($cust['email'] ?? '') ?>"
                                        data-birthday="<?= htmlspecialchars($cust['birthday'] ?? '') ?>"
                                        data-gender="<?= htmlspecialchars($cust['gender'] ?? '') ?>"
                                        data-address="<?= htmlspecialchars($cust['address'] ?? '') ?>"
                                        data-points="<?= $cust['reward_points'] ?>"
                                        data-level="<?= $cust['membership_level'] ?>"
                                        <?= ($cust['id'] == 1) ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-pencil me-1"></i> แก้ไข
                                </button>
                                <button class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-danger delete-btn" data-id="<?= $cust['id'] ?>" <?= ($cust['id'] == 1) ? 'disabled' : '' ?>>
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
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i> ลงทะเบียนลูกค้าใหม่</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-8">
                        <label for="createName" class="form-label small fw-semibold text-secondary">ชื่อ-นามสกุล *</label>
                        <input type="text" class="form-control rounded-3" id="createName" name="name" required placeholder="เช่น นายสมชาย ดีใจ">
                    </div>
                    <div class="col-4">
                        <label for="createGender" class="form-label small fw-semibold text-secondary">เพศ</label>
                        <select class="form-select rounded-3" id="createGender" name="gender">
                            <option value="">เลือกเพศ</option>
                            <option value="Male">ชาย</option>
                            <option value="Female">หญิง</option>
                            <option value="Other">อื่นๆ</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="createPhone" class="form-label small fw-semibold text-secondary">เบอร์โทรศัพท์ *</label>
                        <input type="text" class="form-control rounded-3" id="createPhone" name="phone" required placeholder="เช่น 0891234567">
                    </div>
                    <div class="col-6">
                        <label for="createBirthday" class="form-label small fw-semibold text-secondary">วัน/เดือน/ปี เกิด</label>
                        <input type="date" class="form-control rounded-3" id="createBirthday" name="birthday">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="createEmail" class="form-label small fw-semibold text-secondary">อีเมล</label>
                    <input type="email" class="form-control rounded-3" id="createEmail" name="email" placeholder="เช่น somchai@example.com">
                </div>
                <div class="mb-3">
                    <label for="createAddress" class="form-label small fw-semibold text-secondary">ที่อยู่จัดส่ง / ติดต่อ</label>
                    <textarea class="form-control rounded-3" id="createAddress" name="address" rows="3" placeholder="บ้านเลขที่ ถนน แขวง เขต จังหวัด..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-light-subtle">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">ลงทะเบียนลูกค้า</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg rounded-4" id="editForm">
            <input type="hidden" id="editId" name="id">
            <div class="modal-header border-light-subtle">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pencil text-info me-2"></i> แก้ไขข้อมูลลูกค้าสมาชิก</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-3">
                    <div class="col-8">
                        <label for="editName" class="form-label small fw-semibold text-secondary">ชื่อ-นามสกุล *</label>
                        <input type="text" class="form-control rounded-3" id="editName" name="name" required>
                    </div>
                    <div class="col-4">
                        <label for="editGender" class="form-label small fw-semibold text-secondary">เพศ</label>
                        <select class="form-select rounded-3" id="editGender" name="gender">
                            <option value="">เลือกเพศ</option>
                            <option value="Male">ชาย</option>
                            <option value="Female">หญิง</option>
                            <option value="Other">อื่นๆ</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="editPhone" class="form-label small fw-semibold text-secondary">เบอร์โทรศัพท์ *</label>
                        <input type="text" class="form-control rounded-3" id="editPhone" name="phone" required>
                    </div>
                    <div class="col-6">
                        <label for="editBirthday" class="form-label small fw-semibold text-secondary">วัน/เดือน/ปี เกิด</label>
                        <input type="date" class="form-control rounded-3" id="editBirthday" name="birthday">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="editEmail" class="form-label small fw-semibold text-secondary">อีเมล</label>
                    <input type="email" class="form-control rounded-3" id="editEmail" name="email">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="editPoints" class="form-label small fw-semibold text-secondary">คะแนนสะสม</label>
                        <input type="number" class="form-control rounded-3" id="editPoints" name="reward_points" min="0" required>
                    </div>
                    <div class="col-6">
                        <label for="editLevel" class="form-label small fw-semibold text-secondary">ระดับสมาชิก</label>
                        <select class="form-select rounded-3" id="editLevel" name="membership_level">
                            <option value="Bronze">Bronze</option>
                            <option value="Silver">Silver</option>
                            <option value="Gold">Gold</option>
                            <option value="Platinum">Platinum</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="editAddress" class="form-label small fw-semibold text-secondary">ที่อยู่จัดส่ง / ติดต่อ</label>
                    <textarea class="form-control rounded-3" id="editAddress" name="address" rows="3"></textarea>
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
    $('#customersTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']],
        language: {
            emptyTable: "ไม่พบข้อมูลลูกค้าในตาราง",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            lengthMenu: "แสดง _MENU_ รายการ",
            loadingRecords: "กำลังโหลด...",
            processing: "กำลังประมวลผล...",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกับการค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            },
            search: "ค้นหาด่วน:",
            searchPlaceholder: "ชื่อ, เบอร์โทร..."
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
            gender: document.getElementById('createGender').value,
            phone: document.getElementById('createPhone').value,
            birthday: document.getElementById('createBirthday').value,
            email: document.getElementById('createEmail').value,
            address: document.getElementById('createAddress').value
        };

        fetch('/customers/create', {
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
                text: 'สร้างข้อมูลลูกค้าสมาชิกในระบบเรียบร้อยแล้ว.',
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
        $('#editGender').val($(this).data('gender'));
        $('#editPhone').val($(this).data('phone'));
        $('#editBirthday').val($(this).data('birthday'));
        $('#editEmail').val($(this).data('email'));
        $('#editPoints').val($(this).data('points'));
        $('#editLevel').val($(this).data('level'));
        $('#editAddress').val($(this).data('address'));

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
            gender: document.getElementById('editGender').value,
            phone: document.getElementById('editPhone').value,
            birthday: document.getElementById('editBirthday').value,
            email: document.getElementById('editEmail').value,
            reward_points: parseInt(document.getElementById('editPoints').value),
            membership_level: document.getElementById('editLevel').value,
            address: document.getElementById('editAddress').value
        };

        fetch(`/customers/update/${id}`, {
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
                text: 'แก้ไขข้อมูลโปรไฟล์ของลูกค้าสมาชิกเรียบร้อยแล้ว.',
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
            title: 'ยืนยันการลบลูกค้า?',
            text: "ข้อมูลประวัติการสะสมแต้มและโปรไฟล์ลูกค้าจะถูกลบออกจากฐานข้อมูลอย่างถาวรและไม่สามารถกู้คืนได้!",
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
                    title: 'กำลังลบข้อมูลลูกค้า...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/customers/delete/${id}`, {
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
                        text: 'ลบข้อมูลประวัติของลูกค้าออกจากระบบแล้ว.',
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
