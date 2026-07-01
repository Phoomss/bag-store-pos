<style>
    /* Custom POS modern layout overrides */
    .pos-search-wrapper {
        position: relative;
    }
    .pos-search-input {
        height: 48px;
        border-radius: 14px !important;
        font-size: 14px;
        padding-left: 45px;
    }
    .pos-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 16px;
        z-index: 10;
    }
    .cat-filter-btn {
        background-color: var(--bg-secondary) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-main) !important;
        border-radius: 20px !important;
        padding: 8px 18px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .cat-filter-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }
    .pos-product-card {
        border-radius: 16px;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        padding: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: space-between;
    }
    .pos-product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        border-color: var(--accent-color);
    }
    .pos-product-image {
        height: 135px;
        object-fit: cover;
        border-radius: 12px;
        width: 100%;
        margin-bottom: 10px;
    }
    .pos-cart-item {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 12px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background-color 0.2s;
    }
    .pos-cart-item:hover {
        background-color: rgba(15, 23, 42, 0.01);
    }
    .qty-counter {
        display: flex;
        align-items: center;
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 2px 6px;
    }
    .qty-btn {
        background: none;
        border: none;
        color: var(--text-main);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px;
        transition: background-color 0.2s;
    }
    .qty-btn:hover {
        background-color: var(--bg-tertiary);
    }
    .qty-value {
        width: 28px;
        text-align: center;
        font-weight: 600;
        font-size: 13px;
        color: var(--text-main);
    }
    .pay-mode-btn {
        background: var(--bg-secondary) !important;
        border: 1.5px solid var(--border-color) !important;
        color: var(--text-main) !important;
        border-radius: 14px !important;
        padding: 16px 12px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .pay-mode-btn i {
        font-size: 20px;
    }
    .pay-mode-btn.active {
        border-color: var(--accent-color) !important;
        background-color: rgba(59, 130, 246, 0.04) !important;
        color: var(--accent-color) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    }
    .fast-cash-btn {
        border-radius: 20px !important;
        font-weight: 600 !important;
    }
    .nav-pills-custom {
        display: flex;
        background-color: var(--bg-secondary) !important;
    }
    .nav-pills-custom .btn {
        font-weight: 600;
        transition: all 0.2s ease;
    }
    @media (max-width: 991.98px) {
        #posProductsContainer {
            max-height: calc(100vh - 280px) !important;
        }
        #posRightColumn .glass-panel {
            max-height: calc(100vh - 200px) !important;
        }
    }
</style>

<!-- Tab toggles for smaller screens (< 992px) -->
<div class="d-flex d-lg-none nav-pills-custom mb-3 bg-white p-2 rounded-pill border border-secondary shadow-sm">
    <button class="btn btn-primary w-50 rounded-pill py-2 active" id="posTabProducts">
        <i class="fa-solid fa-boxes-stacked me-1"></i> สินค้า
    </button>
    <button class="btn btn-outline-secondary w-50 rounded-pill py-2 ms-2 position-relative" id="posTabCart">
        <i class="fa-solid fa-cart-shopping me-1"></i> ตะกร้า
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="cartBadgeCount" style="margin-top: 5px; margin-left: -15px;">0</span>
    </button>
</div>

