<?php

declare(strict_types=1);

use App\Application;
use App\Http\Router;
use App\Support\Env;

$router = new Router();
$routes = require dirname(__DIR__) . '/config/routes.php';
$routes($router);

return new Application($router, Env::getBool('APP_DEBUG'));
