<?php $title = 'ประวัติกิจกรรมและระบบความปลอดภัย'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 animate-fade-in">
    <div>
        <h4 class="fw-bold m-0"><i class="fa-solid fa-shield-halved text-primary me-2"></i> บันทึกกิจกรรมระบบ & ประวัติความปลอดภัย</h4>
        <span class="text-secondary small">ตรวจสอบบันทึกการทำงานของพนักงาน ประวัติการทำรายการต่างๆ และบันทึกการเข้าใช้ระบบ</span>
    </div>
</div>

<!-- Tabs Panel -->
<div class="glass-panel animate-fade-in" style="animation-delay: 0.1s;">
    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 border-bottom border-light-subtle pb-3" id="logTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link btn btn-sm rounded-pill px-4 me-2 active" id="audit-tab" data-bs-toggle="pill" data-bs-target="#tab-audit" type="button" role="tab" style="font-weight: 600;">
                <i class="fa-solid fa-list-check me-1"></i> กิจกรรมในระบบ (System Activities)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link btn btn-sm rounded-pill px-4" id="login-tab" data-bs-toggle="pill" data-bs-target="#tab-login" type="button" role="tab" style="font-weight: 600;">
                <i class="fa-solid fa-key me-1"></i> ประวัติการเข้าสู่ระบบ (Login History)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="logTabsContent">
        <!-- Audit Activities Tab -->
        <div class="tab-pane fade show active" id="tab-audit" role="tabpanel">
            <div class="table-responsive">
                <table class="table align-middle w-100" id="auditTable" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-light-subtle">
                            <th>วัน-เวลาเกิดกิจกรรม</th>
                            <th>พนักงาน</th>
                            <th>ประเภทการกระทำ</th>
                            <th>รายละเอียดกิจกรรม</th>
                            <th>หมายเลข IP</th>
                            <th>ข้อมูลอุปกรณ์ (User Agent)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($audit_logs as $log): ?>
                            <tr class="border-light-subtle">
                                <td class="text-secondary small"><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?> น.</td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($log['user_name'] ?? 'ระบบอัตโนมัติ') ?></td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info text-dark-override border border-info border-opacity-10 rounded-pill px-2.5 py-1 small">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </td>
                                <td class="text-dark fw-medium"><?= htmlspecialchars($log['description']) ?></td>
                                <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                <td class="text-secondary small text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($log['user_agent']) ?>"><?= htmlspecialchars($log['user_agent']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Login History Tab -->
        <div class="tab-pane fade" id="tab-login" role="tabpanel">
            <div class="table-responsive">
                <table class="table align-middle w-100" id="loginTable" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-light-subtle">
                            <th>วัน-เวลาล็อกอิน</th>
                            <th>อีเมลที่ใช้ล็อกอิน</th>
                            <th>บัญชีพนักงานที่ตรง</th>
                            <th class="text-center">ผลลัพธ์</th>
                            <th>หมายเลข IP</th>
                            <th>ข้อมูลอุปกรณ์ (User Agent)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($login_history as $lh): ?>
                            <tr class="border-light-subtle">
                                <td class="text-secondary small"><?= date('d/m/Y H:i:s', strtotime($lh['login_at'])) ?> น.</td>
                                <td class="text-dark fw-semibold"><?= htmlspecialchars($lh['email']) ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($lh['user_name'] ?? 'ไม่พบข้อมูลบัญชี') ?></td>
                                <td class="text-center">
                                    <?php if ($lh['status'] === 'Success'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-2.5 py-1">สำเร็จ (Success)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill px-2.5 py-1">ล้มเหลว (Failed)</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($lh['ip_address']) ?></code></td>
                                <td class="text-secondary small text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($lh['user_agent']) ?>"><?= htmlspecialchars($lh['user_agent']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#auditTable').DataTable({
        responsive: true,
        pageLength: 15,
        order: [[0, 'desc']], // sort by date desc
        language: {
            emptyTable: "ไม่พบข้อมูลบันทึกกิจกรรม",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            lengthMenu: "แสดง _MENU_ รายการ",
            loadingRecords: "กำลังโหลด...",
            processing: "กำลังประมวลผล...",
            zeroRecords: "ไม่พบข้อมูลบันทึกกิจกรรมที่ตรงกับการค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            },
            search: "ค้นหาด่วน:",
            searchPlaceholder: "ชื่อพนักงาน, กิจกรรม..."
        }
    });

    $('#loginTable').DataTable({
        responsive: true,
        pageLength: 15,
        order: [[0, 'desc']], // sort by date desc
        language: {
            emptyTable: "ไม่พบประวัติการล็อกอิน",
            info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
            infoEmpty: "แสดง 0 ถึง 0 จากทั้งหมด 0 รายการ",
            lengthMenu: "แสดง _MENU_ รายการ",
            loadingRecords: "กำลังโหลด...",
            processing: "กำลังประมวลผล...",
            zeroRecords: "ไม่พบข้อมูลประวัติการล็อกอินที่ตรงกับการค้นหา",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า"
            },
            search: "ค้นหาด่วน:",
            searchPlaceholder: "อีเมล, ผลลัพธ์..."
        }
    });
});
</script>
