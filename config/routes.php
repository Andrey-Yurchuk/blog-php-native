<?php

declare(strict_types=1);

use App\Http\Request;
use App\Http\Response;
use App\Http\Router;

return static function (Router $router): void {
    $router->addGet('/', static function (Request $request, array $params): Response {
        return Response::createText('Blog PHP Native is ready');
    });
};
