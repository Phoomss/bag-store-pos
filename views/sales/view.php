<?php $title = 'Sale Invoice Details'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Invoice details: <code><?= htmlspecialchars($sale['invoice_no']) ?></code></h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="window.open('/pos/receipt/<?= $sale['id'] ?>', '_blank', 'width=450,height=600')">
            <i class="fa-solid fa-print me-1"></i> Print Receipt
        </button>
        <a href="/sales" class="btn btn-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Invoices
        </a>
    </div>
</div>

<div class="row">
    <!-- Left Column: Items and payments -->
    <div class="col-lg-8">
        <!-- Items table -->
        <div class="glass-panel mb-4">
            <h5 class="fw-bold mb-3">Invoice Items</h5>
            <div class="table-responsive">
                <table class="table align-middle text-light" style="font-size: 14px;">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sale['items'] as $item): ?>
                            <tr class="border-secondary">
                                <td class="fw-bold text-light"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><code><?= htmlspecialchars($item['sku']) ?></code></td>
                                <td class="text-end">฿<?= number_format($item['selling_price'], 2) ?></td>
                                <td class="text-center"><?= $item['quantity'] ?></td>
                                <td class="text-end text-success fw-bold">฿<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments list -->
        <div class="glass-panel">
            <h5 class="fw-bold mb-3">Payments Ledger</h5>
            <div class="table-responsive">
                <table class="table align-middle text-light" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>Method</th>
                            <th>Paid Amount</th>
                            <th>Reference ID</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sale['payments'])): ?>
                            <tr class="border-secondary">
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($sale['payment_method']) ?></span></td>
                                <td class="text-success fw-bold">฿<?= number_format($sale['paid_amount'], 2) ?></td>
                                <td><code><?= htmlspecialchars($sale['reference_no'] ?? 'N/A') ?></code></td>
                                <td><?= date('d M Y H:i', strtotime($sale['created_at'])) ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sale['payments'] as $pay): ?>
                                <tr class="border-secondary">
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($pay['payment_method']) ?></span></td>
                                    <td class="text-success fw-bold">฿<?= number_format($pay['amount'], 2) ?></td>
                                    <td><code><?= htmlspecialchars($pay['reference_no'] ?? 'N/A') ?></code></td>
                                    <td><?= date('d M Y H:i', strtotime($pay['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Status info panel -->
    <div class="col-lg-4">
        <div class="glass-panel">
            <h5 class="fw-bold mb-4">Transaction Summary</h5>
            
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">Invoice Reference:</span>
                <span class="fw-bold"><code><?= htmlspecialchars($sale['invoice_no']) ?></code></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">Customer CRM:</span>
                <span class="fw-bold"><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">CRM Code:</span>
                <span><code><?= htmlspecialchars($sale['customer_code'] ?? 'N/A') ?></code></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">Cashier Staff:</span>
                <span><?= htmlspecialchars($sale['cashier_name']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">Date & Time:</span>
                <span><?= date('d M Y H:i:s', strtotime($sale['created_at'])) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">Payment Method:</span>
                <span class="badge bg-secondary"><?= htmlspecialchars($sale['payment_method']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary pb-2">
                <span class="text-secondary">Order Status:</span>
                <?php
                $statusBadge = 'bg-secondary';
                if ($sale['status'] === 'Completed') $statusBadge = 'bg-success';
                if ($sale['status'] === 'Cancelled') $statusBadge = 'bg-danger';
                if ($sale['status'] === 'Held') $statusBadge = 'bg-warning text-dark';
                ?>
                <span class="badge <?= $statusBadge ?>"><?= $sale['status'] ?></span>
            </div>
            
            <?php if ($sale['notes']): ?>
                <div class="mb-3 border-bottom border-secondary pb-2">
                    <span class="text-secondary d-block mb-1">Notes:</span>
                    <p class="small text-light m-0"><?= htmlspecialchars($sale['notes']) ?></p>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Subtotal:</span>
                <span>฿<?= number_format($sale['subtotal'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Points Discount:</span>
                <span class="text-danger">-฿<?= number_format($sale['discount_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">VAT:</span>
                <span>฿<?= number_format($sale['vat_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2 pt-2 border-top border-secondary">
                <span class="text-secondary fw-bold">Grand Total:</span>
                <h5 class="fw-bold m-0 text-success">฿<?= number_format($sale['total_amount'], 2) ?></h5>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Cash Received:</span>
                <span class="fw-bold text-light">฿<?= number_format($sale['paid_amount'], 2) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Change Returned:</span>
                <span class="fw-bold text-warning">฿<?= number_format($sale['change_amount'], 2) ?></span>
            </div>
        </div>
    </div>
</div>
