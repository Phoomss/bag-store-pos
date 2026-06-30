<?php $title = '404 - Not Found'; ?>
<div class="error-code">404</div>
<h2 class="h4 mb-3">Page Not Found</h2>
<p class="text-secondary mb-4"><?= htmlspecialchars($message ?? 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.') ?></p>
<a href="/" class="btn btn-home">Go Back Home</a>
