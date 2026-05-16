<?php

declare(strict_types=1);

use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\View\SmartyRenderer;

return static function (Router $router, SmartyRenderer $renderer): void {
    $router->addGet('/', static function (Request $request, array $params) use ($renderer): Response {
        return Response::createHtml($renderer->renderTemplate('pages/home.tpl', [
            'pageTitle' => 'Blog PHP Native',
            'heading' => 'Blog PHP Native',
            'description' => 'Smarty rendering is ready',
        ]));
    });
};
