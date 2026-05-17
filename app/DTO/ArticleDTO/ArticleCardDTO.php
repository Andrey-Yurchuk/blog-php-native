<?php

declare(strict_types=1);

namespace App\DTO\ArticleDTO;

use App\Validation\ImagePathValidator;

/**
 * Используется для кратких данных статьи в списках и превью без полного текста
 */
final readonly class ArticleCardDTO
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $image,
        public string $title,
        public string $description,
        public string $publishedAt,
        public int $viewsCount,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            slug: (string) $row['slug'],
            image: ImagePathValidator::normalize((string) $row['image']),
            title: (string) $row['title'],
            description: (string) $row['description'],
            publishedAt: (string) $row['published_at'],
            viewsCount: (int) $row['views_count'],
        );
    }
}
