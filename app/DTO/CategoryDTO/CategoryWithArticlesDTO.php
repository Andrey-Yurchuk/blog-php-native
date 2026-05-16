<?php

declare(strict_types=1);

namespace App\DTO\CategoryDTO;

/**
 * Используется для блока категории со списком последних статей на главной странице
 */
final readonly class CategoryWithArticlesDTO
{
    public function __construct(
        public CategoryDetailsDTO $category,
        public array $articles,
    ) {
    }
}