<div class="row g-3">
    <!-- Left Column: Products Grid Catalog (65% width equivalent) -->
    <div class="col-xl-8 col-lg-7 d-block" id="posLeftColumn">
        <!-- Search & Category Filters -->
        <div class="glass-panel mb-3 py-3 shadow-sm border border-secondary">
            <div class="row g-2 align-items-center">
                <div class="col-md-5 col-12">
                    <div class="pos-search-wrapper">
                        <i class="fa-solid fa-magnifying-glass pos-search-icon"></i>
                        <input type="text" class="form-control pos-search-input" id="posSearchInput" placeholder="สแกนบาร์โค้ด หรือพิมพ์ SKU / ชื่อสินค้า...">
                    </div>
                </div>
                <div class="col-md-7 col-12">
                    <div class="d-flex gap-2 overflow-x-auto pb-1" id="categoryFilterRow">
                        <button class="btn btn-primary btn-sm rounded-pill px-3 cat-filter-btn active" data-cat="">ทั้งหมด</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card Grid -->
        <div class="row g-3 overflow-y-auto" id="posProductsContainer" style="max-height: calc(100vh - 180px); min-height: 400px; padding-bottom: 20px;">
            <!-- JS will populate product cards here -->
        </div>
    </div>

    <!-- Right Column: Shopping Cart & Totals (35% width equivalent) -->
    <div class="col-xl-4 col-lg-5 d-none d-lg-block" id="posRightColumn">
        <div class="glass-panel d-flex flex-column h-100 mb-0 shadow-sm" style="max-height: calc(100vh - 100px);">
            <!-- Customer CRM Selection -->
            <div class="mb-3">
                <label for="posCustomerSelect" class="form-label small text-secondary fw-semibold">บัญชีลูกค้า CRM</label>
                <select class="form-select select2-customer-enable" id="posCustomerSelect" style="width: 100%;">
                    <!-- Walk-in Customer default loaded here -->
                </select>
                <div class="d-flex justify-content-between mt-2 px-1 small">
                    <span class="text-secondary">ระดับสมาชิก: <span id="posCustLevel" class="badge bg-secondary">Bronze</span></span>
                    <span class="text-secondary">คะแนนสะสม: <span id="posCustPoints" class="fw-bold text-success">0</span> คะแนน</span>
                </div>
            </div>

            <div class="border-top border-secondary my-2"></div>

            <!-- Cart Items List -->
            <h6 class="fw-bold mb-2 text-secondary small"><i class="fa-solid fa-cart-shopping me-1"></i> รายการสินค้าในตะกร้า</h6>
            <div class="overflow-y-auto flex-grow-1 mb-3 pr-1" id="posCartContainer" style="min-height: 200px;">
                <!-- JS will inject rows here -->
                <div class="text-center text-secondary py-5" id="posCartEmptyMsg">
                    <i class="fa-solid fa-cart-arrow-down fa-2xl mb-3 text-secondary bg-opacity-10"></i>
                    <p class="small m-0">ไม่มีรายการสินค้าในตะกร้า</p>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 mt-auto">
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>ยอดรวมสินค้า:</span>
                    <span id="posSubtotalVal" class="fw-medium text-light">฿0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>ส่วนลด / คูปอง:</span>
                    <span class="text-danger fw-medium" id="posDiscountVal">-฿0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>ภาษีมูลค่าเพิ่ม (<?= $_ENV['TAX_RATE'] ?? '7.0' ?>%):</span>
                    <span id="posTaxVal" class="fw-medium text-light">฿0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="fw-bold text-light">ยอดชำระสุทธิ:</span>
                    <h3 class="fw-bold text-success m-0" id="posGrandTotalVal">฿0.00</h3>
                </div>

                <!-- Action buttons -->
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2" id="posHoldBtn">
                            <i class="fa-solid fa-pause me-1"></i> พักบิลขาย
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-primary w-100 rounded-pill py-2 fw-bold" id="posPayBtn">
                            <i class="fa-solid fa-cash-register me-1"></i> ชำระเงิน
                        </button>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <button class="btn btn-link btn-xs text-secondary text-decoration-none" id="posResumeListBtn"><i class="fa-solid fa-clock-rotate-left me-1"></i> ดึงรายการขายที่พักไว้</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border border-secondary shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-money-bill-wave text-success me-2"></i> ชำระเงินค่าสินค้า</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="row">
                    <!-- Left: Pricing summary & payment methods -->
                    <div class="col-lg-6 mb-4 mb-lg-0 border-end border-secondary pr-lg-4">
                        <div class="bg-dark bg-opacity-25 rounded-4 p-3 mb-4 text-center border border-secondary">
                            <span class="text-secondary small">ยอดที่ต้องชำระสุทธิ</span>
                            <h2 class="fw-bold text-success m-0" id="modalTotalDueVal">฿0.00</h2>
                        </div>

                        <!-- Loyalty Points Redemption option -->
                        <div class="card bg-secondary bg-opacity-20 border border-secondary rounded-4 p-3 mb-3 d-none" id="loyaltyRedemptionCard">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="redeemPointsToggle">
                                <label class="form-check-label fw-medium text-light" for="redeemPointsToggle">ใช้คะแนนสะสมแลกส่วนลด</label>
                            </div>
                            <div class="mt-2 text-secondary small" id="pointsRedeemInfo">
                                <!-- JS details -->
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-secondary small fw-semibold mb-2">ช่องทางการชำระเงิน</label>
                            <div class="row g-2" id="paymentMethodsGrid">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100 pay-mode-btn active" data-mode="Cash"><i class="fa-solid fa-money-bill-1 text-success"></i> เงินสด</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100 pay-mode-btn" data-mode="PromptPay QR"><i class="fa-solid fa-qrcode text-info"></i> พร้อมเพย์ QR</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100 pay-mode-btn" data-mode="Credit Card"><i class="fa-solid fa-credit-card text-warning"></i> บัตรเครดิต</button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100 pay-mode-btn" data-mode="Bank Transfer"><i class="fa-solid fa-bank text-danger"></i> โอนเงิน</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Input fields & QR codes -->
                    <div class="col-lg-6 pl-lg-4">
                        <!-- Cash payment inputs -->
                        <div id="checkoutCashInputs">
                            <div class="mb-3">
                                <label for="checkoutPaidAmt" class="form-label fw-semibold text-secondary small">รับเงินสดมา *</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-dark border-secondary text-secondary">฿</span>
                                    <input type="number" step="0.01" class="form-control text-end fw-bold text-success" id="checkoutPaidAmt" placeholder="0.00">
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <!-- Fast cash options -->
                                <div class="col-4"><button type="button" class="btn btn-dark w-100 fast-cash-btn" data-val="100">฿100</button></div>
                                <div class="col-4"><button type="button" class="btn btn-dark w-100 fast-cash-btn" data-val="500">฿500</button></div>
                                <div class="col-4"><button type="button" class="btn btn-dark w-100 fast-cash-btn" data-val="1000">฿1,000</button></div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-secondary small fw-semibold">เงินทอน</label>
                                <h3 class="fw-bold text-warning" id="checkoutChangeVal">฿0.00</h3>
                            </div>
                        </div>

                        <!-- PromptPay QR dummy visualization -->
                        <div class="text-center d-none" id="checkoutQrContainer">
                            <p class="text-secondary small mb-3">สแกนคิวอาร์โค้ดนี้ด้วยแอปธนาคารเพื่อชำระเงิน</p>
                            <div class="p-3 bg-white d-inline-block rounded-4 mb-3 shadow-sm border">
                                <!-- Mock generated QR code -->
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=https://promptpay.io/0812345678" alt="Mock PromptPay QR" style="width: 180px; height: 180px;">
                            </div>
                            <div class="mb-3">
                                <label for="checkoutQrRef" class="form-label small text-secondary fw-semibold">หมายเลขอ้างอิงสลิปโอนเงิน</label>
                                <input type="text" class="form-control text-center" id="checkoutQrRef" placeholder="กรอกเลข Transaction ID...">
                            </div>
                        </div>

                        <!-- Card/Bank Transfer inputs -->
                        <div class="d-none" id="checkoutCardInputs">
                            <div class="mb-3">
                                <label for="checkoutCardRef" class="form-label fw-semibold text-secondary small">เลขที่อ้างอิงธุรกรรม / Slip ID</label>
                                <input type="text" class="form-control" id="checkoutCardRef" placeholder="ระบุเลขที่บัตร หรือหมายเลขสลิป...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="checkoutNotes" class="form-label small text-secondary fw-semibold">หมายเหตุ (เพิ่มเติม)</label>
                            <textarea class="form-control" id="checkoutNotes" rows="2" placeholder="ระบุรายละเอียดเพิ่มเติม เช่น รายละเอียดที่ส่งสินค้า..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success rounded-pill px-5 fw-bold" id="checkoutConfirmBtn">ยืนยันชำระเงิน & พิมพ์ใบเสร็จ</button>
            </div>
        </div>
    </div>
