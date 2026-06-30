<?php $title = 'จัดการแบรนด์สินค้า'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-copyright text-primary me-2"></i> แบรนด์สินค้าในระบบ</h4>
    <button class="btn btn-primary btn-sm rounded-pill px-3 no-print" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-plus me-1"></i> เพิ่มแบรนด์สินค้าใหม่
    </button>
</div>

<!-- Table Panel -->
<div class="glass-panel border border-secondary shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="brandsTable" width="100%">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th style="width: 100px;">รหัสแบรนด์</th>
                    <th>ชื่อแบรนด์สินค้า</th>
                    <th>รายละเอียด / คำอธิบาย</th>
                    <th style="width: 180px;" class="text-center no-print">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $brand): ?>
                    <tr class="border-secondary text-light">
                        <td><code>#<?= $brand['id'] ?></code></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($brand['name']) ?></td>
                        <td class="text-secondary small"><?= htmlspecialchars($brand['description'] ?? 'N/A') ?></td>
                        <td class="no-print">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-outline-info btn-xs rounded-pill px-2 edit-btn" 
                                        data-id="<?= $brand['id'] ?>" 
                                        data-name="<?= htmlspecialchars($brand['name']) ?>" 
                                        data-desc="<?= htmlspecialchars($brand['description'] ?? '') ?>">
                                    <i class="fa-solid fa-pencil"></i> แก้ไข
                                </button>
                                <button class="btn btn-outline-danger btn-xs rounded-pill px-2 delete-btn" data-id="<?= $brand['id'] ?>">
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
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-plus text-primary me-2"></i> เพิ่มแบรนด์สินค้าใหม่</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="createName" class="form-label small fw-semibold text-secondary">ชื่อแบรนด์สินค้า *</label>
                    <input type="text" class="form-control fw-bold" id="createName" name="name" required placeholder="เช่น Herschel, Samsonite, Anello...">
                </div>
                <div class="mb-0">
                    <label for="createDesc" class="form-label small fw-semibold text-secondary">รายละเอียดเพิ่มเติม (ถ้ามี)</label>
                    <textarea class="form-control" id="createDesc" name="description" rows="3" placeholder="ระบุรายละเอียด หรือจุดขายของแบรนด์นี้..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">บันทึกแบรนด์สินค้า</button>
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
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pencil text-info me-2"></i> แก้ไขข้อมูลแบรนด์สินค้า</h5>
                <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editName" class="form-label small fw-semibold text-secondary">ชื่อแบรนด์สินค้า *</label>
                    <input type="text" class="form-control fw-bold" id="editName" name="name" required>
                </div>
                <div class="mb-0">
                    <label for="editDesc" class="form-label small fw-semibold text-secondary">รายละเอียดเพิ่มเติม (ถ้ามี)</label>
                    <textarea class="form-control" id="editDesc" name="description" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4">อัปเดตแบรนด์สินค้า</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = $('#brandsTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "ค้นหาแบรนด์...",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "ไม่พบข้อมูลแบรนด์สินค้าในระบบ",
            zeroRecords: "ไม่พบข้อมูลที่ตรงกัน",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            }
        }
    });

    // Handle Create Brand AJAX
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            name: document.getElementById('createName').value,
            description: document.getElementById('createDesc').value
        };

        fetch('/brands/create', {
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
                text: 'เพิ่มแบรนด์สินค้าใหม่เรียบร้อยแล้ว.',
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
        const id = $(this).data('id');
        const name = $(this).data('name');
        const desc = $(this).data('desc');

        $('#editId').val(id);
        $('#editName').val(name);
        $('#editDesc').val(desc);

        $('#editModal').modal('show');
    });

    // Handle Edit Brand AJAX
    const editForm = document.getElementById('editForm');
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const data = {
            name: document.getElementById('editName').value,
            description: document.getElementById('editDesc').value
        };

        fetch(`/brands/update/${id}`, {
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
                title: 'บันทึกข้อมูลสำเร็จ!',
                text: 'อัปเดตรายละเอียดข้อมูลแบรนด์สินค้าเรียบร้อย.',
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

    // Handle Delete Brand AJAX
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'ลบแบรนด์สินค้า?',
            text: "ข้อมูลแบรนด์สินค้าชิ้นนี้จะถูกลบออกจากระบบอย่างถาวร!",
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
                fetch(`/brands/delete/${id}`, {
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
                        text: 'ลบแบรนด์สินค้าออกจากระบบเรียบร้อยแล้ว.',
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
