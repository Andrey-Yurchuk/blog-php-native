<?php

declare(strict_types=1);

namespace App\DTO\ArticleDTO;

/**
 * Используется для вывода полной информации на странице статьи
 */
final readonly class ArticleDetailsDTO
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $image,
        public string $title,
        public string $description,
        public string $body,
        public int $viewsCount,
        public string $publishedAt,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            slug: (string) $row['slug'],
            image: (string) $row['image'],
            title: (string) $row['title'],
            description: (string) $row['description'],
            body: (string) $row['body'],
            viewsCount: (int) $row['views_count'],
            publishedAt: (string) $row['published_at'],
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        );
    }
}
