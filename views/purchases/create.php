<?php $title = 'สร้างใบสั่งซื้อสินค้าใหม่'; ?>

<style>
    #itemsTable {
        table-layout: fixed;
    }
    #itemsTable td {
        overflow: hidden;
    }
    .select2-container {
        max-width: 100% !important;
    }
    .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-plus text-primary me-2"></i> สร้างใบสั่งซื้อสินค้าใหม่</h4>
    <a href="/purchases" class="btn btn-secondary btn-sm rounded-pill px-3 no-print">
        <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าจัดการสั่งซื้อ
    </a>
</div>

<form id="createPurchaseForm">
    <div class="row g-4">
        <!-- PO Header details -->
        <div class="col-lg-4">
            <div class="glass-panel border border-secondary shadow-sm mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-file-invoice text-primary me-2"></i> ข้อมูลใบสั่งซื้อ</h5>
                
                <div class="mb-3">
                    <label for="supplier_id" class="form-label small fw-semibold text-secondary">คู่ค้า / ผู้จัดจำหน่าย *</label>
                    <select class="form-select select2-enable" id="supplier_id" name="supplier_id" required>
                        <option value="">เลือกผู้จัดจำหน่าย</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="order_date" class="form-label small fw-semibold text-secondary">วันที่สั่งซื้อ *</label>
                    <input type="date" class="form-control" id="order_date" name="order_date" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label small fw-semibold text-secondary">สถานะแรกเริ่ม *</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Ordered">สั่งสินค้าแล้ว (รอการจัดส่ง)</option>
                        <option value="Received">รับสินค้าเข้าคลังแล้ว (เพิ่มเข้าสต็อกทันที)</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label for="invoice_no" class="form-label small fw-semibold text-secondary">เลขที่ใบส่งสินค้าอ้างอิง (ถ้ามี)</label>
                    <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="เช่น เลขใบส่งของคู่ค้า...">
                </div>
            </div>

            <!-- Payment details -->
            <div class="glass-panel border border-secondary shadow-sm">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-credit-card text-success me-2"></i> บันทึกการชำระเงิน</h5>
                
                <div class="mb-3">
                    <label for="paid_amount" class="form-label small fw-semibold text-secondary">ยอดชำระเงินเริ่มต้น</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                        <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" value="0.00">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="payment_method" class="form-label small fw-semibold text-secondary">ช่องทางการชำระเงิน</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="Cash">เงินสด</option>
                        <option value="Bank Transfer">โอนเงินผ่านธนาคาร</option>
                        <option value="Cheque">เช็คธนาคาร</option>
                    </select>
                </div>
                
                <div class="mb-0">
                    <label for="reference_no" class="form-label small fw-semibold text-secondary">หมายเลขอ้างอิง / สลิปโอนเงิน</label>
                    <input type="text" class="form-control" id="reference_no" name="reference_no" placeholder="เช่น เลขที่อ้างอิงธุรกรรม...">
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="col-lg-8">
            <div class="glass-panel border border-secondary shadow-sm h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark m-0"><i class="fa-solid fa-cart-flatbed-suitcase text-primary me-2"></i> รายการสินค้าสั่งซื้อ</h5>
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="addItemRowBtn">
                        <i class="fa-solid fa-plus me-1"></i> เพิ่มรายการสินค้า
                    </button>
                </div>

                <div class="table-responsive flex-grow-1" style="min-height: 280px;">
                    <table class="table align-middle text-light w-100" id="itemsTable" style="table-layout: fixed;" width="100%">
                        <thead>
                            <tr class="text-secondary small border-secondary">
                                <th style="width: 45%;">สินค้า</th>
                                <th style="width: 25%;" class="text-end">ราคาทุนต่อหน่วย</th>
                                <th style="width: 15%;" class="text-center">จำนวนสั่งซื้อ</th>
                                <th style="width: 15%;" class="text-end">ยอดรวมย่อย</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <!-- JS will inject rows here -->
                        </tbody>
                    </table>
                </div>

                <div class="border-top border-secondary pt-3 mt-3">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary fw-semibold">ยอดเงินรวมทั้งหมด:</span>
                                <h3 class="fw-bold m-0 text-success" id="grandTotalVal" data-total="0.00">฿0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 no-print">
                    <a href="/purchases" class="btn btn-outline-secondary rounded-pill px-4">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5">บันทึกใบสั่งซื้อ</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Product lookup database template -->
