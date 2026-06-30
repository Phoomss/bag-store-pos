<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Store System' ?> - Bag Store POS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
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
            --accent-hover: #2563eb;
            --border-color: rgba(15, 23, 42, 0.08);
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Layout Structure */
        #wrapper {
            display: flex;
            width: 100%;
        }

        /* Sidebar Navigation */
        #sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background-color: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s ease;
            z-index: 1040;
        }
        #sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        /* Responsive Design Overrides */
        @media (max-width: 767.98px) {
            #sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                height: 100vh;
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show-mobile {
                margin-left: 0 !important;
                box-shadow: 0 0 25px rgba(15, 23, 42, 0.15);
            }
            #topbar {
                padding: 0 15px !important;
            }
            .content-body {
                padding: 15px !important;
            }
        }

        .sidebar-brand {
            padding: 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar-brand i {
            font-size: 24px;
            color: var(--accent-color);
            margin-right: 10px;
        }
        .sidebar-brand span {
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            padding: 20px 10px;
        }
        .nav-header {
            padding: 10px 15px 5px 15px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .nav-item-custom {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-main);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }
        .nav-item-custom:hover {
            background-color: rgba(15, 23, 42, 0.04);
            color: var(--accent-color);
        }
        .nav-item-custom.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .nav-item-custom i {
            width: 24px;
            font-size: 16px;
            margin-right: 10px;
        }

        /* Top Navbar */
        #content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-y: auto;
        }
        #topbar {
            height: 70px;
            background-color: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .toggle-btn {
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 20px;
            cursor: pointer;
        }

        /* Cards & Content */
        .content-body {
            padding: 30px;
            flex-grow: 1;
        }
        .glass-panel {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            margin-bottom: 24px;
        }
        .card-stat {
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .card-stat:hover {
            transform: translateY(-5px);
        }
        .card-stat::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            top: -20px;
            right: -20px;
        }

        /* Form elements overrides for dark mode style */
        .form-select, .form-control {
            background-color: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
        }
        .form-select:focus, .form-control:focus {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
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

        /* DataTables Styling */
        table.dataTable {
            background-color: transparent !important;
            border-color: var(--border-color) !important;
        }
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter, 
        .dataTables_wrapper .dataTables_info, 
        .dataTables_wrapper .dataTables_processing, 
        .dataTables_wrapper .dataTables_paginate {
            color: var(--text-muted) !important;
        }
        .page-link {
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
            color: var(--text-main);
        }
        .page-link:hover {
            background-color: var(--bg-tertiary);
            color: var(--accent-color);
        }
        .page-item.active .page-link {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
    </style>
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-bag-shopping"></i>
                <span>ระบบขายหน้าร้าน POS</span>
            </div>
            
            <div class="sidebar-nav">
                <a href="/" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-line"></i> แผงควบคุม
                </a>
                
                <?php if (\App\Helpers\Session::hasPermission('manage_sales')): ?>
                    <a href="/pos" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/pos' ? 'active' : '' ?>">
                        <i class="fa-solid fa-cash-register"></i> หน้าจอขาย (POS)
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::hasPermission('manage_products')): ?>
                    <div class="nav-header">แคตตาล็อกสินค้า</div>
                    <a href="/products" class="nav-item-custom <?= str_contains($_SERVER['REQUEST_URI'], '/products') ? 'active' : '' ?>">
                        <i class="fa-solid fa-boxes-stacked"></i> รายการสินค้า
                    </a>
                    <a href="/categories" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/categories' ? 'active' : '' ?>">
                        <i class="fa-solid fa-tags"></i> หมวดหมู่สินค้า
                    </a>
                    <a href="/brands" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/brands' ? 'active' : '' ?>">
                        <i class="fa-solid fa-copyright"></i> แบรนด์สินค้า
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::hasPermission('manage_sales') || \App\Helpers\Session::hasPermission('view_reports')): ?>
                    <div class="nav-header">การขายและลูกค้า</div>
                    <?php if (\App\Helpers\Session::hasPermission('manage_sales')): ?>
                        <a href="/sales" class="nav-item-custom <?= str_contains($_SERVER['REQUEST_URI'], '/sales') ? 'active' : '' ?>">
                            <i class="fa-solid fa-file-invoice-dollar"></i> ประวัติใบเสร็จ
                        </a>
                        <a href="/customers" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/customers' ? 'active' : '' ?>">
                            <i class="fa-solid fa-users"></i> ข้อมูลลูกค้า (CRM)
                        </a>
                    <?php endif; ?>
                    <?php if (\App\Helpers\Session::hasPermission('view_reports')): ?>
                        <a href="/reports" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/reports' ? 'active' : '' ?>">
                            <i class="fa-solid fa-chart-pie"></i> รายงานวิเคราะห์
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::hasPermission('manage_purchases')): ?>
                    <div class="nav-header">การจัดซื้อจัดจ้าง</div>
                    <a href="/suppliers" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/suppliers' ? 'active' : '' ?>">
                        <i class="fa-solid fa-truck-field"></i> ผู้จัดจำหน่าย
                    </a>
                    <a href="/purchases" class="nav-item-custom <?= str_contains($_SERVER['REQUEST_URI'], '/purchases') ? 'active' : '' ?>">
                        <i class="fa-solid fa-receipt"></i> รายการสั่งซื้อ (PO)
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::hasPermission('manage_inventory')): ?>
                    <div class="nav-header">คลังสินค้า</div>
                    <a href="/inventory" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/inventory' ? 'active' : '' ?>">
                        <i class="fa-solid fa-warehouse"></i> ปรับปรุงยอดสต็อก
                </a>
                <a href="/inventory/movements" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/inventory/movements' ? 'active' : '' ?>">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i> ประวัติสต็อกเคลื่อนไหว
                </a>
                <a href="/inventory/audit" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/inventory/audit' ? 'active' : '' ?>">
                    <i class="fa-solid fa-clipboard-check"></i> ตรวจสอบสต็อก
                </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::checkRole(['Owner', 'Admin'])): ?>
                    <div class="nav-header">การดำเนินงาน</div>
                    <a href="/expenses" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/expenses' ? 'active' : '' ?>">
                        <i class="fa-solid fa-wallet"></i> บันทึกค่าใช้จ่าย
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::checkRole(['Owner', 'Admin'])): ?>
                    <div class="nav-header">การดูแลระบบ</div>
                    <a href="/users" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/users' ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-shield"></i> จัดการพนักงาน
                    </a>
                    <a href="/users/logs" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/users/logs' ? 'active' : '' ?>">
                        <i class="fa-solid fa-shield-halved"></i> ประวัติกิจกรรมระบบ
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Session::checkRole(['Owner'])): ?>
                    <a href="/settings" class="nav-item-custom <?= $_SERVER['REQUEST_URI'] === '/settings' ? 'active' : '' ?>">
                        <i class="fa-solid fa-sliders"></i> ตั้งค่าร้านค้า
                    </a>
                <?php endif; ?>

                <div class="border-top border-secondary my-3"></div>
                <a href="/logout" class="nav-item-custom text-danger">
                    <i class="fa-solid fa-power-off text-danger"></i> ออกจากระบบ
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content-wrapper">
            <nav id="topbar">
                <button class="toggle-btn" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="d-flex align-items-center">
                    <span class="text-secondary small me-3"><i class="fa-solid fa-circle text-success me-1 small"></i> ออนไลน์</span>
                    <div class="dropdown">
                        <button class="btn btn-dark btn-sm dropdown-toggle rounded-pill px-3 border border-secondary" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-1 text-primary"></i> <?= htmlspecialchars(\App\Helpers\Session::get('user_name', 'Guest')) ?>
                            <?php
                            $role = \App\Helpers\Session::get('user_role', 'Cashier');
                            $thaiRole = $role;
                            if ($role === 'Owner') $thaiRole = 'เจ้าของร้าน';
                            if ($role === 'Admin') $thaiRole = 'ผู้ดูแลระบบ';
                            if ($role === 'Cashier') $thaiRole = 'พนักงานแคชเชียร์';
                            if ($role === 'Warehouse') $thaiRole = 'พนักงานคลังสินค้า';
                            ?>
                            <span class="badge bg-secondary ms-1" style="font-size: 9px;"><?= htmlspecialchars($thaiRole) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border shadow mt-2" aria-labelledby="userMenu" style="background-color: var(--bg-secondary); border-color: var(--border-color) !important;">
                            <li><a class="dropdown-item small" href="/users" style="color: var(--text-main);"><i class="fa-solid fa-lock me-2 text-muted"></i> เปลี่ยนรหัสผ่าน</a></li>
                            <li><hr class="dropdown-divider" style="border-color: var(--border-color);"></li>
                            <li><a class="dropdown-item text-danger small" href="/logout"><i class="fa-solid fa-sign-out-alt me-2 text-danger"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="content-body">
                <?= $viewContent ?>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Set up global CSRF Header for JQuery AJAX requests
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

        // Sidebar collapse logic
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('show-mobile');
                
                // Toggle overlay backdrop
                let backdrop = document.getElementById('sidebar-backdrop');
                if (sidebar.classList.contains('show-mobile')) {
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.id = 'sidebar-backdrop';
                        backdrop.style.position = 'fixed';
                        backdrop.style.top = '0';
                        backdrop.style.left = '0';
                        backdrop.style.width = '100vw';
                        backdrop.style.height = '100vh';
                        backdrop.style.backgroundColor = 'rgba(15, 23, 42, 0.4)';
                        backdrop.style.backdropFilter = 'blur(4px)';
                        backdrop.style.zIndex = '1030';
                        backdrop.addEventListener('click', function() {
                            sidebar.classList.remove('show-mobile');
                            backdrop.remove();
                        });
                        document.body.appendChild(backdrop);
                    }
                } else {
                    if (backdrop) backdrop.remove();
                }
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });

        // Clean up mobile states on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.remove('show-mobile');
                const backdrop = document.getElementById('sidebar-backdrop');
                if (backdrop) backdrop.remove();
            }
        });
    </script>
</body>
</html>
