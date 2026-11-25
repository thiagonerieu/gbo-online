<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $c = Config::get('db', []);
            if (!empty($c['socket'])) {
                $dsn = sprintf('mysql:unix_socket=%s;dbname=%s;charset=utf8mb4', $c['socket'], $c['name']);
            } else {
                $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $c['host'] ?? '127.0.0.1', (int) ($c['port'] ?? 3306), $c['name']);
            }
            self::$pdo = new PDO($dsn, $c['user'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs SQL = exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,                  // vraies requêtes préparées
            ]);
        }
        return self::$pdo;
    }
}
