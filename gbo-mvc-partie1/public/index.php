<?php
declare(strict_types=1);

// Serveur intégré de PHP (php -S) : laisser passer les fichiers statiques existants.
if (PHP_SAPI === 'cli-server') {
    $requested = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($requested !== '/' && is_file(__DIR__ . $requested)) {
        return false;
    }
}

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/autoload.php';
require BASE_PATH . '/app/utils/helpers.php';

use App\Core\Config;
use App\Core\View;
use App\Utils\Session;

$configFile = BASE_PATH . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(503);
    echo 'Configuration manquante : copiez config/config.example.php vers config/config.php.';
    exit;
}
Config::load(require $configFile);

date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Paris'));
$debug = (bool) Config::get('app.debug', false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');

// Toute erreur non gérée : journal serveur + page générique (jamais de détails en production).
set_exception_handler(function (Throwable $e) use ($debug): void {
    error_log((string) $e);
    try {
        View::render('errors/500', ['details' => $debug ? (string) $e : ''], 'auth', 500);
    } catch (Throwable) {
        http_response_code(500);
        echo 'Erreur interne.';
    }
});

// En-têtes de sécurité de base
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

Session::start();

$router = require BASE_PATH . '/app/routes.php';
$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
);
