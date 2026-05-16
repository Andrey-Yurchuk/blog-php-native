<?php

declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Http\Router;

return static function (
    Router $router,
    HomeController $homeController,
    CategoryController $categoryController,
    ArticleController $articleController,
): void {
    $router->addGet('/', [$homeController, 'index']);
    $router->addGet('/category/{slug}', [$categoryController, 'show']);
    $router->addGet('/article/{slug}', [$articleController, 'show']);
};
