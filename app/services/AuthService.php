<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\UserModel;
use App\Utils\Session;
use App\Utils\Throttle;
use App\Utils\Validator;
use PDOException;

/** Règles métier d'authentification : inscription, connexion, déconnexion. */
final class AuthService
{
    // Hash factice : on le vérifie même si l'e-mail n'existe pas, pour que le temps de
    // réponse ne révèle pas quels e-mails sont inscrits.
    private const DUMMY_HASH = '$2y$10$3cHQblX0C8fSpCLmJAdYJOM9b6ha/t/Kg6VTuNwtG3diOcczsHKLq';

    private const LOGIN_WINDOW = 900; // 15 min

    public function __construct(private UserModel $users = new UserModel())
    {
    }

    /** @return array{ok:bool, errors:array<string,string>} */
    public function register(array $in): array
    {
        $password = (string) ($in['password'] ?? '');

        $v = new Validator($in);
        $v->required('first_name', 'Le prénom')->length('first_name', 'Le prénom', 1, 50)
           ->required('last_name', 'Le nom')->length('last_name', 'Le nom', 1, 50)
           ->required('nickname', 'Le pseudo')->length('nickname', 'Le pseudo', 3, 50)
           ->matches('nickname', '/^[A-Za-z0-9_.-]+$/', 'Le pseudo ne peut contenir que des lettres, chiffres, points, tirets et underscores.')
           ->required('email', "L'adresse e-mail")->email('email', "L'adresse e-mail")->length('email', "L'adresse e-mail", 5, 100)
           ->required('password', 'Le mot de passe')->length('password', 'Le mot de passe', 10, 72)
           ->same('password_confirm', 'password', 'Les deux mots de passe ne correspondent pas.');
        $errors = $v->errors();

        if (strlen($password) > 72) { // limite de bcrypt : 72 octets
            $errors['password'] ??= 'Le mot de passe est trop long (72 octets maximum).';
        }
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        if (!$errors && Throttle::isLocked("signup-ip:$ip", 10, 3600)) {
            $errors['email'] = 'Trop d\'inscriptions depuis cette adresse. Réessayez plus tard.';
        }
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        try {
            $userId = $this->users->create(
                (string) $in['first_name'],
                (string) $in['last_name'],
                (string) $in['nickname'],
                mb_strtolower((string) $in['email']),
                password_hash($password, PASSWORD_DEFAULT)
            );
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? 0) === 1062) { // clé unique violée (e-mail ou pseudo)
                return ['ok' => false, 'errors' => ['email' => 'Cet e-mail ou ce pseudo est déjà utilisé.']];
            }
            throw $e;
        }
        Throttle::hit("signup-ip:$ip", 3600);
        $this->startSession($this->users->findById($userId));
        return ['ok' => true, 'errors' => []];
    }

    /** @return array{ok:bool, error:?string} */
    public function attempt(string $email, string $password): array
    {
        $email  = mb_strtolower(trim($email));
        $ip     = $_SERVER['REMOTE_ADDR'] ?? 'cli';
        $pair   = "login:$ip:$email";
        $ipOnly = "login-ip:$ip";

        if (Throttle::isLocked($pair, 5, self::LOGIN_WINDOW) || Throttle::isLocked($ipOnly, 20, self::LOGIN_WINDOW)) {
            return ['ok' => false, 'error' => 'Trop de tentatives. Réessayez dans quelques minutes.'];
        }

        $user  = $this->users->findByEmail($email);
        $hash  = $user['password_hash'] ?? self::DUMMY_HASH;
        $valid = password_verify($password, $hash) && $user !== null;

        if (!$valid) {
            Throttle::hit($pair, self::LOGIN_WINDOW);
            Throttle::hit($ipOnly, self::LOGIN_WINDOW);
            return ['ok' => false, 'error' => 'E-mail ou mot de passe incorrect.']; // message générique
        }

        Throttle::clear($pair);
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            $this->users->updatePasswordHash((int) $user['user_id'], password_hash($password, PASSWORD_DEFAULT));
        }
        $this->startSession($user);
        return ['ok' => true, 'error' => null];
    }

    public function logout(): void
    {
        Session::destroy();
        Session::start(); // nouvelle session vierge pour afficher le message
        Session::flash('success', 'Vous êtes déconnecté.');
    }

    private function startSession(array $user): void
    {
        Session::regenerate();          // nouvel identifiant de session après connexion
        Session::forget('csrf_token');  // et nouveau jeton CSRF
        Session::set('user_id', (int) $user['user_id']);
        Session::set('nickname', (string) $user['nickname']);
        Session::set('role', (string) $user['role']);
    }
}
