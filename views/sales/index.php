<?php $title = 'Sales Invoices'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Sales Invoices</h4>
    <a href="/pos" class="btn btn-primary btn-sm rounded-pill px-3">
        <i class="fa-solid fa-cart-shopping me-1"></i> Open POS Cashier
    </a>
</div>

<!-- Filters Panel -->
<div class="glass-panel mb-4">
    <form method="GET" action="/sales" class="row g-3">
        <div class="col-lg-3 col-md-6">
            <label for="search" class="form-label small text-secondary">Search Invoice, Customer</label>
            <input type="text" class="form-control form-control-sm" id="search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Search...">
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="status" class="form-label small text-secondary">Status</label>
            <select class="form-select form-select-sm" id="status" name="status">
                <option value="">All Status</option>
                <option value="Completed" <?= ($filters['status'] === 'Completed') ? 'selected' : '' ?>>Completed</option>
                <option value="Held" <?= ($filters['status'] === 'Held') ? 'selected' : '' ?>>Held</option>
                <option value="Cancelled" <?= ($filters['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled (Refunded)</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="payment_status" class="form-label small text-secondary">Payment Status</label>
            <select class="form-select form-select-sm" id="payment_status" name="payment_status">
                <option value="">All Payments</option>
                <option value="Paid" <?= ($filters['payment_status'] === 'Paid') ? 'selected' : '' ?>>Paid</option>
                <option value="Refunded" <?= ($filters['payment_status'] === 'Refunded') ? 'selected' : '' ?>>Refunded</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="start_date" class="form-label small text-secondary">Start Date</label>
            <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
        </div>
        <div class="col-lg-2 col-md-4">
            <label for="end_date" class="form-label small text-secondary">End Date</label>
            <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
        </div>
        <div class="col-lg-1 col-md-12 d-flex align-items-end">
            <button type="submit" class="btn btn-secondary btn-sm w-100 rounded-pill"><i class="fa-solid fa-filter me-1"></i> Filter</button>
        </div>
    </form>
</div>

<!-- Table Panel -->
<div class="glass-panel">
    <div class="table-responsive">
        <table class="table align-middle w-100" id="salesTable">
            <thead>
                <tr class="text-secondary small border-secondary">
                    <th>Invoice No</th>
                    <th>Customer Name</th>
                    <th>Cashier</th>
                    <th>Date & Time</th>
                    <th>Payment Method</th>
                    <th class="text-end">Grand Total</th>
                    <th>Status</th>
                    <th style="width: 180px;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr class="border-secondary text-light">
                        <td>
                            <a href="/sales/view/<?= $sale['id'] ?>" class="text-decoration-none text-info fw-bold">
                                <code><?= htmlspecialchars($sale['invoice_no']) ?></code>
                            </a>
                        </td>
                        <td class="fw-medium"><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?></td>
                        <td><?= htmlspecialchars($sale['cashier_name']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($sale['created_at'])) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($sale['payment_method']) ?></span></td>
                        <td class="text-end fw-bold text-success">฿<?= number_format($sale['total_amount'], 2) ?></td>
                        <td>
                            <?php
                            $badge = 'bg-secondary';
                            if ($sale['status'] === 'Completed') $badge = 'bg-success';
                            if ($sale['status'] === 'Held') $badge = 'bg-warning text-dark';
                            if ($sale['status'] === 'Cancelled') $badge = 'bg-danger';
                            ?>
                            <span class="badge <?= $badge ?>"><?= $sale['status'] ?></span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/sales/view/<?= $sale['id'] ?>" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </a>
                                <?php if ($sale['status'] === 'Completed' && \App\Helpers\Session::checkRole(['Owner', 'Admin'])): ?>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 refund-btn" data-id="<?= $sale['id'] ?>" data-inv="<?= htmlspecialchars($sale['invoice_no']) ?>">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Refund
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#salesTable').DataTable({
        responsive: true,
        searching: false, // filtered via top panel
        pageLength: 10,
        order: [[3, 'desc']], // sort by date desc
        language: {
            search: "_INPUT_"
        }
    });

    // Refund AJAX submission
    $('.refund-btn').on('click', function() {
        const id = $(this).data('id');
        const inv = $(this).data('inv');

        Swal.fire({
            title: 'Refund Invoice?',
            text: `Refund invoice ${inv}? This will void the payment, return sold stock items back to warehouse, and retract loyalty points earned.`,
            icon: 'warning',
            showCancelButton: true,
            background: '#1e293b',
            color: '#f8fafc',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, refund sale!'
        }).then((result) => {
            if (result.isConfirmed) {
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
                        title: 'Refunded!',
                        text: 'Sale refunded and stock quantities updated.',
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
