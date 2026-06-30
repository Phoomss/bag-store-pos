<?php $title = 'Activity & Audit Logs'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="fa-solid fa-shield-halved text-primary me-2"></i> Security & Activity Audit Logs</h4>
</div>

<!-- Tabs Panel -->
<div class="glass-panel">
    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 border-bottom border-secondary pb-3" id="logTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="btn btn-dark active btn-sm rounded-pill px-4 me-2" id="audit-tab" data-bs-toggle="pill" data-bs-target="#tab-audit" type="button" role="tab">
                <i class="fa-solid fa-list-check me-1"></i> System Activities
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="btn btn-dark btn-sm rounded-pill px-4" id="login-tab" data-bs-toggle="pill" data-bs-target="#tab-login" type="button" role="tab">
                <i class="fa-solid fa-key me-1"></i> Login History
            </button>
        </li>
    </ul>

    <div class="tab-content" id="logTabsContent">
        <!-- Audit Activities Tab -->
        <div class="tab-pane fade show active" id="tab-audit" role="tabpanel">
            <div class="table-responsive">
                <table class="table align-middle text-light w-100" id="auditTable" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>Timestamp</th>
                            <th>Staff Member</th>
                            <th>Action Type</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($audit_logs as $log): ?>
                            <tr class="border-secondary">
                                <td><?= date('d M Y H:i:s', strtotime($log['created_at'])) ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($log['action']) ?></span></td>
                                <td><?= htmlspecialchars($log['description']) ?></td>
                                <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                <td class="text-secondary small" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($log['user_agent']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Login History Tab -->
        <div class="tab-pane fade" id="tab-login" role="tabpanel">
            <div class="table-responsive">
                <table class="table align-middle text-light w-100" id="loginTable" style="font-size: 13px;">
                    <thead>
                        <tr class="text-secondary small border-secondary">
                            <th>Timestamp</th>
                            <th>Email Input</th>
                            <th>Assigned User</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($login_history as $lh): ?>
                            <tr class="border-secondary">
                                <td><?= date('d M Y H:i:s', strtotime($lh['login_at'])) ?></td>
                                <td><?= htmlspecialchars($lh['email']) ?></td>
                                <td><?= htmlspecialchars($lh['user_name'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if ($lh['status'] === 'Success'): ?>
                                        <span class="badge bg-success">Success</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= htmlspecialchars($lh['ip_address']) ?></code></td>
                                <td class="text-secondary small" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($lh['user_agent']) ?></td>
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
            search: "_INPUT_",
            searchPlaceholder: "Search activities..."
        }
    });

    $('#loginTable').DataTable({
        responsive: true,
        pageLength: 15,
        order: [[0, 'desc']], // sort by date desc
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search logins..."
        }
    });
});
</script>
