<?php
declare(strict_types=1);

namespace App\Core;

use App\Utils\Session;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    /** Valeur POST texte, sans espaces autour. */
    protected function input(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    }

    /** Page réservée aux utilisateurs connectés ; retourne user_id. */
    protected function requireLogin(): int
    {
        $userId = Session::userId();
        if ($userId === null) {
            Session::flash('error', 'Veuillez vous connecter.');
            $this->redirect('/login');
        }
        return $userId;
    }

    /** Page réservée aux visiteurs (login, signup). */
    protected function requireGuest(): void
    {
        if (Session::userId() !== null) {
            $this->redirect('/my-projects');
        }
    }
}
