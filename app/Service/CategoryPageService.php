<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CategoryDTO\CategoryArticleFilterDTO;
use App\DTO\PageDTO\CategoryPageDataDTO;
use App\DTO\PaginationDTO\PaginationDTO;
use App\Exception\NotFoundException;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

final class CategoryPageService
{
    /** Лимит статей на одной странице категории */
    private const int ARTICLES_PER_PAGE = 10;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    /**
     * Возвращает данные для страницы категории
     */
    public function getPageData(string $slug, ?string $sort, ?string $page): CategoryPageDataDTO
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category === null) {
            throw new NotFoundException();
        }

        $filter = $this->normalizeSort($sort);
        $pagination = new PaginationDTO(
            $this->normalizePage($page),
            self::ARTICLES_PER_PAGE,
            $this->articleRepository->countByCategory($category->id),
        );
        $articles = $this->articleRepository->findPaginatedByCategory(
            $category->id,
            $filter,
            $pagination,
        );

        return new CategoryPageDataDTO(
            $category,
            $articles,
            $filter->sort,
        );
    }

    /**
     * Нормализует параметр сортировки к допустимому значению
     */
    private function normalizeSort(?string $sort): CategoryArticleFilterDTO
    {
        return new CategoryArticleFilterDTO(
            match ($sort) {
                CategoryArticleFilterDTO::SORT_VIEWS_COUNT => CategoryArticleFilterDTO::SORT_VIEWS_COUNT,
                default => CategoryArticleFilterDTO::SORT_PUBLISHED_AT,
            },
        );
    }

    /**
     * Нормализует номер страницы к положительному целому
     */
    private function normalizePage(?string $page): int
    {
        if ($page === null || $page === '') {
            return 1;
        }

        $pageNumber = filter_var($page, FILTER_VALIDATE_INT);

        if ($pageNumber === false || $pageNumber < 1) {
            return 1;
        }

        return $pageNumber;
    }
}
