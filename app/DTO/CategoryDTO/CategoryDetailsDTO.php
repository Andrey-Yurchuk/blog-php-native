<?php

declare(strict_types=1);

namespace App\DTO\CategoryDTO;

/**
 * Используется для данных категории на странице категории и в связанных списках
 */
final readonly class CategoryDetailsDTO
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $description,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            slug: (string) $row['slug'],
            title: (string) $row['title'],
            description: (string) $row['description'],
            createdAt: (string) $row['created_at'],
            updatedAt: (string) $row['updated_at'],
        );
    }
}