</div>

<!-- Held Sales Resume Modal -->
<div class="modal fade" id="heldSalesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-secondary shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> รายการขายที่พักไว้</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-close="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle text-light" style="font-size: 13px;">
                        <thead>
                            <tr class="text-secondary small border-secondary">
                                <th>เลขที่อ้างอิง</th>
                                <th>ชื่อลูกค้า</th>
                                <th class="text-end">ยอดรวม</th>
                                <th class="text-center">ดึงข้อมูล</th>
                            </tr>
                        </thead>
                        <tbody id="heldSalesBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template data loaders -->
<script>
const productsCatalog = <?= json_encode($products) ?>;
const categoriesCatalog = <?= json_encode(array_values((new App\Repositories\CategoryRepository())->all())) ?>;
const customersCatalog = <?= json_encode($customers) ?>;
const taxRate = parseFloat("<?= $_ENV['TAX_RATE'] ?? '7.0' ?>");
</script>

<!-- POS Controller logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let selectedCategory = '';
    let selectedPaymentMode = 'Cash';
    let selectedCustomer = null;
    let redeemPointsDiscount = 0.00;

    // Move modals to body to prevent focus/accessibility issues
    const checkoutModalElem = document.getElementById('checkoutModal');
    const heldSalesModalElem = document.getElementById('heldSalesModal');
    if (checkoutModalElem) document.body.appendChild(checkoutModalElem);
    if (heldSalesModalElem) document.body.appendChild(heldSalesModalElem);

    // Responsive Tabs Switching Logic
    const posTabProducts = document.getElementById('posTabProducts');
    const posTabCart = document.getElementById('posTabCart');
    const posLeftColumn = document.getElementById('posLeftColumn');
    const posRightColumn = document.getElementById('posRightColumn');

    if (posTabProducts && posTabCart && posLeftColumn && posRightColumn) {
        posTabProducts.addEventListener('click', function() {
            // Show products, hide cart
            posLeftColumn.classList.remove('d-none');
            posLeftColumn.classList.add('d-block');
            posRightColumn.classList.remove('d-block');
            posRightColumn.classList.add('d-none');

            // Toggle active styles
            posTabProducts.classList.add('btn-primary', 'active');
            posTabProducts.classList.remove('btn-outline-secondary');
            posTabCart.classList.add('btn-outline-secondary');
            posTabCart.classList.remove('btn-primary', 'active');
        });

        posTabCart.addEventListener('click', function() {
            // Hide products, show cart
            posLeftColumn.classList.remove('d-block');
            posLeftColumn.classList.add('d-none');
            posRightColumn.classList.remove('d-none');
            posRightColumn.classList.add('d-block');

            // Toggle active styles
            posTabCart.classList.add('btn-primary', 'active');
            posTabCart.classList.remove('btn-outline-secondary');
            posTabProducts.classList.add('btn-outline-secondary');
            posTabProducts.classList.remove('btn-primary', 'active');
        });
    }

    // Initialize Customers Select2
    $('.select2-customer-enable').select2({
        theme: 'bootstrap-5',
        dropdownAutoWidth: true
    }).on('change', function() {
        const id = $(this).val();
        selectCustomer(id);
    });

    // Populate customer choices
    let custOptions = '';
    customersCatalog.forEach(c => {
        const selected = (c.id == 1) ? 'selected' : '';
        custOptions += `<option value="${c.id}" ${selected}>${c.name} (${c.phone})</option>`;
    });
    $('#posCustomerSelect').html(custOptions).trigger('change');

    function selectCustomer(id) {
        selectedCustomer = customersCatalog.find(c => c.id == id) || null;
        if (selectedCustomer) {
            $('#posCustLevel').text(selectedCustomer.membership_level);
            $('#posCustPoints').text(selectedCustomer.reward_points);
            
            // Show points redemption switch if customer has points and is not walk-in
            if (selectedCustomer.id != 1 && selectedCustomer.reward_points > 0) {
                $('#loyaltyRedemptionCard').removeClass('d-none');
                $('#pointsRedeemInfo').text(`แต้มสะสมที่มี: ${selectedCustomer.reward_points} คะแนน. (1 คะแนน = ฿1.00 ส่วนลด)`);
            } else {
                $('#loyaltyRedemptionCard').addClass('d-none');
                $('#redeemPointsToggle').prop('checked', false);
                redeemPointsDiscount = 0.00;
            }
        }
        calculateCartTotals();
    }

    // Populate Category Row filters
    const catRow = document.getElementById('categoryFilterRow');
    categoriesCatalog.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'btn btn-dark btn-sm rounded-pill px-3 cat-filter-btn';
        btn.setAttribute('data-cat', cat.id);
        btn.innerText = cat.name;
        catRow.appendChild(btn);
    });

    $('.cat-filter-btn').on('click', function() {
        $('.cat-filter-btn').removeClass('btn-primary active').addClass('btn-dark');
        $(this).addClass('btn-primary active').removeClass('btn-dark');
        selectedCategory = $(this).data('cat');
        renderProductCards();
    });

    // Render Product Cards Grid
    function renderProductCards() {
        const container = document.getElementById('posProductsContainer');
        if (!container) return;
        container.innerHTML = '';

        let query = document.getElementById('posSearchInput').value.toLowerCase();

        productsCatalog.forEach(p => {
            // Apply category filter
            if (selectedCategory !== '' && p.category_id != selectedCategory) return;

            // Apply search filter
            if (query !== '') {
                const match = p.name.toLowerCase().includes(query) || p.sku.toLowerCase().includes(query) || p.barcode.includes(query);
                if (!match) return;
            }

            const img = p.primary_image ? p.primary_image : 'https://images.unsplash.com/photo-1547949003-9792a18a2601?q=80&w=200';
            const price = p.promotion_price ? p.promotion_price : p.selling_price;
            const hasPromo = p.promotion_price ? true : false;
            
            // Calculate actual available quantity to show (stock_quantity minus quantity currently in cart)
            const inCart = cart.find(item => item.product_id === p.id);
            const cartQty = inCart ? inCart.quantity : 0;
            const displayStock = Math.max(0, p.stock_quantity - cartQty);
            
            let stockBadge = `<span class="badge bg-success small w-100 py-2 mt-auto" style="border-radius: 8px;">มีสินค้า (${displayStock})</span>`;
            if (displayStock <= 0) {
                stockBadge = `<span class="badge bg-danger small w-100 py-2 mt-auto" style="border-radius: 8px;">สินค้าหมด</span>`;
            } else if (displayStock <= p.min_stock) {
                stockBadge = `<span class="badge bg-warning text-dark small w-100 py-2 mt-auto" style="border-radius: 8px;">สต็อกเหลือน้อย (${displayStock})</span>`;
            }

            const card = document.createElement('div');
            card.className = 'col-xxl-3 col-xl-4 col-sm-6 col-12';
            card.innerHTML = `
                <div class="pos-product-card" data-id="${p.id}">
                    <img src="${p.primary_image || 'https://images.unsplash.com/photo-1547949003-9792a18a2601?q=80&w=200'}" class="pos-product-image">
                    <div class="d-flex flex-column flex-grow-1 justify-content-between">
                        <div class="mb-2">
                            <span class="text-secondary small font-monospace d-block mb-1" style="font-size: 11px;">SKU: ${p.sku}</span>
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 13px; line-height: 1.25; min-height: 32px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${p.name}</h6>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                ${hasPromo ? `
                                    <div>
                                        <span class="text-decoration-line-through text-secondary small" style="font-size: 10px;">฿${parseFloat(p.selling_price).toFixed(2)}</span>
                                        <span class="text-success fw-bold d-block" style="font-size: 14px;">฿${parseFloat(p.promotion_price).toFixed(2)}</span>
                                    </div>
                                ` : `<span class="text-dark fw-bold" style="font-size: 14px;">฿${parseFloat(p.selling_price).toFixed(2)}</span>`}
                            </div>
                            ${stockBadge}
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(card);

            // Add click listener
            card.querySelector('.pos-product-card').addEventListener('click', function() {
                if (displayStock <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'สินค้าหมดคลัง',
                        text: 'ไม่สามารถเลือกเพิ่มได้เนื่องจากสต็อกในระบบถูกเลือกไปหมดแล้ว.',
                        background: '#ffffff',
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                addToCart(p);
            });
        });
    }

    renderProductCards();

    // Search bar event
    document.getElementById('posSearchInput').addEventListener('input', renderProductCards);

    // Barcode scanner simulator
    document.getElementById('posSearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const term = this.value;
            const product = productsCatalog.find(p => p.barcode === term || p.sku.toLowerCase() === term.toLowerCase());
            if (product) {
                if (product.stock_quantity <= 0) {
                    Swal.fire({ icon: 'warning', title: 'สินค้าหมดคลัง', text: 'สินค้ารายการนี้ไม่มีสต็อก.', background: '#ffffff' });
                } else {
                    addToCart(product);
                    this.value = '';
                    renderProductCards();
                }
            }
        }
    });

    // Add product to Cart
    function addToCart(p) {
        if (p.stock_quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'สินค้าหมดคลัง',
                text: `ไม่สามารถเพิ่มสินค้า '${p.name}' ได้เนื่องจากไม่มีสต็อกเหลืออยู่ในระบบ.`,
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const price = p.promotion_price ? parseFloat(p.promotion_price) : parseFloat(p.selling_price);
        const existing = cart.find(item => item.product_id === p.id);

        if (existing) {
            if (existing.quantity >= p.stock_quantity) {
                Swal.fire({
                    icon: 'warning',
                    title: 'เกินขีดจำกัดคลังสินค้า',
                    text: `จำนวนสินค้าที่เลือกได้คือ ${p.stock_quantity} ชิ้น.`,
                    background: '#ffffff',
                    color: '#0f172a',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }
            existing.quantity++;
            existing.subtotal = existing.quantity * existing.price;
        } else {
            cart.push({
                product_id: p.id,
                name: p.name,
                sku: p.sku,
                barcode: p.barcode,
                price: price,
                quantity: 1,
                subtotal: price,
                catalog_stock: p.stock_quantity
            });
        }
        renderCart();
    }

    // Render Cart HTML
    function renderCart() {
        const container = document.getElementById('posCartContainer');

        // Update cart badge count
        const badge = document.getElementById('cartBadgeCount');
        if (badge) {
            let totalQty = 0;
            cart.forEach(item => totalQty += item.quantity);
            if (totalQty > 0) {
                badge.innerText = totalQty;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center text-secondary py-5" id="posCartEmptyMsg">
                    <i class="fa-solid fa-cart-arrow-down fa-2xl mb-3 text-secondary bg-opacity-10"></i>
                    <p class="small m-0">ไม่มีรายการสินค้าในตะกร้า</p>
                </div>
            `;
            calculateCartTotals();
            renderProductCards();
            return;
        }

        container.innerHTML = '';

        cart.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'pos-cart-item';
            row.innerHTML = `
                <div style="width: 50%;">
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</h6>
                    <span class="text-secondary small" style="font-size: 11px;">฿${item.price.toFixed(2)} / ชิ้น</span>
                </div>
                <div class="qty-counter">
                    <button class="qty-btn dec-qty-btn"><i class="fa-solid fa-minus"></i></button>
                    <span class="qty-value">${item.quantity}</span>
                    <button class="qty-btn inc-qty-btn"><i class="fa-solid fa-plus"></i></button>
                </div>
                <div class="text-end" style="width: 25%;">
                    <span class="fw-bold text-success" style="font-size: 13px;">฿${item.subtotal.toFixed(2)}</span>
                </div>
            `;

            container.appendChild(row);

            // Bind actions
            row.querySelector('.dec-qty-btn').addEventListener('click', function() {
                if (item.quantity > 1) {
                    item.quantity--;
                    item.subtotal = item.quantity * item.price;
                } else {
                    cart.splice(index, 1);
                }
                renderCart();
            });

            row.querySelector('.inc-qty-btn').addEventListener('click', function() {
                if (item.quantity >= item.catalog_stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'เกินขีดจำกัดคลังสินค้า',
                        text: `จำนวนสินค้าที่เลือกได้คือ ${item.catalog_stock} ชิ้น.`,
                        background: '#ffffff',
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }
                item.quantity++;
                item.subtotal = item.quantity * item.price;
                renderCart();
            });
        });

        calculateCartTotals();
        renderProductCards();
    }

    // Points redemption logic
    $('#redeemPointsToggle').on('change', function() {
        if ($(this).is(':checked') && selectedCustomer) {
            let sub = 0.00;
            cart.forEach(item => sub += item.subtotal);
            redeemPointsDiscount = Math.min(sub, selectedCustomer.reward_points);
        } else {
            redeemPointsDiscount = 0.00;
        }
        calculateCartTotals();
    });

    // Calculate Cart Totals
    function calculateCartTotals() {
        let subtotal = 0.00;
        cart.forEach(item => subtotal += item.subtotal);

        const discountTotal = redeemPointsDiscount;
        const subAfterDisc = Math.max(0.00, subtotal - discountTotal);
        
        const taxVal = subAfterDisc * (taxRate / 100);
        const grandTotal = subAfterDisc + taxVal;

        $('#posSubtotalVal').text('฿' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#posDiscountVal').text('-฿' + discountTotal.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#posTaxVal').text('฿' + taxVal.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#posGrandTotalVal').text('฿' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})).data('val', grandTotal);
    }

    // Open Checkout Modal
    document.getElementById('posPayBtn').addEventListener('click', function() {
        if (cart.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ตะกร้าสินค้าว่างเปล่า',
                text: 'กรุณาเลือกสินค้าลงตะกร้าก่อนคิดเงิน.',
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        const grand = $('#posGrandTotalVal').data('val') || 0.00;
        $('#modalTotalDueVal').text('฿' + grand.toLocaleString('en-US', {minimumFractionDigits: 2}));
        
        $('#checkoutPaidAmt').val(grand.toFixed(2)).attr('min', grand);
        calculateCashChange();

        $('#checkoutModal').modal('show');
    });

    document.getElementById('checkoutPaidAmt').addEventListener('input', calculateCashChange);

    function calculateCashChange() {
        const grand = $('#posGrandTotalVal').data('val') || 0.00;
        const paid = parseFloat(document.getElementById('checkoutPaidAmt').value) || 0.00;
        const change = Math.max(0.00, paid - grand);
        $('#checkoutChangeVal').text('฿' + change.toLocaleString('en-US', {minimumFractionDigits: 2}));
    }

    // Fast Cash buttons
    $('.fast-cash-btn').on('click', function() {
        const fast = parseFloat($(this).data('val'));
        const grand = $('#posGrandTotalVal').data('val') || 0.00;
        const paid = Math.max(fast, grand);
        $('#checkoutPaidAmt').val(paid.toFixed(2));
        calculateCashChange();
    });

    // Payment method select click listener
    $('.pay-mode-btn').on('click', function() {
        $('.pay-mode-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-primary').removeClass('btn-outline-secondary');
        selectedPaymentMode = $(this).data('mode');

        // Hide/Show correct input containers
        if (selectedPaymentMode === 'Cash') {
            $('#checkoutCashInputs').removeClass('d-none');
            $('#checkoutQrContainer, #checkoutCardInputs').addClass('d-none');
        } else if (selectedPaymentMode === 'PromptPay QR') {
            $('#checkoutQrContainer').removeClass('d-none');
            $('#checkoutCashInputs, #checkoutCardInputs').addClass('d-none');
        } else {
            $('#checkoutCardInputs').removeClass('d-none');
            $('#checkoutCashInputs, #checkoutQrContainer').addClass('d-none');
        }
    });

    // Confirm & Print Receipt checkout submit
    document.getElementById('checkoutConfirmBtn').addEventListener('click', function() {
        const grand = $('#posGrandTotalVal').data('val') || 0.00;
        let paid = grand;
        let refNo = null;

        if (selectedPaymentMode === 'Cash') {
            paid = parseFloat(document.getElementById('checkoutPaidAmt').value) || 0.00;
            if (paid < grand) {
                Swal.fire({ icon: 'warning', title: 'ยอดเงินไม่ถูกต้อง', text: 'จำนวนเงินสดที่รับมาน้อยกว่ายอดชำระจริง.', background: '#ffffff' });
                return;
            }
        } else if (selectedPaymentMode === 'PromptPay QR') {
            refNo = document.getElementById('checkoutQrRef').value;
        } else {
            refNo = document.getElementById('checkoutCardRef').value;
        }

        let subtotal = 0.00;
        cart.forEach(item => subtotal += item.subtotal);
        const discountTotal = redeemPointsDiscount;
        const subAfterDisc = Math.max(0.00, subtotal - discountTotal);
        const taxVal = subAfterDisc * (taxRate / 100);

        const checkoutData = {
            customer_id: selectedCustomer ? selectedCustomer.id : null,
            subtotal: subtotal,
            discount_amount: discountTotal,
            points_redeemed: redeemPointsDiscount > 0 ? redeemPointsDiscount : 0,
            vat_amount: taxVal,
            total_amount: grand,
            paid_amount: paid,
            change_amount: Math.max(0.00, paid - grand),
            payment_method: selectedPaymentMode,
            reference_no: refNo,
            notes: document.getElementById('checkoutNotes').value,
            items: cart.map(item => ({
                product_id: item.product_id,
                selling_price: item.price,
                quantity: item.quantity,
                discount_amount: 0.00,
                subtotal: item.subtotal
            }))
        };

        $('#checkoutConfirmBtn').prop('disabled', true).text('กำลังประมวลผล...');

        fetch('/pos/checkout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(checkoutData)
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            $('#checkoutModal').modal('hide');
            
            Swal.fire({
                icon: 'success',
                title: 'ชำระเงินสำเร็จ!',
                text: 'การทำรายการขายเสร็จสมบูรณ์เรียบร้อยแล้ว.',
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                window.open(`/pos/receipt/${data.sale_id}`, '_blank', 'width=450,height=600');
                location.reload();
            });
        })
        .catch(err => {
            $('#checkoutConfirmBtn').prop('disabled', false).text('ยืนยันชำระเงิน & พิมพ์ใบเสร็จ');
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

    // Hold Sale Action
    document.getElementById('posHoldBtn').addEventListener('click', function() {
        if (cart.length === 0) {
            Swal.fire({ icon: 'warning', title: 'ไม่สามารถพักการขายได้', text: 'ตะกร้าสินค้าว่างเปล่า.', background: '#ffffff' });
            return;
        }

        let subtotal = 0.00;
        cart.forEach(item => subtotal += item.subtotal);

        const holdData = {
            customer_id: selectedCustomer ? selectedCustomer.id : null,
            subtotal: subtotal,
            total_amount: subtotal,
            payment_method: 'Cash',
            items: cart.map(item => ({
                product_id: item.product_id,
                selling_price: item.price,
                quantity: item.quantity,
                subtotal: item.subtotal
            }))
        };

        fetch('/pos/hold', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(holdData)
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'พักบิลเรียบร้อย!',
                text: 'บันทึกการพักบิลขายสำเร็จแล้ว.',
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: err.message, background: '#ffffff' });
        });
    });

    // Resume list Modal open
    document.getElementById('posResumeListBtn').addEventListener('click', function() {
        fetch('/pos/held')
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.length === 0) {
                    html = '<tr><td colspan="4" class="text-center text-secondary py-3">ไม่พบรายการที่พักไว้.</td></tr>';
                } else {
                    data.forEach(h => {
                        html += `
                            <tr class="border-secondary text-light">
                                <td><code>${h.invoice_no}</code></td>
                                <td>${h.customer_name || 'ลูกค้าทั่วไป'}</td>
                                <td class="text-end fw-bold text-success">฿${parseFloat(h.total_amount).toFixed(2)}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-xs rounded-pill px-2 resume-confirm-btn" data-id="${h.id}">ดึงข้อมูล</button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#heldSalesBody').html(html);
                
                // Bind Resume actions
                $('.resume-confirm-btn').on('click', function() {
                    const heldId = $(this).data('id');
                    resumeHeldSale(heldId);
                });

                $('#heldSalesModal').modal('show');
            });
    });

    function resumeHeldSale(heldId) {
        fetch('/pos/resume', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ held_id: heldId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.sale) {
                let stockAdjusted = false;
                let adjustedItems = [];
                
                cart = [];
                data.sale.items.forEach(item => {
                    const prod = productsCatalog.find(p => p.id == item.product_id);
                    const catalogStock = prod ? prod.stock_quantity : 0;
                    let qty = item.quantity;
                    
                    if (qty > catalogStock) {
                        stockAdjusted = true;
                        qty = catalogStock;
                        adjustedItems.push(item.product_name);
                    }
                    
                    if (qty > 0) {
                        cart.push({
                            product_id: item.product_id,
                            name: item.product_name,
                            sku: item.sku,
                            barcode: item.barcode,
                            price: parseFloat(item.selling_price),
                            quantity: qty,
                            subtotal: qty * parseFloat(item.selling_price),
                            catalog_stock: catalogStock
                        });
                    }
                });
                
                if (data.sale.customer_id) {
                    $('#posCustomerSelect').val(data.sale.customer_id).trigger('change');
                }
                
                $('#heldSalesModal').modal('hide');
                renderCart();
                
                if (stockAdjusted) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ปรับปรุงจำนวนสินค้าตามคลังจริง',
                        text: `สินค้าดังต่อไปนี้มีสต็อกคงเหลือไม่เพียงพอ และระบบได้ปรับลดจำนวนหรือนำออกจากรายการแล้ว: ${adjustedItems.join(', ')}`,
                        background: '#ffffff',
                        color: '#0f172a',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            }
        });
    }

    // Check if there is a held_id to resume from URL query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const resumeId = urlParams.get('resume');
    if (resumeId) {
        resumeHeldSale(parseInt(resumeId));
        // Clean query params in browser history
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
