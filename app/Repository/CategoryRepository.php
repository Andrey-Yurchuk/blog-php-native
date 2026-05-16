<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CategoryDetailsDTO;
use PDO;
use PDOStatement;

final class CategoryRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Возвращает категорию по slug
     */
    public function findBySlug(string $slug): ?CategoryDetailsDTO
    {
        $statement = $this->pdo->prepare(
            'SELECT id, slug, title, description, created_at, updated_at
             FROM categories
             WHERE slug = :slug
             LIMIT 1',
        );
        $statement->execute(['slug' => $slug]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return CategoryDetailsDTO::fromRow($row);
    }

    /**
     * Возвращает категории, в которых есть статьи
     */
    public function findCategoriesWithArticles(): array
    {
        $statement = $this->pdo->query(
            'SELECT DISTINCT c.id, c.slug, c.title, c.description, c.created_at, c.updated_at
             FROM categories c
             INNER JOIN article_category ac ON ac.category_id = c.id
             ORDER BY c.title ASC',
        );

        return $this->fetchCategoryDetailsList($statement);
    }

    /**
     * Возвращает категории выбранной статьи
     */
    public function findByArticleId(int $articleId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT c.id, c.slug, c.title, c.description, c.created_at, c.updated_at
             FROM categories c
             INNER JOIN article_category ac ON ac.category_id = c.id
             WHERE ac.article_id = :article_id
             ORDER BY c.title ASC',
        );
        $statement->execute(['article_id' => $articleId]);

        return $this->fetchCategoryDetailsList($statement);
    }

    /**
     * Создает список данных категорий из строк результата SQL-запроса
     */
    private function fetchCategoryDetailsList(PDOStatement $statement): array
    {
        $categories = [];

        while ($row = $statement->fetch()) {
            $categories[] = CategoryDetailsDTO::fromRow($row);
        }

        return $categories;
    }
}
