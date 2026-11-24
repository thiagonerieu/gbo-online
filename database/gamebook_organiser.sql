-- =====================================================================
-- GameBook Organiser - script de creation de la base de donnees
-- Compatible MySQL 8.0.16+ (le CHECK n'est applique qu'a partir de cette version)
-- Contenu : 1) schema  2) donnees de test  3) triggers (optionnels)
-- =====================================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS gamebook_organiser
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gamebook_organiser;

-- ---------------------------------------------------------------------
-- 0) Nettoyage (ordre inverse des dependances)
-- ---------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TRIGGER IF EXISTS trg_entry_before_update;
DROP TABLE IF EXISTS entry_character;
DROP TABLE IF EXISTS choice;
DROP TABLE IF EXISTS story_character;
DROP TABLE IF EXISTS entry;
DROP TABLE IF EXISTS project;
DROP TABLE IF EXISTS user;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1) Schema
-- ---------------------------------------------------------------------

CREATE TABLE user (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name    VARCHAR(50)  NOT NULL,
  last_name     VARCHAR(50)  NOT NULL,
  nickname      VARCHAR(50)  NOT NULL,
  email         VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('author', 'admin') NOT NULL DEFAULT 'author',
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT pk_user PRIMARY KEY (id),
  CONSTRAINT uq_user_nickname UNIQUE (nickname),
  CONSTRAINT uq_user_email UNIQUE (email)
) ENGINE = InnoDB;

