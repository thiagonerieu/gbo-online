<?php
/**
 * Layout de l'espace connecté (my-projects, my-flowchart, entry, my-account).
 * Remplace par ton HTML (header/footer) ; garde <?= $content ?>, le partial flash,
 * le bouton de déconnexion (formulaire POST + csrf_field) et page_style()/page_script().
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
<body class="layout-app">
<header>
    <nav>
        <a href="<?= e(url('/my-projects')) ?>">Mes projets</a>
        <span><?= e(\App\Utils\Session::get('nickname', '')) ?></span>
        <form method="post" action="<?= e(url('/logout')) ?>">
            <?= csrf_field() ?>
            <button type="submit">Se déconnecter</button>
        </form>
    </nav>
</header>
<main>
    <?php require BASE_PATH . '/app/views/partials/flash.php'; ?>
    <?= $content ?>
</main>
<?= page_script($page ?? '') ?>
</body>
</html>
