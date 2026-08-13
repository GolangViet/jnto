<?php

declare(strict_types=1);

// IMPORTANT:
// Do this BEFORE parse_url(), because parse_url('//take-survey')
// interprets "take-survey" as a hostname.
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestUri = '/' . ltrim($requestUri, '/');
$path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$query = parse_url($requestUri, PHP_URL_QUERY);
if ($path !== '/') {
    $path = rtrim($path, '/');
}

$_SERVER['REQUEST_URI'] = $path . ($query !== null && $query !== '' ? '?' . $query : '');

require dirname(__DIR__) . '/vendor/autoload.php';

use Core\Application;

$app = new Application(dirname(__DIR__));
require dirname(__DIR__) . '/routes/web.php';
$app->run();
