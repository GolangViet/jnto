<?php

declare(strict_types=1);

namespace Core;

abstract class Controller
{
    /**
     * Render a view with the given data.
     *
     * @param string $view
     * @param array $data
     * @param string|bool $layout
     * @return string
     */
    protected function view(string $view, array $data = [], string|bool $layout = true): string
    {
        return View::render($view, $data, $layout);
    }

    /**
     * Redirect to a given path.
     *
     * @param string $path
     * @return never
     */
    protected function redirect(string $path): never
    {
        app()->response()->redirect($path);
    }
}
