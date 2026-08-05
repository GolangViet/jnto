<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Core\Application;
use Core\View;

$app = new Application(dirname(__DIR__));

if (session_status() !== PHP_SESSION_ACTIVE) {
    $_SESSION = [];
}

try {
    $html = View::render('home/index', ['posts' => []]);
    
    echo "=== RENDERED HTML OUTPUT ===\n";
    echo $html;
    echo "============================\n\n";

    $hasFormCss = (bool) preg_match('#<link rel="stylesheet" href="/assets/css/user/form\.css\?v=[a-f0-9]{7}">#', $html, $matchesCss, PREG_OFFSET_CAPTURE);
    $hasHomeJs = (bool) preg_match('#<script type="text/javascript" src="/assets/js/home\.js\?v=[a-f0-9]{7}"></script>#', $html, $matchesJs, PREG_OFFSET_CAPTURE);
    
    $headClosePos = strpos($html, '</head>');
    $formCssPos = $hasFormCss ? $matchesCss[0][1] : -1;
    $bodyClosePos = strpos($html, '</body>');
    $homeJsPos = $hasHomeJs ? $matchesJs[0][1] : -1;


    echo "Validation Results:\n";
    echo "Pushed form.css stylesheet (with version hash) detected: " . ($hasFormCss ? "YES" : "NO") . "\n";
    echo "Pushed home.js script (with version hash) detected: " . ($hasHomeJs ? "YES" : "NO") . "\n";

    if ($hasFormCss && $hasHomeJs) {
        if ($formCssPos < $headClosePos) {
            echo "CSS position relative to </head>: CORRECT (placed before closing head tag)\n";
        } else {
            echo "CSS position relative to </head>: INCORRECT\n";
        }
        
        if ($homeJsPos !== -1 && $homeJsPos < $bodyClosePos) {
            echo "JS position relative to </body>: CORRECT (placed before closing body tag)\n";
        } else {
            echo "JS position relative to </body>: INCORRECT\n";
        }
        
        echo "SUCCESS: All asset queue tests passed successfully!\n";
    } else {
        echo "FAILURE: Asset files were not rendered in the template.\n";
        exit(1);
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
