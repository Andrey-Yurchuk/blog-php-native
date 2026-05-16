<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    public function __construct(
        private string $method,
        private string $path,
        private array $query,
    ) {
    }

    /**
     * Создает request из суперглобальных массивов
     */
    public static function createFromGlobals(): self
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);

        return new self(
            $method,
            is_string($path) && $path !== '' ? $path : '/',
            self::normalizeQuery($_GET),
        );
    }

    /**
     * Возвращает HTTP-метод запроса
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Возвращает путь запроса без query string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Возвращает значение query-параметра или значение по дефолту
     */
    public function getQuery(string $key, ?string $default = null): ?string
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Приводит query-параметры к строковым значениям
     */
    private static function normalizeQuery(array $query): array
    {
        $normalized = [];

        foreach ($query as $key => $value) {
            if (is_scalar($value)) {
                $normalized[(string) $key] = (string) $value;
            }
        }

        return $normalized;
    }
}
