<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    /**
     * Rend app/views/<view>.php à l'intérieur de app/views/layouts/<layout>.php.
     * Dans la vue, les clés de $data deviennent des variables ($projects, $page...).
     */
    public static function render(string $view, array $data = [], ?string $layout = 'app', int $status = 200): void
    {
        http_response_code($status);
        $content = self::capture(self::path($view), $data);
        if ($layout === null) {
            echo $content;
            return;
        }
        echo self::capture(self::path('layouts/' . $layout), $data + ['content' => $content]);
    }

    private static function path(string $view): string
    {
        $file = BASE_PATH . '/app/views/' . $view . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Vue introuvable : ' . $view);
        }
        return $file;
    }

    private static function capture(string $__file, array $__data): string
    {
        extract($__data, EXTR_SKIP);
        ob_start();
        try {
            require $__file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        return (string) ob_get_clean();
    }
}
