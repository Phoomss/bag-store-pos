<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Register - Bag Store POS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Select2 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #f1f5f9;
            --bg-secondary: #ffffff;
            --bg-tertiary: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent-color: #3b82f6;
            --border-color: rgba(15, 23, 42, 0.08);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        #pos-header {
            height: 60px;
            background-color: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 767.98px) {
            #pos-header {
                height: auto;
                padding: 10px 15px;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            #pos-header > div {
                width: 100%;
                justify-content: space-between;
            }
        }

        .btn-pos-action {
            background-color: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-pos-action:hover {
            background-color: var(--accent-color);
            color: #fff;
        }

        .form-select, .form-control {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
        }
        .form-select:focus, .form-control:focus {
            background-color: var(--bg-secondary);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            color: var(--text-main);
        }

        .modal-content {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            color: var(--text-main);
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color);
        }
    </style>
</head>
<body>
    <div id="pos-header">
        <div class="d-flex align-items-center">
            <a href="/" class="btn-pos-action me-3" style="text-decoration: none;"><i class="fa-solid fa-arrow-left me-1"></i> <span class="d-none d-md-inline">กลับไปหน้าจัดการร้าน</span></a>
            <h5 class="m-0 fw-bold" style="font-size: 1.1rem;"><i class="fa-solid fa-cash-register text-primary me-2"></i> ระบบคิดเงิน</h5>
        </div>
        <div class="d-flex align-items-center">
            <span class="text-secondary small me-3"><i class="fa-solid fa-circle text-success me-1"></i> <span class="d-none d-sm-inline">พนักงานขาย: </span><?= htmlspecialchars(\App\Helpers\Session::get('user_name')) ?></span>
            <div id="clock" class="text-black fw-medium small"></div>
        </div>
    </div>

    <div class="container-fluid py-3">
        <?= $viewContent ?>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': '<?= \App\Helpers\Session::csrfToken() ?>'
            }
        });

        // Global fetch interceptor to automatically add CSRF header for POST/PUT/DELETE requests
        (function() {
            const originalFetch = window.fetch;
            window.fetch = function(url, options) {
                options = options || {};
                const method = (options.method || 'GET').toUpperCase();
                if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
                    options.headers = options.headers || {};
                    if (options.headers instanceof Headers) {
                        if (!options.headers.has('X-CSRF-Token')) {
                            options.headers.set('X-CSRF-Token', '<?= \App\Helpers\Session::csrfToken() ?>');
                        }
                    } else {
                        if (!options.headers['X-CSRF-Token']) {
                            options.headers['X-CSRF-Token'] = '<?= \App\Helpers\Session::csrfToken() ?>';
                        }
                    }
                }
                return originalFetch(url, options);
            };
        })();

        // Register clock updates
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString() + ' | ' + now.toLocaleDateString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
