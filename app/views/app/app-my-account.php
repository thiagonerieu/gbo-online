<php>
    <!DOCTYPE html>
    <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>GameBook Organiser - Compte</title>
            <link rel="stylesheet" href="../../../public/assets/styles/app-my-account.css">
        </head>
        <body class="page-my-account">

            <header class="page-header">
                <img src="../../../public/assets/imgs/logo-gbo.svg" class="page-header-logo" alt="logo GameBook Organiser">
                <h1 class="page-title">Mon compte</h1>
            </header>

            <main class="page-main">
                
                <div class="profile-identity">
                    <div class="profile-complet-name"><span class="profile-first-name">Prénom</span><span class="profile-last-name">Nom</span></div>
                    <div class="profile-email"><p class="profile-email-display">monadresseemail@email.com</p></div>
                </div>

                <div class="profile-fields">
                    <div class="profile-field-first-name">
                            <label class="profile-field-label">Prénom</label>
                            <span class="profile-field-value">Mon Prénom</span>
                    </div>
                    <button class="btn btn-secondary profile-btn-edit" data-field="firstname" value="Changer">Changer</button>
                    <hr size="0.5px">
                    
                    <div class="profile-field-last-name">
                        <label class="profile-field-label">Nom</label>
                        <span class="profile-field-value">Mon Nom</span>
                    </div>
                    <button class="btn btn-secondary profile-btn-edit" data-field="lastname" value="Changer">Changer</button>
                    <hr size="0.5px">
                    
                    <div class="profile-field-email">
                        <label class="profile-field-label">E-mail</label>
                        <span class="profile-field-value">monadresseemail@email.com</span>
                    </div>
                    <button class="btn btn-secondary profile-btn-edit" data-field="email" value="Changer">Changer</button>
                    <hr size="0.5px">

                    <div class="profile-field-password">
                        <label class="profile-field-label">Mot de passe</label>
                        <span class="profile-field-hint">Modifiez le mot de passe de votre compte.</span>
                    </div>
                    <button class="btn btn-secondary profile-btn-edit" data-field="password" value="Changer">Changer</button>
                    <hr size="0.5px">

                    <div class="profile-danger-zone">
                        <label class="profile-field-label-danger">Supprimer le compte</label>
                        <button class="btn btn-danger" id="btn-delete-account" value="Supprimer">Supprimer</button>
                    </div>
                </div>
            </main>
            
            <?php require_once '../app-footer.php' ?>

            <script src="../../../public/assets/scripts/app-my-account.js"></script>
        </body>
    </html>
</php>