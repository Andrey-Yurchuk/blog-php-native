<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Используется для передачи списка статей вместе с пагинацией
 */
final readonly class PaginatedResultDTO
{
    public function __construct(
        public array $items,
        public PaginationDTO $pagination,
    ) {
    }
}
