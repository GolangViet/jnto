<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

final class View
{
    /**
     * Render a view with the given data and optional layout.
     *
     * @param string $view
     * @param array $data
     * @param string|bool $layout
     * @return string
     */
    public static function render(string $view, array $data = [], string|bool $layout = true): string
    {
        $viewFile = app()->basePath('app/Views/' . $view . '.php');
        if (!is_file($viewFile)) {
            throw new RuntimeException("View {$view} not found.");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        if (!$layout) {
            return $content;
        }

        if ($layout === true) {
            $layoutName = str_starts_with($view, 'admin/') ? 'admin' : 'user';
        } else {
            $layoutName = $layout;
        }

        $layoutFile = app()->basePath('app/Views/layouts/' . $layoutName . '.php');
        if (!is_file($layoutFile)) {
            throw new RuntimeException("Layout {$layoutName} not found.");
        }

        ob_start();
        require $layoutFile;
        return (string) ob_get_clean();
    }
}
