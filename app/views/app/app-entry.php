<php>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GameBook Organiser - Entrada X</title>
        <link rel="stylesheet" href="../public/assets/styles/app-entry.css">
    </head>

    <body class="page-my-projects">

        <header class="page-header">
            <img src="../public/assets/imgs/logo-gbo.svg" class="page-header-logo" alt="logo GameBook Organiser">
            <span class="project-title">Le Petit Chaperon Rouge</span>
            <h1 class="page-title">Entrada X</h1> <!-- à remplacer par le titre de l'entrée -->
        </header>

        <main class="page-main">

            <div class="model-entry">
                <form action="#" method="post" class="form form-entry">
                    <div class="ref-title-entry">
                        <div class="ref-title-entry-label">
                            <label>Ref. #</label>
                            <label>Titre court</label>]
                        </div>
                        <div class="ref-title-entry-input">
                            <input id="ref-entry" class="form-input form-input-ref-entry" type="text" name="ref">

                            <input id="title-entry" class="form-input form-input-title-entry" type="text" name="title-entry">
                        </div>
                    </div>
                    <div class="note-entry">
                        <label>Note de l'auteur</label>
                        <input id="note-entry" class="form-input form-input-note-entry" type="text" name="note-entry">
                    </div>
                    <div class="text-entry">
                        <label>Texte</label>
                        <textarea id="text-entry" class="form-input form-input-text-entry" name="text-entry"></textarea>
                    </div>
                    <div class="choice-entry">
                        <p>Lex Choix</p>
                        <div class="choice-entry-ligne">
                            <input class="form-input form-input-choice-entry" type="text" name="choice-entry">
                            <button class="btn btn-secondary profile-btn-edit" data-field="firstname" value="A">A</button>
                        </div>
                        <div class="choice-entry-ligne">
                            <input class="form-input form-input-choice-entry" type="text" name="choice-entry">
                            <button class="btn btn-secondary profile-btn-edit" data-field="firstname" value="B">B</button>
                        </div>
                    </div>
                </form>
            </div>

        </main>

        <?php require_once 'views/app-footer.php' ?>

        <script src="../public/assets/scripts/app-entry.js"></script>
    </body>

    </html>
</php>