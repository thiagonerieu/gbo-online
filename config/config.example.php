<?php
declare(strict_types=1);

/**
 * Copiez ce fichier vers config/config.php (ignoré par Git) et remplissez vos valeurs.
 * Ne mettez JAMAIS de vrais identifiants dans config.example.php.
 */
return [
    'app' => [
        'name'     => 'GameBook Organiser',
        'debug'    => false,            // true seulement en local
        'base_url' => '',               // '' si le site est à la racine (gbo-online.test)
        'timezone' => 'Europe/Paris',
    ],
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'gamebook_organiser',
        'user'     => 'gbo_app',        // utilisateur dédié (voir database/create_app_user.sql)
        'password' => 'CHANGE_ME',
        // 'socket' => '/var/run/mysqld/mysqld.sock',  // optionnel
    ],
    'session' => [
        'name' => 'gbo_session',
    ],
    // Langues proposées pour un projet (code ISO 639-1 => libellé)
    'languages' => [
        'fr' => 'Français',
        'en' => 'English',
        'pt' => 'Português',
        'es' => 'Español',
    ],
];
