<?php
declare(strict_types=1);

/**
 * Autoloader maison (équivalent simplifié de PSR-4).
 * App\Models\UserModel  ->  app/models/UserModel.php
 * Les dossiers sont en minuscules ; le nom du fichier = nom exact de la classe.
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return; // classe d'une autre bibliothèque : on ne s'en occupe pas
    }
    $parts = explode('\\', substr($class, strlen($prefix)));   // ['Models', 'UserModel']
    $name  = array_pop($parts);                                 // 'UserModel'
    $dir   = $parts ? strtolower(implode('/', $parts)) . '/' : ''; // 'models/'
    $file  = __DIR__ . '/' . $dir . $name . '.php';
    if (is_file($file)) {
        require $file;
    }
});
