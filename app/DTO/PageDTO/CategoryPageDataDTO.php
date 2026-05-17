<?php

declare(strict_types=1);

namespace App\DTO\PageDTO;

use App\DTO\CategoryDTO\CategoryDetailsDTO;
use App\DTO\PaginationDTO\PaginatedResultDTO;

/**
 * Используется для передачи данных страницы категории из сервиса в представление
 */
final readonly class CategoryPageDataDTO
{
    public function __construct(
        public CategoryDetailsDTO $category,
        public PaginatedResultDTO $articles,
        public string $sort,
        public ?string $sortQuery,
    ) {
    }

    /**
     * Возвращает переменные для шаблона Smarty
     */
    public function toTemplateVars(): array
    {
        return [
            'category' => $this->category,
            'articles' => $this->articles,
            'sort' => $this->sort,
            'sortQuery' => $this->sortQuery,
        ];
    }
}
