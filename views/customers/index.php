<?php $title = 'Customers CRM'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-users text-primary me-2"></i> Customer Loyalty CRM</h4>
    <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="fa-solid fa-plus me-1"></i> Add Customer
    </button>
</div>

<!-- CRM Table -->
<div class="glass-panel">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="customersTable">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Loyalty Tier</th>
                    <th class="text-center">Reward Points</th>
                    <th>Address</th>
                    <th style="width: 150px;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $cust): ?>
                    <tr class="border-secondary text-light">
                        <td><code><?= htmlspecialchars($cust['customer_code']) ?></code></td>
                        <td class="fw-medium">
                            <?= htmlspecialchars($cust['name']) ?><br>
                            <span class="text-secondary small"><?= htmlspecialchars($cust['gender'] ?? 'N/A') ?> <?= ($cust['birthday']) ? '| Birthday: ' . date('d M', strtotime($cust['birthday'])) : '' ?></span>
                        </td>
                        <td><?= htmlspecialchars($cust['phone']) ?></td>
                        <td><?= htmlspecialchars($cust['email'] ?? 'N/A') ?></td>
                        <td>
                            <?php
                            $badge = 'bg-secondary';
                            if ($cust['membership_level'] === 'Silver') $badge = 'bg-info text-dark';
                            if ($cust['membership_level'] === 'Gold') $badge = 'bg-warning text-dark';
                            if ($cust['membership_level'] === 'Platinum') $badge = 'bg-primary';
                            ?>
                            <span class="badge <?= $badge ?> rounded-pill px-2 py-1"><?= $cust['membership_level'] ?></span>
                        </td>
                        <td class="text-center fw-bold text-success"><?= number_format($cust['reward_points']) ?> pts</td>
                        <td class="text-secondary small"><?= htmlspecialchars($cust['address'] ?? 'N/A') ?></td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-outline-info btn-sm rounded-pill px-3 edit-btn" 
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
                                    <i class="fa-solid fa-pencil me-1"></i> Edit
                                </button>
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3 delete-btn" data-id="<?= $cust['id'] ?>" <?= ($cust['id'] == 1) ? 'disabled' : '' ?>>
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
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Register Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-8">
                        <label for="createName" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="createName" name="name" required placeholder="e.g. John Doe">
                    </div>
                    <div class="col-4">
                        <label for="createGender" class="form-label">Gender</label>
                        <select class="form-select" id="createGender" name="gender">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="createPhone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" id="createPhone" name="phone" required placeholder="Phone...">
                    </div>
                    <div class="col-6">
                        <label for="createBirthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="createBirthday" name="birthday">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="createEmail" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="createEmail" name="email" placeholder="Email...">
                </div>
                <div class="mb-3">
                    <label for="createAddress" class="form-label">Address</label>
                    <textarea class="form-control" id="createAddress" name="address" rows="3" placeholder="Home address..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Register Customer</button>
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
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pencil text-info me-2"></i> Edit Customer CRM Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-8">
                        <label for="editName" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="col-4">
                        <label for="editGender" class="form-label">Gender</label>
                        <select class="form-select" id="editGender" name="gender">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="editPhone" class="form-label">Phone Number *</label>
                        <input type="text" class="form-control" id="editPhone" name="phone" required>
                    </div>
                    <div class="col-6">
                        <label for="editBirthday" class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="editBirthday" name="birthday">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="editEmail" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="editEmail" name="email">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="editPoints" class="form-label">Reward Points</label>
                        <input type="number" class="form-control" id="editPoints" name="reward_points" min="0" required>
                    </div>
                    <div class="col-6">
                        <label for="editLevel" class="form-label">Membership Level</label>
                        <select class="form-select" id="editLevel" name="membership_level">
                            <option value="Bronze">Bronze</option>
                            <option value="Silver">Silver</option>
                            <option value="Gold">Gold</option>
                            <option value="Platinum">Platinum</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="editAddress" class="form-label">Address</label>
                    <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#customersTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search CRM..."
        }
    });

    // Create AJAX
    const createForm = document.getElementById('createForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
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
            Swal.fire({
                icon: 'success',
                title: 'Created!',
                text: 'Customer profile registered successfully.',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
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
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Customer profile updated successfully.',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message,
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            });
        });
    });

    // Delete AJAX
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Delete Customer Profile?',
            text: "This customer CRM records will be removed permanently!",
            icon: 'warning',
            showCancelButton: true,
            background: '#1e293b',
            color: '#f8fafc',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
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
                        title: 'Deleted!',
                        text: 'Customer removed successfully.',
                        background: '#1e293b',
                        color: '#f8fafc',
                        confirmButtonColor: '#3b82f6'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message,
                        background: '#1e293b',
                        color: '#f8fafc',
                        confirmButtonColor: '#3b82f6'
                    });
                });
            }
        });
    });
});
</script>
