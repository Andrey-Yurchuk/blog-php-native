<?php

declare(strict_types=1);

use App\Application;
use App\Http\Router;
use App\Support\Env;
use App\View\SmartyRenderer;

$rootPath = dirname(__DIR__);
$router = new Router();
$renderer = SmartyRenderer::createDefault(
    $rootPath . '/resources/templates',
    $rootPath . '/storage/cache/smarty',
);
$routes = require $rootPath . '/config/routes.php';
$routes($router, $renderer);

return new Application($router, Env::getBool('APP_DEBUG'));
