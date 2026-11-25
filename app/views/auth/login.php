<?php
/**
 * Vue : connexion  (remplace le HTML par le tien, mais garde :)
 *   - <form method="post" action="<?= url('/login') ?>">  et  <?= csrf_field() ?>
 *   - les noms de champs : email, password
 *   - old('email') pour pré-remplir l'e-mail après une erreur
 */
?>
<h1>Connexion</h1>
<form method="post" action="<?= e(url('/login')) ?>">
    <?= csrf_field() ?>
    <label for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" value="<?= e(old('email')) ?>" maxlength="100" autocomplete="email" required>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" autocomplete="current-password" required>

    <button type="submit">Se connecter</button>
</form>
<p>
    <a href="<?= e(url('/account-recovery')) ?>">Mot de passe oublié ?</a> ·
    <a href="<?= e(url('/signup')) ?>">Créer un compte</a>
</p>
