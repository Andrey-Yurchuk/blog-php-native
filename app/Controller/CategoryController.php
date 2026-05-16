<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\CategoryPageService;
use App\View\SmartyRenderer;

final readonly class CategoryController
{
    public function __construct(
        private CategoryPageService $categoryPageService,
        private SmartyRenderer $renderer,
    ) {
    }

    public function show(Request $request, array $params): Response
    {
        $pageData = $this->categoryPageService->getPageData(
            (string) ($params['slug'] ?? ''),
            $request->getQuery('sort'),
            $request->getQuery('page'),
        );

        return Response::createHtml($this->renderer->renderTemplate(
            'pages/category.tpl',
            array_merge(
                [
                    'pageTitle' => $pageData->category->title,
                ],
                $pageData->toTemplateVars(),
            ),
        ));
    }
}
