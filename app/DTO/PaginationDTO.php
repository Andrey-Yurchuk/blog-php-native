<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Используется для хранения параметров и расчетов пагинации
 */
final readonly class PaginationDTO
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $totalItems,
    ) {
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
