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
        $version = self::getGitCommitHash();
        foreach (self::$styles as $url) {
            $parsedUrl = $url;
            if ($version !== '' && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://') && !str_starts_with($url, '//')) {
                if (str_contains($url, '?')) {
                    $parsedUrl .= '&v=' . $version;
                } else {
                    $parsedUrl .= '?v=' . $version;
                }
            }
            $html .= '    <link rel="stylesheet" href="' . e($parsedUrl) . '">' . "\n";
        }

        return $html;
    }

    /**
     * Get the last git commit hash (short version).
     *
     * @return string
     */
    private static function getGitCommitHash(): string
    {
        static $hash = null;
        if ($hash !== null) {
            return $hash;
        }

        $hash = '';
        $gitDir = app()->basePath('.git');
        if (is_dir($gitDir)) {
            $headFile = $gitDir . '/HEAD';
            if (is_file($headFile)) {
                $headContent = trim((string) file_get_contents($headFile));
                if (str_starts_with($headContent, 'ref:')) {
                    $refPath = trim(substr($headContent, 4));
                    $refFile = $gitDir . '/' . $refPath;
                    if (is_file($refFile)) {
                        $hash = substr(trim((string) file_get_contents($refFile)), 0, 7);
                    } else {
                        $packedRefsFile = $gitDir . '/packed-refs';
                        if (is_file($packedRefsFile)) {
                            $packedContent = file_get_contents($packedRefsFile);
                            if ($packedContent !== false) {
                                if (preg_match('/^([a-f0-9]{40})\s+' . preg_quote($refPath, '/') . '$/m', $packedContent, $matches)) {
                                    $hash = substr($matches[1], 0, 7);
                                }
                            }
                        }
                    }
                } else {
                    $hash = substr($headContent, 0, 7);
                }
            }
        }

        return $hash;
    }

    /**
     * Render all queued script tags.
     *
     * @return string
     */
    public static function renderScripts(): string
    {
        $html = '';
        $version = self::getGitCommitHash();
        foreach (self::$scripts as $url) {
            $parsedUrl = $url;
            if ($version !== '' && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://') && !str_starts_with($url, '//')) {
                if (str_contains($url, '?')) {
                    $parsedUrl .= '&v=' . $version;
                } else {
                    $parsedUrl .= '?v=' . $version;
                }
            }
            $html .= '    <script type="text/javascript" src="' . e($parsedUrl) . '"></script>' . "\n";
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
