<div class="login-logo">
    <i class="fa-solid fa-bag-shopping"></i>
    <h1>ระบบขายหน้าร้าน POS</h1>
    <p class="text-secondary small">ระบบจัดการสต็อกสินค้าและคิดเงินหน้าร้านกระเป๋า</p>
</div>

<?php if ($error = \App\Helpers\Session::flash('error')): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4 small">
        <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form id="loginForm" method="POST" action="/login">
    <input type="hidden" name="csrf_token" value="<?= \App\Helpers\Session::csrfToken() ?>">
    
    <div class="mb-3">
        <label for="email" class="form-label">ที่อยู่อีเมล</label>
        <div class="input-group">
            <span class="input-group-text bg-dark bg-opacity-20 border-end-0 text-secondary" style="border-radius: 12px 0 0 12px; border: 1px solid rgba(255,255,255,0.1);"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" class="form-control border-start-0" id="email" name="email" placeholder="กรอกอีเมลผู้ใช้..." required style="border-radius: 0 12px 12px 0;">
        </div>
    </div>
    
    <div class="mb-4">
        <label for="password" class="form-label">รหัสผ่าน</label>
        <div class="input-group">
            <span class="input-group-text bg-dark bg-opacity-20 border-end-0 text-secondary" style="border-radius: 12px 0 0 12px; border: 1px solid rgba(255,255,255,0.1);"><i class="fa-solid fa-lock"></i></span>
            <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="••••••••" required style="border-radius: 0 12px 12px 0;">
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary-gradient w-100 mb-3">
        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="loginSpinner"></span>
        <i class="fa-solid fa-right-to-bracket me-2" id="loginIcon"></i> เข้าสู่ระบบ
    </button>
</form>

<div class="text-center mt-3 small text-secondary">
    <p class="mb-1">บัญชีทดลองเข้าใช้ระบบ:</p>
    <div style="font-size: 11px;">
        เจ้าของร้าน: <code>owner@bagpos.com</code> / <code>password</code><br>
        พนักงานแคชเชียร์: <code>cashier@bagpos.com</code> / <code>password</code>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const spinner = document.getElementById('loginSpinner');
    const icon = document.getElementById('loginIcon');
    const btn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        btn.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => { data[key] = value; });

        fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        })
        .catch(err => {
            btn.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');

            Swal.fire({
                icon: 'error',
                title: 'เข้าสู่ระบบล้มเหลว',
                text: err.message,
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'border border-secondary rounded-4'
                }
            });
        });
    });
});
</script>
