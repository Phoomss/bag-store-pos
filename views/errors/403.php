<?php $title = '403 - Forbidden'; ?>
<div class="error-code">403</div>
<h2 class="h4 mb-3">Access Denied</h2>
<p class="text-secondary mb-4"><?= htmlspecialchars($message ?? "You don't have permission to access this page or perform this action.") ?></p>
<a href="/" class="btn btn-home">Go Back Home</a>
