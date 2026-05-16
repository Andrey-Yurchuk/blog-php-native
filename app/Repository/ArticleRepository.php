<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\ArticleCardDTO;
use App\DTO\ArticleDetailsDTO;
use App\DTO\CategoryArticleFilterDTO;
use App\DTO\PaginatedResultDTO;
use App\DTO\PaginationDTO;
use PDO;
use PDOStatement;

final class ArticleRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Возвращает список статей категории с данными пагинации
     */
    public function findPaginatedByCategory(
        int $categoryId,
        CategoryArticleFilterDTO $filter,
        PaginationDTO $pagination,
    ): PaginatedResultDTO {
        $orderBy = $this->getCategoryArticleOrderBy($filter);
        $sql = sprintf(
            'SELECT a.id, a.slug, a.image, a.title, a.description, a.published_at, a.views_count
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :category_id
             ORDER BY %s DESC, a.id DESC
             LIMIT :limit OFFSET :offset',
            $orderBy,
        );

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $statement->bindValue('limit', $pagination->perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $pagination->getOffset(), PDO::PARAM_INT);
        $statement->execute();

        return new PaginatedResultDTO($this->fetchArticleCardList($statement), $pagination);
    }

    /**
     * Возвращает количество статей в категории
     */
    public function countByCategory(int $categoryId): int
    {
        $statement = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM article_category
             WHERE category_id = :category_id',
        );
        $statement->execute(['category_id' => $categoryId]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Возвращает полную статью по slug
     */
    public function findBySlug(string $slug): ?ArticleDetailsDTO
    {
        $statement = $this->pdo->prepare(
            'SELECT id, slug, image, title, description, body, views_count, published_at, created_at, updated_at
             FROM articles
             WHERE slug = :slug
             LIMIT 1',
        );
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return ArticleDetailsDTO::fromRow($row);
    }

    /**
     * Увеличивает счетчик просмотров статьи
     */
    public function incrementViews(int $articleId): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE articles
             SET views_count = views_count + 1
             WHERE id = :id',
        );
        $statement->execute(['id' => $articleId]);
    }

    /**
     * Возвращает похожие статьи по общим категориям
     */
    public function findRelated(int $articleId, array $categoryIds, int $limit): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $statement = $this->pdo->prepare($this->getRelatedArticlesSql(count($categoryIds)));
        $this->bindRelatedArticlesParameters($statement, $categoryIds, $articleId, $limit);
        $statement->execute();

        return $this->fetchArticleCardList($statement);
    }

    /**
     * Возвращает SQL для выборки похожих статей
     */
    private function getRelatedArticlesSql(int $categoryCount): string
    {
        $placeholders = implode(', ', array_fill(0, $categoryCount, '?'));

        return sprintf(
            'SELECT
                 a.id,
                 a.slug,
                 a.image,
                 a.title,
                 a.description,
                 a.published_at,
                 a.views_count,
                 COUNT(ac.category_id) AS related_score
             FROM articles a
             INNER JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id IN (%s)
               AND a.id != ?
             GROUP BY a.id, a.slug, a.image, a.title, a.description, a.published_at, a.views_count
             ORDER BY related_score DESC, a.published_at DESC, a.id DESC
             LIMIT ?',
            $placeholders,
        );
    }

    /**
     * Привязывает параметры запроса похожих статей
     */
    private function bindRelatedArticlesParameters(
        PDOStatement $statement,
        array $categoryIds,
        int $articleId,
        int $limit,
    ): void {
        $position = 1;

        foreach ($categoryIds as $categoryId) {
            $statement->bindValue($position, $categoryId, PDO::PARAM_INT);
            $position++;
        }

        $statement->bindValue($position, $articleId, PDO::PARAM_INT);
        $position++;
        $statement->bindValue($position, $limit, PDO::PARAM_INT);
    }

    /**
     * Создает список кратких данных статей из строк результата SQL-запроса
     */
    private function fetchArticleCardList(PDOStatement $statement): array
    {
        $articles = [];

        while ($row = $statement->fetch()) {
            $articles[] = ArticleCardDTO::fromRow($row);
        }

        return $articles;
    }

    /**
     * Возвращает поле для ORDER BY в списке статей категории
     */
    private function getCategoryArticleOrderBy(CategoryArticleFilterDTO $filter): string
    {
        return match ($filter->sort) {
            CategoryArticleFilterDTO::SORT_VIEWS_COUNT => 'a.views_count',
            default => 'a.published_at',
        };
    }
}
