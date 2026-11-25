<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ProjectController;
use App\Core\Router;
use App\Utils\Session;

$router = new Router();

// Accueil : redirige selon l'état de connexion
$router->get('/', function (): void {
    redirect(Session::userId() !== null ? '/my-projects' : '/login');
});

// Authentification
$router->get('/login',            [AuthController::class, 'showLogin']);
$router->post('/login',           [AuthController::class, 'login']);
$router->get('/signup',           [AuthController::class, 'showSignup']);
$router->post('/signup',          [AuthController::class, 'signup']);
$router->post('/logout',          [AuthController::class, 'logout']);
$router->get('/account-recovery', [AuthController::class, 'showRecovery']);

// Projets
$router->get('/my-projects',                       [ProjectController::class, 'index']);
$router->post('/my-projects',                      [ProjectController::class, 'store']);
$router->post('/my-projects/{project_id}/delete',  [ProjectController::class, 'destroy']);

return $router;
