<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\HomePageService;
use App\View\SmartyRenderer;

final readonly class HomeController
{
    public function __construct(
        private HomePageService $homePageService,
        private SmartyRenderer $renderer,
    ) {
    }

    public function index(Request $request, array $params): Response
    {
        $pageData = $this->homePageService->getPageData();

        return Response::createHtml($this->renderer->renderTemplate(
            'pages/home.tpl',
            array_merge(
                [
                    'pageTitle' => 'Blog PHP Native',
                    'heading' => 'Blog PHP Native',
                    'description' => 'Latest articles grouped by category',
                ],
                $pageData->toTemplateVars(),
            ),
        ));
    }
}
