<?php
declare(strict_types=1);

namespace App\Core;

use App\Utils\Csrf;
use Closure;

final class Router
{
    /** @var array<int, array{method:string, regex:string, handler:Closure|array}> */
    private array $routes = [];

    public function get(string $path, Closure|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, Closure|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /** {project_id} -> paramètre numérique */
    private function add(string $method, string $path, Closure|array $handler): void
    {
        $regex = preg_replace('#\{([a-z_]+)\}#', '(?P<$1>[0-9]+)', $path);
        $this->routes[] = ['method' => $method, 'regex' => '#^' . $regex . '$#', 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $path    = '/' . trim(rawurldecode($uri), '/');
        $allowed = [];

        foreach ($this->routes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }
            if ($route['method'] !== $method) {
                $allowed[] = $route['method'];
                continue;
            }
            // Protection CSRF centralisée : tout POST doit porter le jeton de session.
            if ($method === 'POST' && !Csrf::verify($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null))) {
                View::render('errors/419', [], 'auth', 419);
                return;
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $this->call($route['handler'], $params);
            return;
        }

        if ($allowed) {
            http_response_code(405);
            header('Allow: ' . implode(', ', array_unique($allowed)));
            echo 'Méthode non autorisée.';
            return;
        }
        View::render('errors/404', [], 'auth', 404);
    }

    private function call(Closure|array $handler, array $params): void
    {
        if ($handler instanceof Closure) {
            $handler($params);
            return;
        }
        [$class, $action] = $handler;
        (new $class())->$action($params);
    }
}
