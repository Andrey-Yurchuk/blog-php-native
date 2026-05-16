<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ArticleDTO\ArticleDetailsDTO;
use App\DTO\CategoryDTO\CategoryDetailsDTO;
use App\DTO\PageDTO\ArticlePageDataDTO;
use App\Exception\NotFoundException;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

final class ArticlePageService
{
    /** Лимит похожих статей на странице статьи */
    private const int RELATED_ARTICLES_LIMIT = 3;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * Возвращает данные для страницы статьи
     */
    public function getPageData(string $slug): ArticlePageDataDTO
    {
        $article = $this->articleRepository->findBySlug($slug);

        if ($article === null) {
            throw new NotFoundException();
        }

        $this->articleRepository->incrementViews($article->id);

        $categories = $this->categoryRepository->findByArticleId($article->id);
        $relatedArticles = $this->articleRepository->findRelated(
            $article->id,
            $this->extractCategoryIds($categories),
            self::RELATED_ARTICLES_LIMIT,
        );

        return new ArticlePageDataDTO(
            $this->withIncrementedViews($article),
            $categories,
            $relatedArticles,
        );
    }

    /**
     * Возвращает идентификаторы категорий для выборки похожих статей
     */
    private function extractCategoryIds(array $categories): array
    {
        return array_map(
            static fn (CategoryDetailsDTO $category): int => $category->id,
            $categories,
        );
    }

    /**
     * Возвращает статью с увеличенным на единицу счетчиком просмотров
     */
    private function withIncrementedViews(ArticleDetailsDTO $article): ArticleDetailsDTO
    {
        return new ArticleDetailsDTO(
            id: $article->id,
            slug: $article->slug,
            image: $article->image,
            title: $article->title,
            description: $article->description,
            body: $article->body,
            viewsCount: $article->viewsCount + 1,
            publishedAt: $article->publishedAt,
            createdAt: $article->createdAt,
            updatedAt: $article->updatedAt,
        );
    }
}
