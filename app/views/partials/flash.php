<?php
// Messages "flash" (succès / erreur générale). Garde ce bloc dans tes layouts.
$flashError   = \App\Utils\Session::flashGet('error');
$flashSuccess = \App\Utils\Session::flashGet('success');
?>
<?php if ($flashError): ?>
    <div class="alert alert-error" role="alert"><?= e($flashError) ?></div>
<?php endif; ?>
<?php if ($flashSuccess): ?>
    <div class="alert alert-success" role="status"><?= e($flashSuccess) ?></div>
<?php endif; ?>
