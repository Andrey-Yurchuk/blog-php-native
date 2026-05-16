<?php

declare(strict_types=1);

namespace App\DTO\PageDTO;

use App\DTO\ArticleDTO\ArticleDetailsDTO;

/**
 * Используется для передачи данных страницы статьи из сервиса в представление
 */
final readonly class ArticlePageDataDTO
{
    public function __construct(
        public ArticleDetailsDTO $article,
        public array $categories,
        public array $relatedArticles,
    ) {
    }

    /**
     * Возвращает переменные для шаблона Smarty
     */
    public function toTemplateVars(): array
    {
        return [
            'article' => $this->article,
            'categories' => $this->categories,
            'relatedArticles' => $this->relatedArticles,
        ];
    }
}