<script>
const productsCatalog = <?= json_encode($products) ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.select2-enable').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    const tbody = document.getElementById('itemsTableBody');
    let rowId = 0;

    // Add row function
    function addItemRow() {
        rowId++;
        const tr = document.createElement('tr');
        tr.className = 'border-secondary';
        tr.id = 'row_' + rowId;

        // Generate product selection dropdown options
        let options = '<option value="">เลือกสินค้ากระเป๋า...</option>';
        productsCatalog.forEach(p => {
            options += `<option value="${p.id}" data-cost="${p.cost_price}">${p.name} (${p.sku})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select class="form-select select2-row" name="items[${rowId}][product_id]" required>
                    ${options}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm shadow-sm" style="max-width: 150px;">
                    <span class="input-group-text bg-light border-secondary text-secondary">฿</span>
                    <input type="number" step="0.01" class="form-control text-end cost-input fw-semibold" name="items[${rowId}][cost_price]" required value="0.00">
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm shadow-sm justify-content-center mx-auto" style="width: 110px;">
                    <button type="button" class="btn btn-outline-secondary dec-qty-btn px-2">-</button>
                    <input type="number" class="form-control text-center qty-input fw-semibold" name="items[${rowId}][quantity]" required value="1" min="1" style="border-inline: none;">
                    <button type="button" class="btn btn-outline-secondary inc-qty-btn px-2">+</button>
                </div>
            </td>
            <td class="text-end fw-bold text-dark subtotal-val">฿0.00</td>
            <td>
                <button type="button" class="btn btn-link text-danger btn-sm delete-row-btn"><i class="fa-solid fa-trash-can"></i></button>
            </td>
        `;

        tbody.appendChild(tr);

        // Enable Select2 on the new row dropdown
        $(tr).find('.select2-row').select2({
            theme: 'bootstrap-5',
            width: '100%'
        }).on('change', function() {
            // Update cost price when item is selected
            const cost = $(this).find(':selected').data('cost') || 0.00;
            $(tr).find('.cost-input').val(parseFloat(cost).toFixed(2));
            calculateRowSubtotal(tr);
        });

        // Listen for quantity and cost price input adjustments
        $(tr).find('.cost-input, .qty-input').on('input', function() {
            calculateRowSubtotal(tr);
        });

        // Inc/Dec buttons
        $(tr).find('.inc-qty-btn').on('click', function() {
            const input = $(tr).find('.qty-input');
            input.val(parseInt(input.val() || 0) + 1);
            calculateRowSubtotal(tr);
        });
        $(tr).find('.dec-qty-btn').on('click', function() {
            const input = $(tr).find('.qty-input');
            const val = parseInt(input.val() || 0);
            if (val > 1) {
                input.val(val - 1);
                calculateRowSubtotal(tr);
            }
        });

        // Listen for delete button
        $(tr).find('.delete-row-btn').on('click', function() {
            tr.remove();
            calculateGrandTotal();
        });
    }

    // Add initial item row
    addItemRow();

    document.getElementById('addItemRowBtn').addEventListener('click', addItemRow);

    function calculateRowSubtotal(row) {
        const cost = parseFloat($(row).find('.cost-input').val()) || 0.00;
        const qty = parseInt($(row).find('.qty-input').val()) || 0;
        const subtotal = cost * qty;
        
        $(row).find('.subtotal-val').text('฿' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $(row).data('subtotal', subtotal);
        
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0.00;
        $('#itemsTableBody tr').each(function() {
            const sub = $(this).data('subtotal') || 0.00;
            total += sub;
        });

        $('#grandTotalVal').text('฿' + total.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#grandTotalVal').data('total', total);
        
        // Populate full payment if Received status is selected
        if (document.getElementById('status').value === 'Received') {
            document.getElementById('paid_amount').value = total.toFixed(2);
        }
    }

    // Handle Status Change to adjust payment default
    document.getElementById('status').addEventListener('change', function() {
        if (this.value === 'Received') {
            const total = $('#grandTotalVal').data('total') || 0.00;
            document.getElementById('paid_amount').value = total.toFixed(2);
        } else {
            document.getElementById('paid_amount').value = '0.00';
        }
    });

    // Handle PO creation Form AJAX submit
    const form = document.getElementById('createPurchaseForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate items count
        const rows = $('#itemsTableBody tr');
        if (rows.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่มีรายการสินค้า!',
                text: 'กรุณาเพิ่มรายการสินค้าสั่งซื้ออย่างน้อย 1 รายการ.',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        // Collect structured items data
        const items = [];
        let validationFailed = false;

        rows.each(function() {
            const prodId = $(this).find('.select2-row').val();
            const cost = parseFloat($(this).find('.cost-input').val()) || 0.00;
            const qty = parseInt($(this).find('.qty-input').val()) || 0;

            if (!prodId) {
                validationFailed = true;
                return false;
            }

            items.push({
                product_id: parseInt(prodId),
                cost_price: cost,
                quantity: qty,
                subtotal: cost * qty
            });
        });

        if (validationFailed) {
            Swal.fire({
                icon: 'warning',
                title: 'กรอกข้อมูลไม่ครบถ้วน',
                text: 'กรุณาเลือกรายการสินค้าในแถวที่เพิ่มเข้ามา.',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const totalAmt = $('#grandTotalVal').data('total') || 0.00;
        const paidAmt = parseFloat(document.getElementById('paid_amount').value) || 0.00;

        if (paidAmt > totalAmt) {
            Swal.fire({
                icon: 'warning',
                title: 'ยอดเงินไม่ถูกต้อง',
                text: 'จำนวนเงินที่ชำระไม่สามารถเกินยอดรวมสั่งซื้อทั้งหมดได้.',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const data = {
            supplier_id: parseInt(document.getElementById('supplier_id').value),
            order_date: document.getElementById('order_date').value,
            status: document.getElementById('status').value,
            invoice_no: document.getElementById('invoice_no').value,
            paid_amount: paidAmt,
            payment_method: document.getElementById('payment_method').value,
            reference_no: document.getElementById('reference_no').value,
            total_amount: totalAmt,
            items: items
        };

        fetch('/purchases/store', {
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
                text: 'สร้างรายการใบสั่งซื้อสินค้าเรียบร้อยแล้ว.',
                background: '#ffffff',
                color: '#1e293b',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                window.location.href = '/purchases';
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
});
</script>
