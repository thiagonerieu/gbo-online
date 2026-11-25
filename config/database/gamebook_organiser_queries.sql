-- =====================================================================
-- GameBook Organiser - requetes avec jointures (par niveau de difficulte) v1.3
-- A executer apres gamebook_organiser.sql (donnees de test chargees).
-- Compatible MySQL 8.x (CTE recursives : MySQL 8.0+).
--
-- NB : dans gbo_choice (noms generes par Looping) : entry_id_1 = entry d'ORIGINE (obligatoire),
--      entry_id = entry de DESTINATION (nullable = choix en attente).
-- Les variables @... remplacent ici les parametres PDO (:user_id, etc.).
-- En PHP, utiliser des requetes preparees avec des parametres nommes.
-- =====================================================================

USE gamebook_organiser;

SET @user_id    = 1;   -- Alice
SET @project_id = 1;   -- La Foret des Murmures
SET @entry_id   = 5;   -- entry 'Le gardien' (reference_number 5)
SET @intruder   = 2;   -- Bob (utilisateur qui ne possede PAS le projet 1)

-- =====================================================================
-- NIVEAU 1 - INNER JOIN simple (2 tables)
-- =====================================================================

-- 1.1 Les projets d'un auteur (gbo_user -> gbo_project)
SELECT u.nickname, p.project_id, p.name, p.gbo_language
FROM gbo_user u
INNER JOIN gbo_project p ON p.user_id = u.user_id
WHERE u.user_id = @user_id
ORDER BY p.name;

-- 1.2 Les entries d'un projet, dans l'ordre de lecture (gbo_project -> gbo_entry)
SELECT p.name AS project, e.reference_number, e.title
FROM gbo_project p
INNER JOIN gbo_entry e ON e.project_id = p.project_id
WHERE p.project_id = @project_id
ORDER BY e.reference_number;

-- =====================================================================
-- NIVEAU 2 - Jointures multiples (3 a 5 tables)
-- =====================================================================

-- 2.1 Les choix de chaque entry (gbo_entry -> gbo_choice)
SELECT e.reference_number, c.slot, c.label
FROM gbo_entry e
INNER JOIN gbo_choice c ON c.entry_id_1 = e.entry_id
WHERE e.project_id = @project_id
ORDER BY e.reference_number, c.slot;

-- 2.2 Les personnages presents dans chaque entry
--     (gbo_entry -> gbo_entry_character -> gbo_story_character : table associative N-N)
SELECT e.reference_number, e.title, sc.name AS character_name
FROM gbo_entry e
INNER JOIN gbo_entry_character ec ON ec.entry_id = e.entry_id
INNER JOIN gbo_story_character sc ON sc.story_character_id = ec.story_character_id
WHERE e.project_id = @project_id
ORDER BY e.reference_number, sc.name;

-- 2.3 La chaine complete d'un auteur (5 tables)
--     gbo_user -> gbo_project -> gbo_entry -> gbo_entry_character -> gbo_story_character
SELECT u.nickname, p.name AS project, e.reference_number, sc.name AS character_name
FROM gbo_user u
INNER JOIN gbo_project p ON p.user_id = u.user_id
INNER JOIN gbo_entry e ON e.project_id = p.project_id
INNER JOIN gbo_entry_character ec ON ec.entry_id = e.entry_id
INNER JOIN gbo_story_character sc ON sc.story_character_id = ec.story_character_id
WHERE u.user_id = @user_id
ORDER BY p.name, e.reference_number, sc.name;

-- =====================================================================
-- NIVEAU 3 - Auto-jointure (le graphe : gbo_entry -> gbo_choice -> gbo_entry)
-- =====================================================================

-- 3.1 Toutes les aretes du graphe, avec numeros et titres d'origine/destination.
--     La table entry est utilisee DEUX fois (o = origine, t = destination).
--     LEFT JOIN sur la destination : les choix "en attente" restent visibles
--     (to_ref = NULL).
SELECT o.reference_number AS from_ref, o.title AS from_title,
       c.slot, c.label,
       t.reference_number AS to_ref,   t.title AS to_title
FROM gbo_entry o
INNER JOIN gbo_choice c ON c.entry_id_1 = o.entry_id
LEFT JOIN gbo_entry t ON t.entry_id = c.entry_id
WHERE o.project_id = @project_id
ORDER BY o.reference_number, c.slot;

-- 3.2 Les noeuds pour vis.js (position enregistree dans la base)
SELECT e.entry_id AS id, e.reference_number AS label, e.title, e.pos_x AS x, e.pos_y AS y
FROM gbo_entry e
WHERE e.project_id = @project_id
ORDER BY e.reference_number;

