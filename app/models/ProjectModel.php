<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Accès à la table gbo_project. Chaque requête est filtrée par propriétaire (user_id). */
final class ProjectModel
{
    /** Projets d'un auteur avec leur nombre d'entries (LEFT JOIN : un projet vide affiche 0). */
    public function listByUser(int $userId): array
    {
        $st = Database::pdo()->prepare(
            'SELECT p.project_id, p.name, p.gbo_language, p.created_at, p.updated_at,
                    COUNT(e.entry_id) AS entries
             FROM gbo_project p
             LEFT JOIN gbo_entry e ON e.project_id = p.project_id
             WHERE p.user_id = :user_id
             GROUP BY p.project_id, p.name, p.gbo_language, p.created_at, p.updated_at
             ORDER BY p.updated_at DESC, p.project_id DESC'
        );
        $st->execute(['user_id' => $userId]);
        return $st->fetchAll();
    }

    /** Contrôle d'accès : retourne le projet seulement s'il appartient à l'utilisateur. */
    public function findForUser(int $projectId, int $userId): ?array
    {
        $st = Database::pdo()->prepare(
            'SELECT project_id, user_id, name, gbo_language, created_at, updated_at
             FROM gbo_project
             WHERE project_id = :project_id AND user_id = :user_id
             LIMIT 1'
        );
        $st->execute(['project_id' => $projectId, 'user_id' => $userId]);
        return $st->fetch() ?: null;
    }

    /** @throws \PDOException code 1062 si le nom existe déjà pour cet auteur */
    public function create(int $userId, string $name, string $language): int
    {
        $pdo = Database::pdo();
        $st  = $pdo->prepare(
            'INSERT INTO gbo_project (user_id, name, gbo_language) VALUES (:user_id, :name, :language)'
        );
        $st->execute(['user_id' => $userId, 'name' => $name, 'language' => $language]);
        return (int) $pdo->lastInsertId();
    }

    /** Supprime un projet (les entries, choices et personnages partent en cascade). */
    public function delete(int $projectId, int $userId): bool
    {
        $st = Database::pdo()->prepare(
            'DELETE FROM gbo_project WHERE project_id = :project_id AND user_id = :user_id'
        );
        $st->execute(['project_id' => $projectId, 'user_id' => $userId]);
        return $st->rowCount() === 1;
    }
}
