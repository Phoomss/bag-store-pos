<?php $title = 'ตั้งค่าร้านค้า'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-sliders text-primary me-2"></i> ตั้งค่าร้านค้าและระบบ</h4>
    <span class="text-secondary small">แก้ไขการกำหนดค่าต่างๆ ทั่วทั้งระบบ</span>
</div>

<form id="settingsForm">
    <div class="row">
        <!-- Left: Store Information -->
        <div class="col-lg-6 mb-4">
            <div class="glass-panel h-100">
                <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-shop me-2"></i> ข้อมูลโปรไฟล์ร้านค้า</h5>
                
                <div class="mb-3">
                    <label for="store_name" class="form-label">ชื่อร้าน / ชื่อสาขา *</label>
                    <input type="text" class="form-control" id="store_name" name="store_name" value="<?= htmlspecialchars($settings['store_name'] ?? '') ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="store_email" class="form-label">อีเมลติดต่อร้านค้า</label>
                        <input type="email" class="form-control" id="store_email" name="store_email" value="<?= htmlspecialchars($settings['store_email'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label for="store_phone" class="form-label">เบอร์โทรศัพท์ติดต่อ</label>
                        <input type="text" class="form-control" id="store_phone" name="store_phone" value="<?= htmlspecialchars($settings['store_phone'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="store_address" class="form-label">ที่อยู่ร้านค้า (ที่ตั้งสาขา)</label>
                    <textarea class="form-control" id="store_address" name="store_address" rows="3"><?= htmlspecialchars($settings['store_address'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="receipt_footer" class="form-label">ข้อความส่วนท้ายใบเสร็จรับเงิน</label>
                    <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="3"><?= htmlspecialchars($settings['receipt_footer'] ?? '') ?></textarea>
                    <p class="text-secondary small mt-1">ข้อความนี้จะถูกแสดงและพิมพ์ที่ด้านล่างสุดของใบเสร็จรับเงินความร้อน (80 มม.)</p>
                </div>
            </div>
        </div>

        <!-- Right: Tax, Currency & SMTP -->
        <div class="col-lg-6 mb-4">
            <div class="glass-panel mb-4">
                <h5 class="fw-bold mb-4 text-warning"><i class="fa-solid fa-coins me-2"></i> ข้อมูลภาษีและสกุลเงิน</h5>
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="tax_rate" class="form-label">อัตราภาษีมูลค่าเพิ่ม (%) *</label>
                        <input type="number" step="0.1" class="form-control" id="tax_rate" name="tax_rate" value="<?= htmlspecialchars($settings['tax_rate'] ?? '7.0') ?>" required>
                    </div>
                    <div class="col-4">
                        <label for="currency" class="form-label">รหัสสกุลเงิน *</label>
                        <input type="text" class="form-control text-uppercase" id="currency" name="currency" value="<?= htmlspecialchars($settings['currency'] ?? 'THB') ?>" required placeholder="เช่น THB, USD">
                    </div>
                    <div class="col-4">
                        <label for="currency_symbol" class="form-label">สัญลักษณ์สกุลเงิน *</label>
                        <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?= htmlspecialchars($settings['currency_symbol'] ?? '฿') ?>" required>
                    </div>
                </div>
            </div>

            <div class="glass-panel">
                <h5 class="fw-bold mb-4 text-info"><i class="fa-solid fa-envelope-open-text me-2"></i> ตั้งค่าการส่งอีเมล (SMTP Server)</h5>
                <div class="row mb-3">
                    <div class="col-8">
                        <label for="smtp_host" class="form-label">โฮสต์เซิร์ฟเวอร์ SMTP (Host)</label>
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" placeholder="smtp.mailtrap.io">
                    </div>
                    <div class="col-4">
                        <label for="smtp_port" class="form-label">พอร์ต (Port)</label>
                        <input type="text" class="form-control" id="smtp_port" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" placeholder="587">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="smtp_user" class="form-label">ชื่อผู้ใช้ SMTP (Username)</label>
                        <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <label for="smtp_pass" class="form-label">รหัสผ่าน SMTP (Password)</label>
                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '') ?>" placeholder="••••••••">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="smtp_encryption" class="form-label">การเข้ารหัสความปลอดภัย (Encryption)</label>
                    <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                        <option value="tls" <?= (($settings['smtp_encryption'] ?? '') === 'tls') ? 'selected' : '' ?>>TLS (แนะนำ)</option>
                        <option value="ssl" <?= (($settings['smtp_encryption'] ?? '') === 'ssl') ? 'selected' : '' ?>>SSL</option>
                        <option value="none" <?= (($settings['smtp_encryption'] ?? '') === 'none') ? 'selected' : '' ?>>ไม่มี</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <hr class="border-secondary my-4">

    <div class="d-flex justify-content-end gap-2 mb-5">
        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold" id="saveSettingsBtn">บันทึกตั้งค่าระบบ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const data = {};
        const formData = new FormData(form);
        formData.forEach((val, key) => { data[key] = val; });

        $('#saveSettingsBtn').prop('disabled', true).text('กำลังบันทึก...');

        fetch('/settings/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.message); });
            return res.json();
        })
        .then(data => {
            $('#saveSettingsBtn').prop('disabled', false).text('บันทึกตั้งค่าระบบ');
            
            Swal.fire({
                icon: 'success',
                title: 'บันทึกสำเร็จ!',
                text: 'ปรับปรุงการกำหนดค่าระบบร้านค้าเรียบร้อยแล้ว.',
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            $('#saveSettingsBtn').prop('disabled', false).text('บันทึกตั้งค่าระบบ');
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: err.message,
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6'
            });
        });
    });
});
</script>
