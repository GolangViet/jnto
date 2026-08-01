<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

final class View
{
    /**
     * @var string[]
     */
    private static array $styles = [];

    /**
     * @var string[]
     */
    private static array $scripts = [];

    /**
     * @var string[]
     */
    private static array $modals = [];

    /**
     * Push a stylesheet URL to the queue.
     *
     * @param string $url
     * @return void
     */
    public static function pushStyle(string $url): void
    {
        if (!in_array($url, self::$styles, true)) {
            self::$styles[] = $url;
        }
    }

    /**
     * Push a script URL to the queue.
     *
     * @param string $url
     * @return void
     */
    public static function pushScript(string $url): void
    {
        if (!in_array($url, self::$scripts, true)) {
            self::$scripts[] = $url;
        }
    }

    /**
     * Push a modal template to the queue.
     *
     * @param string $modal
     * @return void
     */
    public static function pushModal(string $modal): void
    {
        if (!in_array($modal, self::$modals, true)) {
            self::$modals[] = $modal;
        }
    }

    /**
     * Render all queued stylesheet link tags.
     *
     * @return string
     */
    public static function renderStyles(): string
    {
        $html = '';
        foreach (self::$styles as $url) {
            $html .= '    <link rel="stylesheet" href="' . e($url) . '">' . "\n";
        }

        return $html;
    }

    /**
     * Render all queued script tags.
     *
     * @return string
     */
    public static function renderScripts(): string
    {
        $html = '';
        foreach (self::$scripts as $url) {
            $html .= '    <script type="text/javascript" src="' . e($url) . '"></script>' . "\n";
        }

        return $html;
    }

    /**
     * Render all queued modal templates.
     *
     * @return string
     */
    public static function renderModals(): string
    {
        $html = '';
        foreach (self::$modals as $modal) {
            $html .= $modal . "\n";
        }

        return $html;
    }

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
