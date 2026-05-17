<?php

declare(strict_types=1);

namespace App\DTO\CategoryDTO;

use App\DTO\PaginationDTO\PaginationDTO;

/**
 * Используется для блока категории со списком последних статей на главной странице
 */
final readonly class CategoryWithArticlesDTO
{
    public function __construct(
        public CategoryDetailsDTO $category,
        public array $articles,
        public int $totalArticles,
        private int $articlesPerPage,
    ) {
    }

    public function getListUrl(): string
    {
        $pagination = new PaginationDTO(1, $this->articlesPerPage, $this->totalArticles);

        return $pagination->buildPageUrl('/category/' . $this->category->slug, 1, null);
    }
}
