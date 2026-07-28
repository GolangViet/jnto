<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Core\Application;

$app = new Application(dirname(__DIR__));
require dirname(__DIR__) . '/routes/web.php';
$app->run();
