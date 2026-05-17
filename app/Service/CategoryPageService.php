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
    private const int ARTICLES_PER_PAGE = 9;

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

        $sortState = $this->resolveSort($sort);
        $totalItems = $this->articleRepository->countByCategory($category->id);
        $pagination = new PaginationDTO(
            $this->normalizePage($page),
            self::ARTICLES_PER_PAGE,
            $totalItems,
        );

        if ($totalItems > 0 && $pagination->page > $pagination->getTotalPages()) {
            throw new NotFoundException();
        }

        $articles = $this->articleRepository->findPaginatedByCategory(
            $category->id,
            $sortState['filter'],
            $pagination,
        );

        return new CategoryPageDataDTO(
            $category,
            $articles,
            $sortState['filter']->sort,
            $sortState['sortQuery'],
        );
    }

    /**
     * Возвращает лимит статей на странице категории
     */
    public function getArticlesPerPage(): int
    {
        return self::ARTICLES_PER_PAGE;
    }

    /**
     * Возвращает сортировку для SQL и значение sort для URL
     */
    private function resolveSort(?string $sort): array
    {
        if ($sort === null || $sort === '') {
            return [
                'filter' => new CategoryArticleFilterDTO(CategoryArticleFilterDTO::SORT_PUBLISHED_AT),
                'sortQuery' => null,
            ];
        }

        return match ($sort) {
            CategoryArticleFilterDTO::SORT_VIEWS_COUNT => [
                'filter' => new CategoryArticleFilterDTO(CategoryArticleFilterDTO::SORT_VIEWS_COUNT),
                'sortQuery' => CategoryArticleFilterDTO::SORT_VIEWS_COUNT,
            ],
            CategoryArticleFilterDTO::SORT_PUBLISHED_AT => [
                'filter' => new CategoryArticleFilterDTO(CategoryArticleFilterDTO::SORT_PUBLISHED_AT),
                'sortQuery' => CategoryArticleFilterDTO::SORT_PUBLISHED_AT,
            ],
            default => [
                'filter' => new CategoryArticleFilterDTO(CategoryArticleFilterDTO::SORT_PUBLISHED_AT),
                'sortQuery' => null,
            ],
        };
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