-- 3.3 Les aretes pour vis.js (seulement les choix dont la destination existe).
--     La jointure avec entry filtre par projet.
SELECT c.choice_id AS id, c.entry_id_1 AS `from`, c.entry_id AS `to`, c.label
FROM gbo_choice c
INNER JOIN gbo_entry e ON e.entry_id = c.entry_id_1
WHERE e.project_id = @project_id
  AND c.entry_id IS NOT NULL
ORDER BY c.choice_id;

-- 3.4 Qui mene a une entry donnee ? (utile avant de la supprimer)
SELECT o.reference_number AS from_ref, o.title AS from_title, c.label
FROM gbo_choice c
INNER JOIN gbo_entry o ON o.entry_id = c.entry_id_1
WHERE c.entry_id = @entry_id
ORDER BY o.reference_number;

-- =====================================================================
-- NIVEAU 4 - LEFT JOIN et anti-jointures (IS NULL)
-- =====================================================================

-- 4.1 Les fins : entries sans aucun choix.
--     (L'entry 9 'Brouillon' apparait aussi : sans choix, elle est une "fin" au sens du modele.)
SELECT e.reference_number, e.title
FROM gbo_entry e
LEFT JOIN gbo_choice c ON c.entry_id_1 = e.entry_id
WHERE e.project_id = @project_id
  AND c.choice_id IS NULL
ORDER BY e.reference_number;

-- 4.2 Les entries jamais ciblees directement (hors entry de depart).
SELECT e.reference_number, e.title
FROM gbo_entry e
LEFT JOIN gbo_choice c ON c.entry_id = e.entry_id
WHERE e.project_id = @project_id
  AND e.reference_number <> 1
  AND c.choice_id IS NULL
ORDER BY e.reference_number;

-- 4.3 Les choix "en attente" (destination pas encore creee).
SELECT o.reference_number AS from_ref, c.slot, c.label
FROM gbo_choice c
INNER JOIN gbo_entry o ON o.entry_id = c.entry_id_1
WHERE o.project_id = @project_id
  AND c.entry_id IS NULL
ORDER BY o.reference_number, c.slot;

-- 4.4 Les entries sans aucun personnage.
SELECT e.reference_number, e.title
FROM gbo_entry e
LEFT JOIN gbo_entry_character ec ON ec.entry_id = e.entry_id
WHERE e.project_id = @project_id
  AND ec.entry_id IS NULL
ORDER BY e.reference_number;

-- 4.5 Les personnages jamais utilises (resultat vide avec les donnees de test).
SELECT sc.name
FROM gbo_story_character sc
LEFT JOIN gbo_entry_character ec ON ec.story_character_id = sc.story_character_id
WHERE sc.project_id = @project_id
  AND ec.entry_id IS NULL;

-- =====================================================================
-- NIVEAU 5 - Jointures et agregats (GROUP BY, HAVING)
-- =====================================================================

-- 5.1 Nombre d'entries par projet (LEFT JOIN : un projet vide apparait avec 0).
SELECT u.nickname, p.name AS project, COUNT(e.entry_id) AS entries
FROM gbo_user u
INNER JOIN gbo_project p ON p.user_id = u.user_id
LEFT JOIN gbo_entry e ON e.project_id = p.project_id
GROUP BY u.user_id, u.nickname, p.project_id, p.name
ORDER BY u.nickname, p.name;

-- 5.2 Nombre de choix par entry (0 = fin).
SELECT e.reference_number, e.title, COUNT(c.choice_id) AS nb_choices
FROM gbo_entry e
LEFT JOIN gbo_choice c ON c.entry_id_1 = e.entry_id
WHERE e.project_id = @project_id
GROUP BY e.entry_id, e.reference_number, e.title
ORDER BY e.reference_number;

-- 5.3 Les entries qui n'ont qu'un seul choix (HAVING sur l'agregat).
SELECT e.reference_number, e.title
FROM gbo_entry e
INNER JOIN gbo_choice c ON c.entry_id_1 = e.entry_id
WHERE e.project_id = @project_id
GROUP BY e.entry_id, e.reference_number, e.title
HAVING COUNT(c.choice_id) = 1
ORDER BY e.reference_number;

-- 5.4 Les personnages classes par nombre d'apparitions.
SELECT sc.name, COUNT(ec.entry_id) AS appearances
FROM gbo_story_character sc
LEFT JOIN gbo_entry_character ec ON ec.story_character_id = sc.story_character_id
WHERE sc.project_id = @project_id
GROUP BY sc.story_character_id, sc.name
ORDER BY appearances DESC, sc.name;

-- 5.5 Tableau de bord d'un projet : entries, choix, choix en attente.
SELECT p.name AS project,
       COUNT(DISTINCT e.entry_id) AS entries,
       COUNT(DISTINCT c.choice_id) AS choices,
       COUNT(DISTINCT CASE WHEN c.entry_id IS NULL THEN c.choice_id END) AS pending_choices
FROM gbo_project p
LEFT JOIN gbo_entry e ON e.project_id = p.project_id
LEFT JOIN gbo_choice c ON c.entry_id_1 = e.entry_id
WHERE p.project_id = @project_id
GROUP BY p.project_id, p.name;

-- 5.6 Vue administrateur : projets et entries par utilisateur.
SELECT u.nickname, u.role,
       COUNT(DISTINCT p.project_id) AS projects,
       COUNT(DISTINCT e.entry_id) AS entries
FROM gbo_user u
LEFT JOIN gbo_project p ON p.user_id = u.user_id
LEFT JOIN gbo_entry e ON e.project_id = p.project_id
GROUP BY u.user_id, u.nickname, u.role
ORDER BY u.nickname;

-- =====================================================================
-- NIVEAU 6 - Securite et integrite par jointure
-- =====================================================================

-- 6.1 Controle d'acces : lire une entry SEULEMENT si elle appartient a l'utilisateur.
--     Proprietaire (Alice) : 1 ligne.
SELECT e.entry_id, e.reference_number, e.title
FROM gbo_entry e
INNER JOIN gbo_project p ON p.project_id = e.project_id
WHERE e.entry_id = @entry_id
  AND p.user_id = @user_id;

--     Intrus (Bob) avec le meme identifiant : 0 ligne (acces refuse).
SELECT e.entry_id, e.reference_number, e.title
FROM gbo_entry e
INNER JOIN gbo_project p ON p.project_id = e.project_id
WHERE e.entry_id = @entry_id
  AND p.user_id = @intruder;

-- 6.2 Avant d'inserer un choix : origine et destination sont-elles dans le meme projet ?
--     Resultat attendu : same_project = 1 (valide) ; 0 = refuser l'insertion.
SET @origin_id = 4;  -- projet 1
SET @target_ok = 7;  -- projet 1
SET @target_ko = 12; -- projet 2

SELECT COUNT(*) AS same_project
FROM gbo_entry o
INNER JOIN gbo_entry t ON t.project_id = o.project_id
WHERE o.entry_id = @origin_id AND t.entry_id = @target_ok;

SELECT COUNT(*) AS same_project
FROM gbo_entry o
INNER JOIN gbo_entry t ON t.project_id = o.project_id
WHERE o.entry_id = @origin_id AND t.entry_id = @target_ko;

-- 6.3 Audit : detection des violations (les deux requetes doivent etre vides).
SELECT c.choice_id
FROM gbo_choice c
INNER JOIN gbo_entry o ON o.entry_id = c.entry_id_1
INNER JOIN gbo_entry t ON t.entry_id = c.entry_id
WHERE o.project_id <> t.project_id;

SELECT ec.entry_id, ec.story_character_id
FROM gbo_entry_character ec
INNER JOIN gbo_entry e ON e.entry_id = ec.entry_id
INNER JOIN gbo_story_character sc ON sc.story_character_id = ec.story_character_id
WHERE e.project_id <> sc.project_id;

-- =====================================================================
-- NIVEAU 7 - Avance : CTE recursive (MySQL 8) sur le graphe
-- =====================================================================

-- 7.1 Entries inaccessibles depuis l'entry de depart, meme indirectement.
--     UNION (sans ALL) elimine les doublons et evite les boucles infinies.
WITH RECURSIVE reachable (entry_id) AS (
  SELECT e.entry_id
  FROM gbo_entry e
  WHERE e.project_id = @project_id AND e.reference_number = 1
  UNION
  SELECT c.entry_id
  FROM reachable r
  INNER JOIN gbo_choice c ON c.entry_id_1 = r.entry_id
  WHERE c.entry_id IS NOT NULL
)
SELECT e.reference_number, e.title
FROM gbo_entry e
LEFT JOIN reachable r ON r.entry_id = e.entry_id
WHERE e.project_id = @project_id
  AND r.entry_id IS NULL
ORDER BY e.reference_number;

-- 7.2 Distance minimale (en choix) depuis l'entry de depart.
--     La limite de profondeur protege contre les boucles.
WITH RECURSIVE walk (entry_id, depth) AS (
  SELECT e.entry_id, 0
  FROM gbo_entry e
  WHERE e.project_id = @project_id AND e.reference_number = 1
  UNION ALL
  SELECT c.entry_id, w.depth + 1
  FROM walk w
  INNER JOIN gbo_choice c ON c.entry_id_1 = w.entry_id
  WHERE c.entry_id IS NOT NULL AND w.depth < 50
)
SELECT e.reference_number, e.title, MIN(w.depth) AS min_steps
FROM walk w
INNER JOIN gbo_entry e ON e.entry_id = w.entry_id
GROUP BY e.entry_id, e.reference_number, e.title
ORDER BY min_steps, e.reference_number;
