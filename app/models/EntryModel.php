<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Accès à la table gbo_entry (partie 1 : création de l'entry de départ). */
final class EntryModel
{
    /** Entry de départ : reference_number = 1, positionnée en (0, 0). */
    public function createStart(int $projectId): int
    {
        $pdo = Database::pdo();
        $st  = $pdo->prepare(
            'INSERT INTO gbo_entry (project_id, reference_number, pos_x, pos_y)
             VALUES (:project_id, 1, 0, 0)'
        );
        $st->execute(['project_id' => $projectId]);
        return (int) $pdo->lastInsertId();
    }
}
