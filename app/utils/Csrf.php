<?php
declare(strict_types=1);

namespace App\Utils;

final class Csrf
{
    public static function token(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return (string) Session::get('csrf_token');
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function verify(mixed $token): bool
    {
        return is_string($token) && $token !== '' && hash_equals(self::token(), $token);
    }
}
