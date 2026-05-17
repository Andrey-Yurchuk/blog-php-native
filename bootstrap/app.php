<?php

declare(strict_types=1);

use App\Application;
use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Database\DatabaseConnection;
use App\Http\Router;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\HomePageRepository;
use App\Service\ArticlePageService;
use App\Service\CategoryPageService;
use App\Service\HomePageService;
use App\Support\Env;
use App\View\SmartyRenderer;

$rootPath = dirname(__DIR__);
$pdo = DatabaseConnection::create();
$categoryRepository = new CategoryRepository($pdo);
$articleRepository = new ArticleRepository($pdo);
$homePageRepository = new HomePageRepository($pdo, $categoryRepository, $articleRepository);
$categoryPageService = new CategoryPageService($categoryRepository, $articleRepository);
$renderer = SmartyRenderer::createDefault(
    $rootPath . '/resources/templates',
    $rootPath . '/storage/cache/smarty',
);

$homeController = new HomeController(
    new HomePageService($homePageRepository, $categoryPageService->getArticlesPerPage()),
    $renderer,
);
$categoryController = new CategoryController(
    $categoryPageService,
    $renderer,
);
$articleController = new ArticleController(
    new ArticlePageService($articleRepository, $categoryRepository),
    $renderer,
);

$router = new Router();
$routes = require $rootPath . '/config/routes.php';
$routes($router, $homeController, $categoryController, $articleController);

return new Application($router, $renderer, Env::getBool('APP_DEBUG'));
