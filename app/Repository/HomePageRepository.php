<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\ArticleDTO\ArticleCardDTO;
use App\DTO\CategoryDTO\CategoryDetailsDTO;
use App\DTO\CategoryDTO\CategoryWithArticlesDTO;
use PDO;
use PDOStatement;

final class HomePageRepository
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly CategoryRepository $categoryRepository,
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    /**
     * Возвращает категории с последними статьями для главной страницы
     */
    public function findCategoriesWithLatestArticles(int $limitPerCategory, int $articlesPerPage): array
    {
        $categories = $this->categoryRepository->findCategoriesWithArticles();

        if ($categories === []) {
            return [];
        }

        $categoryIds = array_map(
            static fn (CategoryDetailsDTO $category): int => $category->id,
            $categories,
        );
        $articlesByCategoryId = $this->findLatestArticlesByCategoryIds($categoryIds, $limitPerCategory);
        $articleCountsByCategoryId = $this->articleRepository->countByCategoryIds($categoryIds);

        return $this->mergeCategoriesWithArticles(
            $categories,
            $articlesByCategoryId,
            $articleCountsByCategoryId,
            $articlesPerPage,
        );
    }

    /**
     * Возвращает последние статьи для списка категорий
     */
    private function findLatestArticlesByCategoryIds(array $categoryIds, int $limitPerCategory): array
    {
        $statement = $this->pdo->prepare($this->getLatestArticlesByCategoryIdsSql(count($categoryIds)));
        $this->bindLatestArticlesParameters($statement, $categoryIds, $limitPerCategory);
        $statement->execute();

        $articlesByCategoryId = [];

        while ($row = $statement->fetch()) {
            $categoryId = (int) $row['category_id'];
            $articlesByCategoryId[$categoryId][] = ArticleCardDTO::fromRow($row);
        }

        return $articlesByCategoryId;
    }

    /**
     * Собирает категории со списками статей для главной страницы
     */
    private function mergeCategoriesWithArticles(
        array $categories,
        array $articlesByCategoryId,
        array $articleCountsByCategoryId,
        int $articlesPerPage,
    ): array {
        $result = [];

        foreach ($categories as $category) {
            $result[] = new CategoryWithArticlesDTO(
                $category,
                $articlesByCategoryId[$category->id] ?? [],
                $articleCountsByCategoryId[$category->id] ?? 0,
                $articlesPerPage,
            );
        }

        return $result;
    }

    /**
     * Возвращает SQL для выборки последних статей по категориям
     */
    private function getLatestArticlesByCategoryIdsSql(int $categoryCount): string
    {
        $placeholders = implode(', ', array_fill(0, $categoryCount, '?'));

        return sprintf(
            'SELECT
                 ranked.category_id,
                 ranked.id,
                 ranked.slug,
                 ranked.image,
                 ranked.title,
                 ranked.description,
                 ranked.published_at,
                 ranked.views_count
             FROM (
                 SELECT
                     ac.category_id,
                     a.id,
                     a.slug,
                     a.image,
                     a.title,
                     a.description,
                     a.published_at,
                     a.views_count,
                     ROW_NUMBER() OVER (
                         PARTITION BY ac.category_id
                         ORDER BY a.published_at DESC, a.id DESC
                     ) AS article_rank
                 FROM article_category ac
                 INNER JOIN articles a ON a.id = ac.article_id
                 WHERE ac.category_id IN (%s)
             ) ranked
             WHERE ranked.article_rank <= ?
             ORDER BY ranked.category_id ASC, ranked.published_at DESC',
            $placeholders,
        );
    }

    /**
     * Привязывает параметры запроса последних статей по категориям
     */
    private function bindLatestArticlesParameters(
        PDOStatement $statement,
        array $categoryIds,
        int $limitPerCategory,
    ): void {
        $position = 1;

        foreach ($categoryIds as $categoryId) {
            $statement->bindValue($position, $categoryId, PDO::PARAM_INT);
            $position++;
        }

        $statement->bindValue($position, $limitPerCategory, PDO::PARAM_INT);
    }
}
