<?php

declare(strict_types=1);

namespace App\DTO\PageDTO;

/**
 * Используется для передачи данных главной страницы из сервиса в представление
 */
final readonly class HomePageDataDTO
{
    public function __construct(
        public array $categories,
    ) {
    }

    /**
     * Возвращает переменные для шаблона Smarty
     */
    public function toTemplateVars(): array
    {
        return [
            'categories' => $this->categories,
        ];
    }
}
