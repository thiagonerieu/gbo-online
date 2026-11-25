<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\EntryModel;
use App\Models\ProjectModel;
use PDOException;

/** Règles métier des projets. */
final class ProjectService
{
    public function __construct(
        private ProjectModel $projects = new ProjectModel(),
        private EntryModel $entries = new EntryModel()
    ) {
    }

    /**
     * Crée un projet ET son entry de départ (reference_number = 1) dans une transaction :
     * soit les deux existent, soit aucun.
     *
     * @return array{ok:bool, project_id:?int, error:?string}
     */
    public function create(int $userId, string $name, string $language): array
    {
        $pdo = Database::pdo();
        try {
            $pdo->beginTransaction();
            $projectId = $this->projects->create($userId, $name, $language);
            $this->entries->createStart($projectId);
            $pdo->commit();
            return ['ok' => true, 'project_id' => $projectId, 'error' => null];
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if (($e->errorInfo[1] ?? 0) === 1062) { // UNIQUE (user_id, name)
                return ['ok' => false, 'project_id' => null, 'error' => 'Vous avez déjà un projet portant ce nom.'];
            }
            throw $e;
        }
    }
}
