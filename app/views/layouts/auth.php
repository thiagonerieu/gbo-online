<?php
/**
 * Layout des pages publiques (login, signup, récupération, erreurs).
 * Remplace le <head>/<body> ci-dessous par ton HTML ; garde :
 *   - <?= $content ?>            (le contenu de la vue)
 *   - le partial flash           (messages)
 *   - page_style() / page_script() (CSS/JS propres à la page)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'GameBook Organiser') ?></title>
    <?= style_tag('styles/variables.css') ?>
    <?= style_tag('styles/style.css') ?>
    <?= page_style($page ?? '') ?>
</head>
<body class="layout-auth">
<main>
    <?php require BASE_PATH . '/app/views/partials/flash.php'; ?>
    <?= $content ?>
</main>
<?= page_script($page ?? '') ?>
</body>
</html>
