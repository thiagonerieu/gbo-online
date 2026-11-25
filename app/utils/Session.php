<?php
declare(strict_types=1);

namespace App\Utils;

use App\Core\Config;

final class Session
{
    private const IDLE_TIMEOUT = 7200; // 2 h d'inactivité

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name((string) Config::get('session.name', 'gbo_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        if (isset($_SESSION['_last_activity']) && time() - $_SESSION['_last_activity'] > self::IDLE_TIMEOUT) {
            self::destroy();
            session_start();
        }
        $_SESSION['_last_activity'] = time();

        // Messages "flash" : écrits à la requête N, lisibles à la requête N+1.
        $_SESSION['_flash']      = $_SESSION['_flash_next'] ?? [];
        $_SESSION['_flash_next'] = [];
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true); // contre la fixation de session
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $p['path'],
                'domain'   => $p['domain'],
                'secure'   => $p['secure'],
                'httponly' => $p['httponly'],
                'samesite' => $p['samesite'] ?? 'Lax',
            ]);
        }
        session_destroy();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash_next'][$key] = $value;
    }

    public static function flashGet(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash'][$key] ?? $default;
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}
