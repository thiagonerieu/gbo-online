<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Accès à la table gbo_user (SQL uniquement, requêtes préparées). */
final class UserModel
{
    public function findByEmail(string $email): ?array
    {
        $st = Database::pdo()->prepare(
            'SELECT user_id, first_name, last_name, nickname, email, password_hash, role
             FROM gbo_user
             WHERE email = :email
             LIMIT 1'
        );
        $st->execute(['email' => $email]);
        return $st->fetch() ?: null;
    }

    public function findById(int $userId): ?array
    {
        $st = Database::pdo()->prepare(
            'SELECT user_id, first_name, last_name, nickname, email, role, created_at
             FROM gbo_user
             WHERE user_id = :user_id
             LIMIT 1'
        );
        $st->execute(['user_id' => $userId]);
        return $st->fetch() ?: null;
    }

    /** @throws \PDOException code 1062 si e-mail ou pseudo déjà pris */
    public function create(string $firstName, string $lastName, string $nickname, string $email, string $passwordHash): int
    {
        $pdo = Database::pdo();
        $st  = $pdo->prepare(
            'INSERT INTO gbo_user (first_name, last_name, nickname, email, password_hash)
             VALUES (:first_name, :last_name, :nickname, :email, :password_hash)'
        );
        $st->execute([
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'nickname'      => $nickname,
            'email'         => $email,
            'password_hash' => $passwordHash,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public function updatePasswordHash(int $userId, string $hash): void
    {
        $st = Database::pdo()->prepare('UPDATE gbo_user SET password_hash = :hash WHERE user_id = :user_id');
        $st->execute(['hash' => $hash, 'user_id' => $userId]);
    }
}
