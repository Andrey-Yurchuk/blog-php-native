<?php

declare(strict_types=1);

namespace App\DTO\PaginationDTO;

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

    public function getTotalPages(): int
    {
        if ($this->totalItems === 0) {
            return 1;
        }

        return (int) ceil($this->totalItems / $this->perPage);
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->getTotalPages();
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->page - 1);
    }

    public function getNextPage(): int
    {
        return min($this->getTotalPages(), $this->page + 1);
    }

    public function buildPageUrl(string $baseUrl, int $targetPage, ?string $sortQuery = null): string
    {
        $query = [];

        if ($sortQuery !== null && $sortQuery !== '') {
            $query['sort'] = $sortQuery;
        }

        if ($this->getTotalPages() > 1) {
            $query['page'] = (string) $targetPage;
        }

        if ($query === []) {
            return $baseUrl;
        }

        return $baseUrl . '?' . http_build_query($query);
    }
}
