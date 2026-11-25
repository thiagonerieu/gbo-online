<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Utils\Session;

final class AuthController extends Controller
{
    public function __construct(private AuthService $auth = new AuthService())
    {
    }

    public function showLogin(): void
    {
        $this->requireGuest();
        $this->view('auth/login', ['page' => 'auth-login', 'title' => 'Connexion'], 'auth');
    }

    public function login(): void
    {
        $this->requireGuest();
        $email  = $this->input('email');
        $result = $this->auth->attempt($email, (string) ($_POST['password'] ?? ''));

        if (!$result['ok']) {
            Session::flash('error', $result['error']);
            Session::flash('old', ['email' => $email]);
            $this->redirect('/login');
        }
        $this->redirect('/my-projects');
    }

    public function showSignup(): void
    {
        $this->requireGuest();
        $this->view('auth/signup', ['page' => 'auth-signup', 'title' => 'Créer un compte'], 'auth');
    }

    public function signup(): void
    {
        $this->requireGuest();
        $in = [
            'first_name'       => $this->input('first_name'),
            'last_name'        => $this->input('last_name'),
            'nickname'         => $this->input('nickname'),
            'email'            => $this->input('email'),
            'password'         => (string) ($_POST['password'] ?? ''),         // jamais « trim » sur un mot de passe
            'password_confirm' => (string) ($_POST['password_confirm'] ?? ''),
        ];
        $result = $this->auth->register($in);

        if (!$result['ok']) {
            Session::flash('errors', $result['errors']);
            Session::flash('old', array_diff_key($in, ['password' => 1, 'password_confirm' => 1]));
            $this->redirect('/signup');
        }
        Session::flash('success', 'Bienvenue ! Votre compte a été créé.');
        $this->redirect('/my-projects');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('/login');
    }

    /** Partie 4 : l'envoi d'e-mail n'est pas encore implémenté. */
    public function showRecovery(): void
    {
        $this->requireGuest();
        $this->view('auth/account-recovery', ['page' => 'auth-account-recovery', 'title' => 'Mot de passe oublié'], 'auth');
    }
}
