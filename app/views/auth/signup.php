<?php
/**
 * Vue : inscription  (remplace le HTML par le tien, mais garde :)
 *   - <form method="post" action="<?= url('/signup') ?>">  et  <?= csrf_field() ?>
 *   - les noms de champs : first_name, last_name, nickname, email, password, password_confirm
 *   - old('...') et field_error('...') pour réafficher valeurs et erreurs
 */
?>
<h1>Créer un compte</h1>
<form method="post" action="<?= e(url('/signup')) ?>">
    <?= csrf_field() ?>

    <label for="first_name">Prénom</label>
    <input type="text" id="first_name" name="first_name" value="<?= e(old('first_name')) ?>" maxlength="50" autocomplete="given-name" required>
    <?= field_error('first_name') ?>

    <label for="last_name">Nom</label>
    <input type="text" id="last_name" name="last_name" value="<?= e(old('last_name')) ?>" maxlength="50" autocomplete="family-name" required>
    <?= field_error('last_name') ?>

    <label for="nickname">Pseudo</label>
    <input type="text" id="nickname" name="nickname" value="<?= e(old('nickname')) ?>" minlength="3" maxlength="50" autocomplete="username" required>
    <?= field_error('nickname') ?>

    <label for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" value="<?= e(old('email')) ?>" maxlength="100" autocomplete="email" required>
    <?= field_error('email') ?>

    <label for="password">Mot de passe (10 caractères minimum)</label>
    <input type="password" id="password" name="password" minlength="10" maxlength="72" autocomplete="new-password" required>
    <?= field_error('password') ?>

    <label for="password_confirm">Confirmer le mot de passe</label>
    <input type="password" id="password_confirm" name="password_confirm" minlength="10" maxlength="72" autocomplete="new-password" required>
    <?= field_error('password_confirm') ?>

    <button type="submit">Créer mon compte</button>
</form>
<p><a href="<?= e(url('/login')) ?>">J'ai déjà un compte</a></p>
