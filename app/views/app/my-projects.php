<?php
/**
 * Vue : mes projets  (variables disponibles : $projects, $languages)
 * Garde : le formulaire POST /my-projects (champs name, language) avec csrf_field(),
 * et un formulaire POST /my-projects/{id}/delete par projet (avec csrf_field()).
 */
?>
<h1>Mes projets</h1>

<form method="post" action="<?= e(url('/my-projects')) ?>">
    <?= csrf_field() ?>
    <label for="name">Nom du projet</label>
    <input type="text" id="name" name="name" value="<?= e(old('name')) ?>" maxlength="50" required>
    <?= field_error('name') ?>

    <label for="language">Langue</label>
    <select id="language" name="language" required>
        <?php foreach ($languages as $code => $label): ?>
            <option value="<?= e($code) ?>" <?= old('language', 'fr') === $code ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <?= field_error('language') ?>

    <button type="submit">Créer le projet</button>
</form>

<?php if (!$projects): ?>
    <p>Vous n'avez pas encore de projet.</p>
<?php else: ?>
    <ul>
        <?php foreach ($projects as $p): ?>
            <li>
                <a href="<?= e(url('/my-flowchart/' . (int) $p['project_id'])) ?>"><?= e($p['name']) ?></a>
                (<?= e($languages[$p['gbo_language']] ?? $p['gbo_language']) ?>,
                <?= (int) $p['entries'] ?> entrée<?= (int) $p['entries'] > 1 ? 's' : '' ?>,
                créé le <?= e(date('d/m/Y', strtotime($p['created_at']))) ?>)
                <form method="post" action="<?= e(url('/my-projects/' . (int) $p['project_id'] . '/delete')) ?>"
                      onsubmit="return confirm('Supprimer ce projet et toutes ses entrées ?');">
                    <?= csrf_field() ?>
                    <button type="submit">Supprimer</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
