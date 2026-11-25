<?php
declare(strict_types=1);

use App\Core\Config;
use App\Utils\Csrf;
use App\Utils\Session;

/** Échappe une valeur avant de l'afficher en HTML (protection XSS). */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    return rtrim((string) Config::get('app.base_url', ''), '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path), true, 303);
    exit;
}

/** URL d'un fichier de public/assets, avec numéro de version (cache). */
function asset(string $path): string
{
    $file    = BASE_PATH . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? '?v=' . filemtime($file) : '';
    return url('/assets/' . ltrim($path, '/')) . $version;
}

function asset_exists(string $path): bool
{
    return is_file(BASE_PATH . '/public/assets/' . ltrim($path, '/'));
}

function style_tag(string $path): string
{
    return asset_exists($path) ? '<link rel="stylesheet" href="' . e(asset($path)) . '">' . "\n" : '';
}

function script_tag(string $path): string
{
    return asset_exists($path) ? '<script src="' . e(asset($path)) . '" defer></script>' . "\n" : '';
}

/** CSS propre à une page : styles/<page>.css (ignoré s'il n'existe pas). */
function page_style(string $page): string
{
    return preg_match('/^[a-z0-9-]+$/', $page) ? style_tag('styles/' . $page . '.css') : '';
}

/** JS propre à une page : scripts/<page>.js (ignoré s'il n'existe pas). */
function page_script(string $page): string
{
    return preg_match('/^[a-z0-9-]+$/', $page) ? script_tag('scripts/' . $page . '.js') : '';
}

function csrf_field(): string
{
    return Csrf::field();
}

function csrf_token(): string
{
    return Csrf::token();
}

/** Valeur saisie avant une erreur de formulaire (jamais le mot de passe). */
function old(string $key, string $default = ''): string
{
    $old = Session::flashGet('old', []);
    return is_array($old) && isset($old[$key]) ? (string) $old[$key] : $default;
}

/** Message d'erreur d'un champ, ou chaîne vide. */
function field_error(string $field): string
{
    $errors = Session::flashGet('errors', []);
    if (is_array($errors) && isset($errors[$field])) {
        return '<p class="field-error" role="alert">' . e($errors[$field]) . '</p>';
    }
    return '';
}
