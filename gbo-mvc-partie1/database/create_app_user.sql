-- Utilisateur MySQL dédié à l'application (privilèges minimaux, pas de root).
-- 1) Remplacez le mot de passe. 2) Exécutez avec un compte administrateur (root).
-- 3) Reportez user et password dans config/config.php.

CREATE USER IF NOT EXISTS 'gbo_app'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';

GRANT SELECT, INSERT, UPDATE, DELETE ON gamebook_organiser.* TO 'gbo_app'@'localhost';

FLUSH PRIVILEGES;
