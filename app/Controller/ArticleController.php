<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\ArticlePageService;
use App\View\SmartyRenderer;

final readonly class ArticleController
{
    public function __construct(
        private ArticlePageService $articlePageService,
        private SmartyRenderer $renderer,
    ) {
    }

    public function show(Request $request, array $params): Response
    {
        $pageData = $this->articlePageService->getPageData((string) ($params['slug'] ?? ''));

        return Response::createHtml($this->renderer->renderTemplate(
            'pages/article.tpl',
            array_merge(
                [
                    'pageTitle' => $pageData->article->title,
                ],
                $pageData->toTemplateVars(),
            ),
        ));
    }
}
