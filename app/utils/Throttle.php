<?php
declare(strict_types=1);

namespace App\Utils;

/**
 * Limiteur de tentatives (anti force brute) basé sur de petits fichiers JSON
 * dans storage/throttle/ : fonctionne même si l'attaquant supprime ses cookies.
 */
final class Throttle
{
    public static function isLocked(string $key, int $limit, int $window): bool
    {
        $d = self::read($key);
        return $d !== null && (time() - $d['first']) < $window && $d['count'] >= $limit;
    }

    public static function hit(string $key, int $window): void
    {
        $d = self::read($key);
        if ($d === null || (time() - $d['first']) >= $window) {
            $d = ['count' => 0, 'first' => time()];
        }
        $d['count']++;
        file_put_contents(self::file($key), json_encode($d), LOCK_EX);
    }

    public static function clear(string $key): void
    {
        $file = self::file($key);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    private static function read(string $key): ?array
    {
        $file = self::file($key);
        if (!is_file($file)) {
            return null;
        }
        $d = json_decode((string) file_get_contents($file), true);
        return is_array($d) && isset($d['count'], $d['first']) ? $d : null;
    }

    private static function file(string $key): string
    {
        $dir = BASE_PATH . '/storage/throttle';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir . '/' . hash('sha256', $key) . '.json';
    }
}