CREATE TABLE project (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT UNSIGNED NOT NULL,
  name       VARCHAR(50)  NOT NULL,
  language   CHAR(2)      NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT pk_project PRIMARY KEY (id),
  CONSTRAINT uq_project_user_name UNIQUE (user_id, name),
  CONSTRAINT fk_project_user FOREIGN KEY (user_id)
    REFERENCES user (id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE entry (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id       INT UNSIGNED NOT NULL,
  reference_number INT UNSIGNED NOT NULL,
  title            VARCHAR(100) NULL,
  content          TEXT         NULL,
  author_notes     TEXT         NULL,
  pos_x            DOUBLE       NOT NULL DEFAULT 0,
  pos_y            DOUBLE       NOT NULL DEFAULT 0,
  created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT pk_entry PRIMARY KEY (id),
  CONSTRAINT uq_entry_project_reference UNIQUE (project_id, reference_number),
  CONSTRAINT fk_entry_project FOREIGN KEY (project_id)
    REFERENCES project (id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- target_entry_id nullable : choix "en attente" (destination pas encore creee)
CREATE TABLE choice (
  id              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  entry_id        INT UNSIGNED     NOT NULL,
  target_entry_id INT UNSIGNED     NULL,
  slot            TINYINT UNSIGNED NOT NULL,
  label           VARCHAR(500)     NOT NULL,
  created_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT pk_choice PRIMARY KEY (id),
  CONSTRAINT uq_choice_entry_slot UNIQUE (entry_id, slot),
  CONSTRAINT chk_choice_slot CHECK (slot IN (1, 2)),
  CONSTRAINT fk_choice_entry FOREIGN KEY (entry_id)
    REFERENCES entry (id) ON DELETE CASCADE,
  CONSTRAINT fk_choice_target_entry FOREIGN KEY (target_entry_id)
    REFERENCES entry (id) ON DELETE SET NULL
) ENGINE = InnoDB;

-- "character" est un mot reserve MySQL : la table s'appelle story_character
CREATE TABLE story_character (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id INT UNSIGNED NOT NULL,
  name       VARCHAR(50)  NOT NULL,
  CONSTRAINT pk_story_character PRIMARY KEY (id),
  CONSTRAINT uq_story_character_project_name UNIQUE (project_id, name),
  CONSTRAINT fk_story_character_project FOREIGN KEY (project_id)
    REFERENCES project (id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE entry_character (
  entry_id           INT UNSIGNED NOT NULL,
  story_character_id INT UNSIGNED NOT NULL,
  CONSTRAINT pk_entry_character PRIMARY KEY (entry_id, story_character_id),
  CONSTRAINT fk_entry_character_entry FOREIGN KEY (entry_id)
    REFERENCES entry (id) ON DELETE CASCADE,
  CONSTRAINT fk_entry_character_character FOREIGN KEY (story_character_id)
    REFERENCES story_character (id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- ---------------------------------------------------------------------
-- 2) Donnees de test
-- Mots de passe de test : Alice#2026 / Bob#2026 / Admin#2026
-- (hash bcrypt compatibles avec password_verify() de PHP)
-- ---------------------------------------------------------------------

INSERT INTO user (first_name, last_name, nickname, email, password_hash, role) VALUES
  ('Alice', 'Martin', 'alice_m', 'alice@example.com',
   '$2y$10$OcdfXWVMhGAK.Q79Akz8buDVVrqfO2CXCJ6u.XS9jn6ghC5KVqOFW', 'author'),
  ('Bob',   'Durand', 'bob_d',   'bob@example.com',
   '$2y$10$fn2SI0kCLDBm0Zars/r4Ju6AUqJo1V3MkeIYVoxYS3wSxCvgpnaDW', 'author'),
  ('Ada',   'Admin',  'admin',   'admin@example.com',
   '$2y$10$BzTGttU9e1bzOYcKW0EESe2X2L06FiXUFYMhNb6qXx2BYOEusryRm', 'admin');
-- user ids : 1 = Alice, 2 = Bob, 3 = admin

INSERT INTO project (user_id, name, language) VALUES
  (1, 'La Foret des Murmures', 'fr'),
  (1, 'O Labirinto de Vidro',  'pt'),
  (2, 'The Clockwork Keep',    'en');
-- project ids : 1, 2 (Alice), 3 (Bob)

-- Projet 1 : entry ids 1 a 9 (reference_number 1 a 9)
-- L'entry 1 est l'entry de depart, positionnee en (0, 0).
INSERT INTO entry (project_id, reference_number, title, content, author_notes, pos_x, pos_y) VALUES
  (1, 1, 'Le sentier',
   'Vous arrivez a la lisiere de la foret. Un sentier part a gauche, un autre a droite.',
   'Entry de depart.', 0, 0),
  (1, 2, 'La clairiere',
   'Une clairiere baignee de brume. Deux silhouettes vous observent.',
   NULL, -200, 150),
  (1, 3, 'Le pont de corde',
   'Un vieux pont de corde enjambe un ravin.',
   NULL, 200, 150),
  (1, 4, 'La grotte',
   'Une grotte sombre. Une faible lueur brille au fond.',
   'Le choix 2 est a ecrire.', -300, 300),
  (1, 5, 'Le gardien',
   'Un gardien vous barre la route et pose une enigme.',
   NULL, 0, 300),
  (1, 6, 'La chute',
   'Le pont cede. Votre aventure s''acheve ici.',
   'Fin : mort.', 300, 300),
  (1, 7, 'La sortie',
   'Vous retrouvez la lumiere du jour. Victoire !',
   'Fin : victoire.', -150, 450),
  (1, 8, 'Le tresor',
   'Le gardien s''efface et revele un coffre ancien.',
   'Fin : victoire alternative.', 150, 450),
  (1, 9, 'Brouillon',
   'Idee pour plus tard : une scene au bord de la riviere.',
   'Aucune entry n''y mene encore.', 400, 100);

-- Projet 2 : entry ids 10 a 12
INSERT INTO entry (project_id, reference_number, title, content, pos_x, pos_y) VALUES
  (2, 1, 'A entrada',    'Voce esta diante de um labirinto de vidro.',      0, 0),
  (2, 2, 'O espelho',    'Seu reflexo sorri de volta.',                  -150, 150),
  (2, 3, 'A saida',      'Uma porta de luz se abre. Fim da aventura.',    150, 150);

-- Projet 3 : entry ids 13 et 14
INSERT INTO entry (project_id, reference_number, title, content, pos_x, pos_y) VALUES
  (3, 1, 'The gate',     'The clockwork gate creaks open.',                  0, 0),
  (3, 2, 'The courtyard','Gears turn beneath your feet.',                  0, 150);

-- Choix du projet 1 (entry_id -> target_entry_id ; ids = reference_number)
INSERT INTO choice (entry_id, target_entry_id, slot, label) VALUES
  (1, 2,    1, 'Prendre le sentier de gauche'),
  (1, 3,    2, 'Prendre le sentier de droite'),
  (2, 4,    1, 'Entrer dans la grotte'),
  (2, 5,    2, 'Parler au gardien'),
  (3, 5,    1, 'Revenir sur vos pas'),
  (3, 6,    2, 'Traverser le pont'),
  (4, 7,    1, 'Suivre le courant d''air vers la sortie'),
  (4, NULL, 2, 'Suivre la lueur (destination a ecrire)'),
  (5, 8,    1, 'Repondre a l''enigme');
-- entries 6, 7, 8 : aucun choix = fins ; entry 9 : jamais ciblee (inaccessible)

-- Choix du projet 2
INSERT INTO choice (entry_id, target_entry_id, slot, label) VALUES
  (10, 11, 1, 'Toucher o espelho'),
  (10, 12, 2, 'Seguir em frente');

-- Choix du projet 3
INSERT INTO choice (entry_id, target_entry_id, slot, label) VALUES
  (13, 14, 1, 'Enter the courtyard');

INSERT INTO story_character (project_id, name) VALUES
  (1, 'Mira'),
  (1, 'Le Gardien'),
  (2, 'Teseu'),
  (3, 'Cogsworth');
-- story_character ids : 1 = Mira, 2 = Le Gardien, 3 = Teseu, 4 = Cogsworth

INSERT INTO entry_character (entry_id, story_character_id) VALUES
  (1, 1), (2, 1), (3, 1), (5, 1),
  (4, 2), (5, 2), (6, 2),
  (10, 3), (11, 3),
  (13, 4), (14, 4);

-- ---------------------------------------------------------------------
-- 3) Triggers (OPTIONNELS) : renforcent dans la base des regles de gestion
--    - entry.id est immuable
--    - reference_number = 1 (entry de depart) est immuable
-- A executer avec le delimiteur DELIMITER (client mysql, HeidiSQL, DBeaver...).
-- Si votre outil gere mal DELIMITER, ignorez cette section : les memes
-- regles sont aussi verifiees dans le code PHP.
-- ---------------------------------------------------------------------

DELIMITER //

CREATE TRIGGER trg_entry_before_update
BEFORE UPDATE ON entry
FOR EACH ROW
BEGIN
  IF NEW.id <> OLD.id THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'entry.id is immutable';
  END IF;
  IF OLD.reference_number = 1 AND NEW.reference_number <> 1 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'reference_number 1 is immutable';
  END IF;
END//

DELIMITER ;