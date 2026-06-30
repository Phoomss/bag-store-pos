<?php $title = 'Staff Management'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-user-shield text-primary me-2"></i> Staff Accounts Management</h4>
    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-user-plus me-1"></i> Register Staff
    </button>
</div>

<!-- Table Panel -->
<div class="glass-panel">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="usersTable">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered At</th>
                    <th style="width: 150px;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-secondary text-light">
                        <td><code>#<?= $user['id'] ?></code></td>
                        <td class="fw-bold"><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php
                            $badge = 'bg-secondary';
                            if ($user['role_name'] === 'Owner') $badge = 'bg-danger';
                            if ($user['role_name'] === 'Admin') $badge = 'bg-primary';
                            if ($user['role_name'] === 'Cashier') $badge = 'bg-success';
                            if ($user['role_name'] === 'Warehouse') $badge = 'bg-warning text-dark';
                            ?>
                            <span class="badge <?= $badge ?>"><?= $user['role_name'] ?></span>
                        </td>
                        <td>
                            <?php if ($user['status'] === 'Active'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-outline-info btn-sm rounded-pill px-3 edit-btn" 
                                        data-id="<?= $user['id'] ?>" 
                                        data-name="<?= htmlspecialchars($user['name']) ?>" 
                                        data-email="<?= htmlspecialchars($user['email']) ?>"
                                        data-role="<?= $user['role_id'] ?>"
                                        data-status="<?= $user['status'] ?>"
                                        <?= ($user['id'] === 1) ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-pencil me-1"></i> Edit
                                </button>
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3 delete-btn" data-id="<?= $user['id'] ?>" <?= ($user['id'] === 1) ? 'disabled' : '' ?>>
                                    <i class="fa-solid fa-trash me-1"></i> Delete
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
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Register Staff Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="createName" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="createName" required placeholder="e.g. Somchai Dee">
                </div>
                <div class="mb-3">
                    <label for="createEmail" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="createEmail" required placeholder="somchai@company.com">
                </div>
                <div class="mb-3">
                    <label for="createPass" class="form-label">Password *</label>
                    <input type="password" class="form-control" id="createPass" required placeholder="Enter password (min 6 chars)...">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="createRoleId" class="form-label">System Role *</label>
                        <select class="form-select" id="createRoleId" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="createStatus" class="form-label">Account Status</label>
                        <select class="form-select" id="createStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Register User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border border-secondary shadow" id="editForm">
            <input type="hidden" id="editId">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pencil text-info me-2"></i> Edit Staff Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editName" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="editName" required>
                </div>
                <div class="mb-3">
                    <label for="editEmail" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="editEmail" required>
                </div>
                <div class="mb-3">
                    <label for="editPass" class="form-label">Reset Password (Leave blank to keep current)</label>
                    <input type="password" class="form-control" id="editPass" placeholder="Enter new password...">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="editRoleId" class="form-label">System Role *</label>
                        <select class="form-select" id="editRoleId" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="editStatus" class="form-label">Account Status</label>
                        <select class="form-select" id="editStatus">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search staff..."
        }
    });

    // Create AJAX
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
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
            Swal.fire({
                icon: 'success',
                title: 'Created!',
                text: 'User created successfully.',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message, background: '#1e293b' });
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
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'User updated successfully.',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message, background: '#1e293b' });
        });
    });

    // Delete AJAX
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Delete Staff User?',
            text: "This will suspend/remove their login credentials permanently!",
            icon: 'warning',
            showCancelButton: true,
            background: '#1e293b',
            color: '#f8fafc',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
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
                        title: 'Deleted!',
                        text: 'User deleted successfully.',
                        background: '#1e293b',
                        color: '#f8fafc',
                        confirmButtonColor: '#3b82f6'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message, background: '#1e293b' });
                });
            }
        });
    });
});
</script>
