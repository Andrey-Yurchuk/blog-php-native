<?php

declare(strict_types=1);

use App\Http\Request;

$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoloadPath)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Application bootstrap failed';
    exit;
}

require $autoloadPath;

$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->handle(Request::fromGlobals())->send();
