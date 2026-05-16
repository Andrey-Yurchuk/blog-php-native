<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Используется для параметров сортировки статей внутри категории
 */
final readonly class CategoryArticleFilterDTO
{
    public const string SORT_PUBLISHED_AT = 'published_at';
    public const string SORT_VIEWS_COUNT = 'views_count';

    public function __construct(
        public string $sort,
    ) {
    }
}
